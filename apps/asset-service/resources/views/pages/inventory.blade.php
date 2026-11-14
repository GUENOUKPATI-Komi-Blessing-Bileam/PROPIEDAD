@extends('layouts.app')

@section('title', 'Campagne d’inventaire 2026')
@section('section', 'Inventaires / Campagne 2026')

@section('content')
<div class="page-heading"><div><p class="eyebrow">Inventaire périodique</p><h1>Campagne d’inventaire 2026</h1><p class="page-lead">Direction des finances · Bâtiment principal · 1er au 30 novembre 2026</p></div><button class="button button-secondary" type="button" data-export>Exporter le suivi <span aria-hidden="true">↓</span></button></div>

<section class="panel progress-panel"><div class="panel-heading"><div><h2>Progression de la campagne</h2><p>Avancement des biens attendus</p></div></div><div class="progress-summary"><strong>68 %</strong><span>des biens attendus ont été vérifiés</span></div><div class="progress-track" role="progressbar" aria-label="Progression de la campagne" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"><span></span></div><div class="progress-legend"><span>1 694 vérifiés</span><span>785 à vérifier</span><span class="note-danger">7 écarts à examiner</span></div></section>

<form class="scan-panel" method="get" action="{{ route('inventory.show') }}" data-inventory-search><div><h2>Vérifier un bien</h2><p>Scannez son étiquette ou saisissez son code d’inventaire.</p></div><label class="sr-only" for="inventory-code">Code d’inventaire</label><input id="inventory-code" name="code" placeholder="INV-2026-0000" value="{{ request('code') }}"><button class="button button-primary" type="submit"><span aria-hidden="true">⌕</span> Rechercher</button></form>

<section class="panel registry-panel inventory-results"><div class="panel-heading registry-heading"><div><h2>Résultats de l’inventaire</h2><p>Biens inclus dans le périmètre de la campagne</p></div><label class="sr-only" for="inventory-filter">Filtrer les résultats</label><select class="compact-select" id="inventory-filter" data-inventory-filter><option>Tous les résultats</option><option>Présent</option><option>État à corriger</option><option>Absent</option></select></div>
    <div class="table-scroll"><table class="data-table inventory-table"><thead><tr><th>Code / bien</th><th>Emplacement attendu</th><th>Résultat</th><th>Observé par</th><th><span class="sr-only">Action</span></th></tr></thead><tbody>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0482') }}">INV-2026-0482</a><small>Ordinateur Dell Latitude</small></td><td>Bât. A · Bureau 204</td><td><span class="status status-ok">Présent</span></td><td>S. Traoré</td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0482') }}" aria-label="Consulter le bien">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0479') }}">INV-2026-0479</a><small>Véhicule Toyota Hilux</small></td><td>Parc automobile · Place 12</td><td><span class="status status-warn">État à corriger</span></td><td>S. Traoré</td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0479') }}" aria-label="Consulter le bien">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0461') }}">INV-2026-0461</a><small>Bureau métallique</small></td><td>Bât. B · Bureau 108</td><td><span class="status status-ok">Présent</span></td><td>A. Koné</td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0461') }}" aria-label="Consulter le bien">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0458') }}">INV-2026-0458</a><small>Imprimante HP LaserJet</small></td><td>Bât. A · Bureau 210</td><td><span class="status status-danger">Absent</span></td><td>S. Traoré</td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0458') }}" aria-label="Consulter le bien">›</a></td></tr>
    </tbody></table></div><p class="table-footnote">Les écarts sont soumis au gestionnaire pour examen avant clôture.</p>
</section>
@endsection