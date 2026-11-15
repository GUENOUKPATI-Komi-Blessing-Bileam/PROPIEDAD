<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function references(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        return response()->json([
            'categories' => DB::table('categories')
                ->where('actif', true)
                ->orderBy('libelle')
                ->get(['id', 'libelle as label']),
            'services' => DB::table('service_administratifs')
                ->where('actif', true)
                ->orderBy('libelle')
                ->get(['id', 'libelle as label']),
            'sites' => DB::table('sites')
                ->where('actif', true)
                ->orderBy('libelle')
                ->get(['id', 'libelle as label']),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'q' => ['sometimes', 'string', 'max:200'],
        ]);

        $query = DB::table('biens');

        if (! empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($query) use ($search): void {
                $query->where('code_inventaire', 'like', "%{$search}%")
                    ->orWhere('libelle', 'like', "%{$search}%");
            });
        }

        $page = $query->orderByDesc('created_at')->paginate($validated['per_page'] ?? 20);
        $page->getCollection()->transform(fn (object $asset): array => $this->assetPayload($asset));

        return response()->json([
            'data' => $page->items(),
            'meta' => [
                'page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        $validated = $request->validate([
            'label' => ['required', 'string', 'min:1', 'max:200'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'uuid', 'exists:categories,id,actif,1'],
            'service_id' => ['required', 'uuid', 'exists:service_administratifs,id,actif,1'],
            'site_id' => ['required', 'uuid', 'exists:sites,id,actif,1'],
            'condition' => ['required', 'in:good,fair,poor,unusable'],
            'acquisition_date' => ['nullable', 'date_format:Y-m-d'],
            'acquisition_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'regex:/^[A-Z]{3}$/'],
        ]);

        $asset = DB::transaction(function () use ($validated, $userId): array {
            $id = (string) Str::uuid();
            $inventoryCode = $this->nextInventoryCode();
            $now = now();

            DB::table('biens')->insert([
                'id' => $id,
                'code_inventaire' => $inventoryCode,
                'libelle' => $validated['label'],
                'description' => $validated['description'] ?? null,
                'date_acquisition' => $validated['acquisition_date'] ?? null,
                'valeur_acquisition' => $validated['acquisition_value'] ?? null,
                'devise' => $validated['currency'] ?? null,
                'etat' => $validated['condition'],
                'statut' => 'active',
                'categorie_id' => $validated['category_id'],
                'service_responsable_id' => $validated['service_id'],
                'site_id' => $validated['site_id'],
                'cree_par' => $userId,
                'version' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $asset = [
                'id' => $id,
                'inventory_code' => $inventoryCode,
                'label' => $validated['label'],
                'description' => $validated['description'] ?? null,
                'category_id' => $validated['category_id'],
                'service_id' => $validated['service_id'],
                'site_id' => $validated['site_id'],
                'condition' => $validated['condition'],
                'acquisition_date' => $validated['acquisition_date'] ?? null,
                'acquisition_value' => isset($validated['acquisition_value'])
                    ? (float) $validated['acquisition_value']
                    : null,
                'currency' => $validated['currency'] ?? null,
                'status' => 'active',
                'created_at' => $now->toISOString(),
                'updated_at' => $now->toISOString(),
            ];

            DB::table('outbox_events')->insert([
                'event_id' => (string) Str::uuid(),
                'type_evenement' => 'patrimoine.bien.cree',
                'aggregate_type' => 'asset',
                'aggregate_id' => $id,
                'payload' => json_encode($asset, JSON_THROW_ON_ERROR),
                'cree_le' => $now,
                'tentatives' => 0,
            ]);

            return $asset;
        });

        return response()->json($asset, 201);
    }

    public function show(Request $request, string $assetId): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        $asset = DB::table('biens')->where('id', $assetId)->first();
        abort_if($asset === null, 404);

        return response()->json($this->assetPayload($asset));
    }

    private function authenticatedUserId(Request $request): string|JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return $this->problem(401, 'Authentification requise', 'Un jeton valide est requis pour accéder aux biens.');
        }

        try {
            $response = Http::timeout(5)
                ->withToken($token)
                ->acceptJson()
                ->get(rtrim(config('asset.auth_url'), '/').'/api/v1/auth/me');
        } catch (ConnectionException) {
            return $this->problem(503, 'Service indisponible', 'Le service d’authentification est temporairement indisponible.');
        }

        if ($response->unauthorized()) {
            return $this->problem(401, 'Authentification requise', 'Le jeton fourni est invalide ou expiré.');
        }

        $userId = $response->json('id');
        if (! $response->successful() || ! is_string($userId) || ! Str::isUuid($userId)) {
            return $this->problem(503, 'Service indisponible', 'Le service d’authentification n’a pas fourni un profil valide.');
        }

        return $userId;
    }

    private function nextInventoryCode(): string
    {
        do {
            $code = 'INV-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (DB::table('biens')->where('code_inventaire', $code)->exists());

        return $code;
    }

    private function assetPayload(object $asset): array
    {
        return [
            'id' => $asset->id,
            'inventory_code' => $asset->code_inventaire,
            'label' => $asset->libelle,
            'description' => $asset->description,
            'category_id' => $asset->categorie_id,
            'service_id' => $asset->service_responsable_id,
            'site_id' => $asset->site_id,
            'condition' => $asset->etat,
            'acquisition_date' => $asset->date_acquisition,
            'acquisition_value' => $asset->valeur_acquisition === null
                ? null
                : (float) $asset->valeur_acquisition,
            'currency' => $asset->devise,
            'status' => $asset->statut,
            'created_at' => $asset->created_at,
            'updated_at' => $asset->updated_at,
        ];
    }

    private function problem(int $status, string $title, string $detail): JsonResponse
    {
        return response()->json([
            'type' => 'about:blank',
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
            'request_id' => request()->header('X-Request-Id', (string) Str::uuid()),
        ], $status, ['Content-Type' => 'application/problem+json']);
    }
}
