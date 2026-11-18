<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MovementController extends Controller
{
    private const WEB_ACTOR_ID = '00000000-0000-4000-8000-000000000001';

    public function index(): View
    {
        $movements = DB::table('mouvements')
            ->join('biens', 'biens.id', '=', 'mouvements.bien_id')
            ->leftJoin('service_administratifs', 'service_administratifs.id', '=', 'mouvements.service_destination_id')
            ->leftJoin('sites', 'sites.id', '=', 'mouvements.site_destination_id')
            ->select([
                'mouvements.id',
                'mouvements.type',
                'mouvements.statut',
                'mouvements.date_effet',
                'biens.code_inventaire',
                'biens.libelle',
                'service_administratifs.libelle as service_destination',
                'sites.libelle as site_destination',
            ])
            ->orderByDesc('mouvements.created_at')
            ->paginate(20);

        return view('pages.movements.index', ['movements' => $movements]);
    }

    public function create(): View
    {
        $assets = DB::table('biens')
            ->join('service_administratifs', 'service_administratifs.id', '=', 'biens.service_responsable_id')
            ->join('sites', 'sites.id', '=', 'biens.site_id')
            ->select([
                'biens.id',
                'biens.code_inventaire',
                'biens.libelle',
                'service_administratifs.libelle as service',
                'sites.libelle as site',
            ])
            ->orderBy('biens.code_inventaire')
            ->get();

        return view('pages.movements.create', [
            'assets' => $assets,
            'services' => DB::table('service_administratifs')->where('actif', true)->orderBy('libelle')->get(['id', 'libelle']),
            'sites' => DB::table('sites')->where('actif', true)->orderBy('libelle')->get(['id', 'libelle']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bien_id' => ['required', 'uuid', 'exists:biens,id'],
            'type' => ['required', Rule::in(['Transfert', 'Sortie temporaire', 'Retour'])],
            'date_effet' => ['required', 'date_format:Y-m-d'],
            'service_destination_id' => ['required', 'uuid', Rule::exists('service_administratifs', 'id')->where('actif', true)],
            'site_destination_id' => ['required', 'uuid', Rule::exists('sites', 'id')->where('actif', true)],
            'destinataire' => ['nullable', 'string', 'max:255'],
            'motif' => ['required', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($validated): void {
            $asset = DB::table('biens')->where('id', $validated['bien_id'])->first();
            abort_if($asset === null, 404);
            $now = now();

            DB::table('mouvements')->insert([
                'id' => (string) Str::uuid(),
                'type' => $validated['type'],
                'statut' => 'pending',
                'date_effet' => $validated['date_effet'].' 00:00:00',
                'motif' => $validated['motif'],
                'destinataire' => $validated['destinataire'] ?? null,
                'bien_id' => $asset->id,
                'service_origine_id' => $asset->service_responsable_id,
                'site_origine_id' => $asset->site_id,
                'service_destination_id' => $validated['service_destination_id'],
                'site_destination_id' => $validated['site_destination_id'],
                'soumis_par' => self::WEB_ACTOR_ID,
                'valide_par' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return redirect()
            ->route('movements.index')
            ->with('status', 'La demande de mouvement a été soumise.');
    }
}
