@extends('layouts.app')

@section('title', 'Biens')
@section('section', 'Biens')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">Registre patrimonial</p><h1>Biens</h1><p class="page-lead">Consultez et gérez les biens enregistrés dans le patrimoine.</p></div>
    <a class="button button-primary" href="{{ route('assets.create') }}"><span aria-hidden="true">＋</span> Enregistrer un bien</a>
</div>

@if (session('status'))
    <p class="submission-success" role="status">{{ session('status') }}</p>
@endif

<form class="filter-panel" method="get" action="{{ route('assets.index') }}" data-filter-form>
    <label class="field field-search"><span>Recherche</span><input type="search" name="q" placeholder="Code, désignation, catégorie…" value="{{ request('q') }}"></label>
    <label class="field"><span>Catégorie</span><select name="category"><option value="">Toutes les catégories</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(request('category') === $category->id)>{{ $category->libelle }}</option>@endforeach</select></label>
    <label class="field"><span>État</span><select name="state"><option value="">Tous les états</option><option value="good" @selected(request('state') === 'good')>Bon état</option><option value="fair" @selected(request('state') === 'fair')>À réparer</option><option value="poor" @selected(request('state') === 'poor')>Mauvais état</option><option value="unusable" @selected(request('state') === 'unusable')>Hors service</option></select></label>
    <label class="field"><span>Service</span><select name="service"><option value="">Tous</option>@foreach ($services as $service)<option value="{{ $service->id }}" @selected(request('service') === $service->id)>{{ $service->libelle }}</option>@endforeach</select></label>
    <button class="button button-soft" type="submit">Filtrer les biens</button>
</form>

<section class="panel registry-panel">
    <div class="panel-heading registry-heading"><div><h2>Registre des biens</h2><p>{{ $assets->total() }} résultat{{ $assets->total() === 1 ? '' : 's' }}</p></div><button class="text-button" type="button" data-export>Exporter CSV <span aria-hidden="true">↓</span></button></div>
    <div class="table-scroll"><table class="data-table asset-table"><thead><tr><th>Code</th><th>Désignation</th><th>Catégorie</th><th>Service / site</th><th>État</th><th><span class="sr-only">Action</span></th></tr></thead><tbody>
        @forelse ($assets as $asset)
            @php
                [$stateLabel, $stateClass] = match ($asset->etat) {
                    'good' => ['Bon état', 'status-ok'],
                    'fair' => ['À réparer', 'status-warn'],
                    'poor' => ['Mauvais état', 'status-warn'],
                    'unusable' => ['Hors service', 'status-danger'],
                    default => [$asset->etat, 'status-warn'],
                };
            @endphp
            <tr>
                <td><a class="table-code" href="{{ route('assets.show', $asset->code_inventaire) }}">{{ $asset->code_inventaire }}</a></td>
                <td><a class="table-primary" href="{{ route('assets.show', $asset->code_inventaire) }}">{{ $asset->libelle }}</a></td>
                <td>{{ $asset->categorie }}</td>
                <td><span>{{ $asset->service }}</span><small>{{ $asset->site }}</small></td>
                <td><span class="status {{ $stateClass }}">{{ $stateLabel }}</span></td>
                <td><a class="row-arrow" href="{{ route('assets.show', $asset->code_inventaire) }}" aria-label="Consulter {{ $asset->code_inventaire }}">›</a></td>
            </tr>
        @empty
            <tr><td colspan="6">Aucun bien enregistré ne correspond à ces critères.</td></tr>
        @endforelse
    </tbody></table></div>
    @if ($assets->total() > 0)
        <div class="pagination-row"><span>Affichage de {{ $assets->firstItem() }} à {{ $assets->lastItem() }} sur {{ $assets->total() }} biens</span>{{ $assets->links() }}</div>
    @endif
</section>
@endsection