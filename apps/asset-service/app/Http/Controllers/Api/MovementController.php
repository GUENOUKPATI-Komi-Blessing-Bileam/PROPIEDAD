<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MovementController extends AuthenticatedController
{
    public function index(Request $request): JsonResponse
    {
        $userId = $this->authenticatedUserId($request);
        if ($userId instanceof JsonResponse) {
            return $userId;
        }

        $validated = $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'status' => ['sometimes', Rule::in(['pending', 'approved', 'rejected'])],
            'asset_id' => ['sometimes', 'uuid', 'exists:biens,id'],
        ]);

        $query = DB::table('mouvements')
            ->join('biens', 'biens.id', '=', 'mouvements.bien_id')
            ->select('mouvements.*', 'biens.code_inventaire as asset_code', 'biens.libelle as asset_label');
        if (isset($validated['status'])) {
            $query->where('statut', $validated['status']);
        }
        if (isset($validated['asset_id'])) {
            $query->where('bien_id', $validated['asset_id']);
        }

        $page = $query
            ->orderByDesc('created_at')
            ->paginate($validated['per_page'] ?? 20);
        $page->getCollection()->transform(fn (object $movement): array => $this->movementPayload($movement));

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
            'asset_id' => ['required', 'uuid', 'exists:biens,id'],
            'type' => ['required', Rule::in(['transfer', 'assignment', 'return', 'disposal'])],
            'destination_service_id' => ['nullable', 'uuid', Rule::exists('service_administratifs', 'id')->where('actif', true)],
            'destination_site_id' => ['nullable', 'uuid', Rule::exists('sites', 'id')->where('actif', true)],
            'effective_at' => ['required', 'date'],
            'reason' => ['required', 'string', 'min:1', 'max:1000'],
        ]);

        $movement = DB::transaction(function () use ($validated, $userId): array {
            $asset = DB::table('biens')->where('id', $validated['asset_id'])->first();
            abort_if($asset === null, 404);

            $id = (string) Str::uuid();
            $now = now();
            $effectiveAt = Carbon::parse($validated['effective_at'])->utc();
            $payload = [
                'id' => $id,
                'asset_id' => $asset->id,
                'asset_code' => $asset->code_inventaire,
                'asset_label' => $asset->libelle,
                'type' => $validated['type'],
                'status' => 'pending',
                'origin_service_id' => $asset->service_responsable_id,
                'origin_site_id' => $asset->site_id,
                'destination_service_id' => $validated['destination_service_id'] ?? null,
                'destination_site_id' => $validated['destination_site_id'] ?? null,
                'effective_at' => $effectiveAt->toIso8601String(),
                'reason' => $validated['reason'],
                'requested_by' => $userId,
                'decided_by' => null,
                'created_at' => $now->toISOString(),
            ];

            DB::table('mouvements')->insert([
                'id' => $id,
                'type' => $payload['type'],
                'statut' => $payload['status'],
                'date_effet' => $effectiveAt->format('Y-m-d H:i:s'),
                'motif' => $payload['reason'],
                'bien_id' => $payload['asset_id'],
                'service_origine_id' => $payload['origin_service_id'],
                'site_origine_id' => $payload['origin_site_id'],
                'service_destination_id' => $payload['destination_service_id'],
                'site_destination_id' => $payload['destination_site_id'],
                'soumis_par' => $userId,
                'valide_par' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('outbox_events')->insert([
                'event_id' => (string) Str::uuid(),
                'type_evenement' => 'patrimoine.mouvement.soumis',
                'aggregate_type' => 'movement',
                'aggregate_id' => $id,
                'payload' => json_encode($payload, JSON_THROW_ON_ERROR),
                'cree_le' => $now,
                'tentatives' => 0,
            ]);

            return $payload;
        });

        return response()->json($movement, 201);
    }

    private function movementPayload(object $movement): array
    {
        return [
            'id' => $movement->id,
            'asset_id' => $movement->bien_id,
            'asset_code' => $movement->asset_code,
            'asset_label' => $movement->asset_label,
            'type' => $movement->type,
            'status' => $movement->statut,
            'origin_service_id' => $movement->service_origine_id,
            'origin_site_id' => $movement->site_origine_id,
            'destination_service_id' => $movement->service_destination_id,
            'destination_site_id' => $movement->site_destination_id,
            'effective_at' => Carbon::parse($movement->date_effet)->utc()->toIso8601String(),
            'reason' => $movement->motif,
            'requested_by' => $movement->soumis_par,
            'decided_by' => $movement->valide_par,
            'created_at' => Carbon::parse($movement->created_at)->toIso8601String(),
        ];
    }
}
