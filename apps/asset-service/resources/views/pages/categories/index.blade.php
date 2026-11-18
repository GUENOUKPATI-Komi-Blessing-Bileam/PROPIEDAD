@extends('layouts.app')

@section('title', 'Catégories')
@section('section', 'Catégories')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Référentiel patrimonial</p>
        <h1>Catégories</h1>
        <p class="page-lead">Gérez les catégories disponibles dans les formulaires de l’application.</p>
    </div>
</div>

@if (session('status'))
    <p class="submission-success" role="status">{{ session('status') }}</p>
@endif

<section class="panel registry-panel">
    <div class="panel-heading registry-heading">
        <div>
            <h2>Liste des catégories</h2>
            <p>{{ $categories->count() }} catégorie{{ $categories->count() === 1 ? '' : 's' }}</p>
        </div>
        <details class="category-create" @if ($errors->any()) open @endif>
            <summary class="button button-primary"><span aria-hidden="true">＋</span> Nouvelle catégorie</summary>
            <div class="category-form">
                @if ($errors->any())
                    <div class="validation-summary" role="alert">
                        <strong>Vérifiez les informations saisies :</strong>
                        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf
                    <div class="form-grid">
                        <label class="field field-wide">
                            <span>Nom de la catégorie <b>*</b></span>
                            <input required maxlength="255" name="libelle" value="{{ old('libelle') }}" placeholder="Ex. Matériel informatique">
                        </label>
                        <label class="field field-wide">
                            <span>Description</span>
                            <textarea maxlength="2000" name="description" rows="3" placeholder="Précisez les biens concernés par cette catégorie…">{{ old('description') }}</textarea>
                        </label>
                    </div>
                    <div class="form-actions">
                        <button class="button button-primary" type="submit">Créer la catégorie</button>
                    </div>
                </form>
            </div>
        </details>
    </div>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Disponibilité</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td class="table-primary">{{ $category->libelle }}</td>
                        <td>{{ $category->description ?: '—' }}</td>
                        <td><span class="status {{ $category->actif ? 'status-ok' : 'status-warn' }}">{{ $category->actif ? 'Active' : 'Inactive' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="3">Aucune catégorie n’a encore été créée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
