@extends('layouts.app')

@section('title', 'Fiche du bien')
@section('section', 'Biens / INV-2026-0482')

@section('content')
<a class="back-link" href="{{ route('assets.index') }}"><span aria-hidden="true">‹</span> Retour à la liste des biens</a>
<div class="page-heading detail-heading"><div><p class="eyebrow">Fiche du bien</p><h1>{{ $asset->libelle }}</h1><p class="page-lead">Référence <span class="table-code">{{ $asset->code_inventaire }}</span></p><span class="status {{ $asset->statut === 'active' ? 'status-ok' : 'status-warn' }}">{{ $asset->statut === 'active' ? 'Actif' : $asset->statut }}</span></div><div class="heading-actions"><a class="button button-secondary" href="{{ route('assets.create') }}">Modifier</a><a class="button button-primary" href="{{ route('movements.create') }}"><span aria-hidden="true">＋</span> Mouvement</a></div></div>

<div class="detail-grid">
    <section class="panel details-panel">
        <div class="panel-heading">
            <div>
                <h2>Informations du bien</h2>
                <p>Données d’identification et d’affectation</p>
            </div>
        </div>
        <dl class="detail-list">
            <div>
                <dt>Catégorie</dt>
                <dd>{{ $asset->categorie }}</dd>
            </div>
            <div>
                <dt>État physique</dt>
                <dd>{{ ['good' => 'Bon état', 'fair' => 'À réparer', 'poor' => 'Mauvais état', 'unusable' => 'Hors service'][$asset->etat] ?? $asset->etat }}</dd>
            </div>
            <div>
                <dt>Service responsable</dt>
                <dd>{{ $asset->service }}</dd>
            </div>
            <div>
                <dt>Emplacement</dt>
                <dd>{{ $asset->site }}</dd>
            </div>
            <div>
                <dt>Date d’acquisition</dt>
                <dd>{{ $asset->date_acquisition ? \Illuminate\Support\Carbon::parse($asset->date_acquisition)->format('d/m/Y') : 'Non renseignée' }}</dd>
            </div>
            <div>
                <dt>Valeur déclarée</dt>
                <dd>{{ $asset->valeur_acquisition === null ? 'À renseigner' : number_format((float) $asset->valeur_acquisition, 2, ',', ' ').' '.($asset->devise ?? '') }}</dd>
            </div>
            <div class="detail-wide">
                <dt>Description</dt>
                <dd>{{ $asset->description ?: 'Aucune description.' }}</dd>
            </div>
            <div class="detail-wide">
                <dt>Enregistré par</dt>
                <dd>{{ \Illuminate\Support\Carbon::parse($asset->created_at)->format('d/m/Y') }}</dd>
            </div>
        </dl>
    </section>
    <aside class="detail-aside">
        <section class="panel label-panel">
            <div class="panel-heading">
                <div>
                    <h2>Étiquette d’inventaire</h2>
                    <p>Code d’identification</p>
                </div>
            </div>
            <div class="label-content">
                <div class="qr-placeholder" aria-label="Aperçu du code QR">
                    <i></i>
                    <i></i>
                    <i></i>
                    <b></b>
                </div>
                <div>
                    <span class="eyebrow">Code d’inventaire</span>
                    <strong class="table-code">{{ $asset->code_inventaire }}</strong>
                    <small>Code d’identification</small>
                </div>
            </div>
            <button class="text-button" type="button" data-print>Imprimer l’étiquette <span aria-hidden="true">↗</span></button>
        </section>
        <section class="panel shortcuts-panel">
            <div class="panel-heading">
                <div><h2>Raccourcis</h2></div>
            </div>
            <a class="shortcut-link" href="{{ route('movements.create') }}">⇄ &nbsp; Soumettre un transfert <span>›</span></a>
            <a class="shortcut-link" href="{{ route('inventory.show') }}">▣ &nbsp; Vérifier en inventaire <span>›</span></a>
        </section>
    </aside>
</div>
<section class="panel activity-panel detail-history">
    <div class="panel-heading">
        <div><h2>Historique du bien</h2><p>Événements récents liés à cette fiche</p></div>
    </div>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Événement</th>
                    <th>Auteur</th>
                    <th>Détail</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ \Illuminate\Support\Carbon::parse($movement->created_at)->format('d/m/Y') }}</td>
                        <td>{{ $movement->type }}</td>
                        <td>—</td>
                        <td>{{ $movement->service_destination ?? '—' }}@if ($movement->site_destination) · {{ $movement->site_destination }}@endif ({{ $movement->statut }})</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Aucun mouvement enregistré pour ce bien.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>

@endsection