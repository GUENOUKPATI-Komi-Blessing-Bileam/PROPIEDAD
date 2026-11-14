@extends('layouts.app')

@section('title', 'Biens')
@section('section', 'Biens')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">Registre patrimonial</p><h1>Biens</h1><p class="page-lead">Consultez et gérez les biens enregistrés dans le patrimoine.</p></div>
    <a class="button button-primary" href="{{ route('assets.create') }}"><span aria-hidden="true">＋</span> Enregistrer un bien</a>
</div>

<form class="filter-panel" method="get" action="{{ route('assets.index') }}" data-filter-form>
    <label class="field field-search"><span>Recherche</span><input type="search" name="q" placeholder="Code, désignation, catégorie…" value="{{ request('q') }}"></label>
    <label class="field"><span>Catégorie</span><select name="category"><option value="">Toutes les catégories</option><option>Informatique</option><option>Véhicules</option><option>Mobilier</option></select></label>
    <label class="field"><span>État</span><select name="state"><option value="">Tous les états</option><option>Bon état</option><option>À réparer</option><option>Hors service</option></select></label>
    <label class="field"><span>Service</span><select name="service"><option value="">Tous</option><option>Direction des finances</option><option>Direction du patrimoine</option></select></label>
    <button class="button button-soft" type="submit">Filtrer les biens</button>
</form>

<section class="panel registry-panel">
    <div class="panel-heading registry-heading"><div><h2>Registre des biens</h2><p data-result-count>2 486 résultats</p></div><button class="text-button" type="button" data-export>Exporter CSV <span aria-hidden="true">↓</span></button></div>
    <div class="table-scroll"><table class="data-table asset-table"><thead><tr><th>Code</th><th>Désignation</th><th>Catégorie</th><th>Service / site</th><th>État</th><th><span class="sr-only">Action</span></th></tr></thead><tbody>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0482') }}">INV-2026-0482</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0482') }}">Ordinateur portable Dell Latitude</a></td><td>Informatique</td><td><span>Direction des finances</span><small>Bâtiment A · Bureau 204</small></td><td><span class="status status-ok">Bon état</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0482') }}" aria-label="Consulter INV-2026-0482">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0479') }}">INV-2026-0479</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0479') }}">Véhicule de service Toyota Hilux</a></td><td>Véhicules</td><td><span>Direction du patrimoine</span><small>Parc automobile · Place 12</small></td><td><span class="status status-warn">À réparer</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0479') }}" aria-label="Consulter INV-2026-0479">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0461') }}">INV-2026-0461</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0461') }}">Bureau métallique 4 tiroirs</a></td><td>Mobilier</td><td><span>Direction des ressources</span><small>Bâtiment B · Bureau 108</small></td><td><span class="status status-ok">Bon état</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0461') }}" aria-label="Consulter INV-2026-0461">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0458') }}">INV-2026-0458</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0458') }}">Imprimante réseau HP LaserJet</a></td><td>Informatique</td><td><span>Direction des finances</span><small>Bâtiment A · Bureau 210</small></td><td><span class="status status-danger">Hors service</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0458') }}" aria-label="Consulter INV-2026-0458">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0442') }}">INV-2026-0442</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0442') }}">Climatiseur mural 18 000 BTU</a></td><td>Équipement</td><td><span>Direction des ressources</span><small>Annexe · Salle 03</small></td><td><span class="status status-ok">Bon état</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0442') }}" aria-label="Consulter INV-2026-0442">›</a></td></tr>
        <tr><td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0428') }}">INV-2026-0428</a></td><td><a class="table-primary" href="{{ route('assets.show', 'INV-2026-0428') }}">Armoire de rangement sécurisée</a></td><td>Mobilier</td><td><span>Direction du patrimoine</span><small>Bâtiment C · Bureau 12</small></td><td><span class="status status-ok">Bon état</span></td><td><a class="row-arrow" href="{{ route('assets.show', 'INV-2026-0428') }}" aria-label="Consulter INV-2026-0428">›</a></td></tr>
    </tbody></table></div>
    <div class="pagination-row"><span>Affichage de 1 à 6 sur 2 486 biens</span><div><button type="button" aria-label="Page précédente" disabled>‹</button><button type="button" class="is-current" aria-current="page">1</button><button type="button" aria-label="Page suivante">›</button></div></div>
</section>
@endsection