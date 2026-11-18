<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    private const WEB_ACTOR_ID = '00000000-0000-4000-8000-000000000001';

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'uuid', 'exists:categories,id'],
            'state' => ['nullable', Rule::in(['good', 'fair', 'poor', 'unusable'])],
            'service' => ['nullable', 'uuid', 'exists:service_administratifs,id'],
        ]);

        $query = DB::table('biens')
            ->join('categories', 'categories.id', '=', 'biens.categorie_id')
            ->join('service_administratifs', 'service_administratifs.id', '=', 'biens.service_responsable_id')
            ->join('sites', 'sites.id', '=', 'biens.site_id')
            ->select([
                'biens.id',
                'biens.code_inventaire',
                'biens.libelle',
                'biens.etat',
                'categories.libelle as categorie',
                'service_administratifs.libelle as service',
                'sites.libelle as site',
            ]);

        if ($search = $filters['q'] ?? null) {
            $query->where(function ($query) use ($search): void {
                $query->where('biens.code_inventaire', 'like', "%{$search}%")
                    ->orWhere('biens.libelle', 'like', "%{$search}%")
                    ->orWhere('categories.libelle', 'like', "%{$search}%");
            });
        }

        $assets = $query
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('biens.categorie_id', $category))
            ->when($filters['state'] ?? null, fn ($query, string $state) => $query->where('biens.etat', $state))
            ->when($filters['service'] ?? null, fn ($query, string $service) => $query->where('biens.service_responsable_id', $service))
            ->orderByDesc('biens.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('pages.assets.index', [
            'assets' => $assets,
            'categories' => DB::table('categories')->orderBy('libelle')->get(['id', 'libelle']),
            'services' => DB::table('service_administratifs')->orderBy('libelle')->get(['id', 'libelle']),
        ]);
    }

    public function create(): View
    {
        return view('pages.assets.create', [
            'categories' => DB::table('categories')->where('actif', true)->orderBy('libelle')->get(['id', 'libelle']),
            'services' => DB::table('service_administratifs')->where('actif', true)->orderBy('libelle')->get(['id', 'libelle']),
            'sites' => DB::table('sites')->where('actif', true)->orderBy('libelle')->get(['id', 'libelle']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label' => ['required', 'string', 'min:1', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'uuid', Rule::exists('categories', 'id')->where('actif', true)],
            'service' => ['required', 'string', 'max:255', Rule::exists('service_administratifs', 'libelle')->where('actif', true)],
            'site' => ['required', 'string', 'max:255', Rule::exists('sites', 'libelle')->where('actif', true)],
            'condition' => ['required', Rule::in(['good', 'fair', 'poor', 'unusable'])],
            'acquisition_date' => ['nullable', 'date_format:Y-m-d'],
            'acquisition_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'regex:/^[A-Z]{3}$/'],
        ]);

        $validated['service_id'] = DB::table('service_administratifs')
            ->where('libelle', $validated['service'])
            ->where('actif', true)
            ->value('id');
        $validated['site_id'] = DB::table('sites')
            ->where('libelle', $validated['site'])
            ->where('actif', true)
            ->value('id');

        $inventoryCode = DB::transaction(function () use ($validated): string {
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
                'cree_par' => self::WEB_ACTOR_ID,
                'version' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $payload = [
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
                'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
                'cree_le' => $now,
                'tentatives' => 0,
            ]);

            return $inventoryCode;
        });

        return redirect()
            ->route('assets.index')
            ->with('status', "Le bien {$inventoryCode} a été enregistré.");
    }

    public function show(string $asset): View
    {
        $asset = DB::table('biens')
            ->join('categories', 'categories.id', '=', 'biens.categorie_id')
            ->join('service_administratifs', 'service_administratifs.id', '=', 'biens.service_responsable_id')
            ->join('sites', 'sites.id', '=', 'biens.site_id')
            ->where('biens.code_inventaire', $asset)
            ->select([
                'biens.*',
                'categories.libelle as categorie',
                'service_administratifs.libelle as service',
                'sites.libelle as site',
            ])
            ->first();

        abort_if($asset === null, 404);

        $movements = DB::table('mouvements')
            ->leftJoin('service_administratifs', 'service_administratifs.id', '=', 'mouvements.service_destination_id')
            ->leftJoin('sites', 'sites.id', '=', 'mouvements.site_destination_id')
            ->where('mouvements.bien_id', $asset->id)
            ->orderByDesc('mouvements.created_at')
            ->get([
                'mouvements.type',
                'mouvements.statut',
                'mouvements.created_at',
                'service_administratifs.libelle as service_destination',
                'sites.libelle as site_destination',
            ]);

        return view('pages.assets.show', ['asset' => $asset, 'movements' => $movements]);
    }

    private function nextInventoryCode(): string
    {
        do {
            $code = 'INV-'.now()->format('Y').'-'.Str::upper(Str::random(8));
        } while (DB::table('biens')->where('code_inventaire', $code)->exists());

        return $code;
    }
}
