@extends('layouts.app')

@section('title', 'Enregistrer un bien')
@section('section', 'Biens / Nouveau bien')

@section('content')
<a class="back-link" href="{{ route('assets.index') }}"><span aria-hidden="true">‹</span> Retour aux biens</a>
<div class="page-heading form-heading"><div><p class="eyebrow">Registre patrimonial</p><h1>Enregistrer un bien</h1><p class="page-lead">Renseignez les informations connues. Les champs marqués d’un * sont obligatoires.</p></div></div>

@if ($errors->any())
    <div class="validation-summary" role="alert">
        <strong>Vérifiez les informations saisies :</strong>
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form class="form-layout" method="POST" action="{{ route('assets.store') }}">
    @csrf
    <section class="panel form-panel">
        <div class="panel-heading">
            <div>
                <h2>Informations générales</h2>
                <p>Identifiez le bien et son affectation</p>
            </div>
        </div>
        <div class="form-grid">
            <label class="field field-wide">
                <span>Désignation <b>*</b></span>
                <input required name="label" value="{{ old('label') }}" placeholder="Ex. Ordinateur portable Dell Latitude">
            </label>
            <label class="field">
                <span>Catégorie <b>*</b></span>
                <select required name="category_id">
                    <option value="">Sélectionner une catégorie</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') === $category->id)>{{ $category->libelle }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span>État physique <b>*</b></span>
                <select required name="condition">
                    <option value="">Sélectionner un état</option>
                    <option value="good" @selected(old('condition') === 'good')>Bon état</option>
                    <option value="fair" @selected(old('condition') === 'fair')>À réparer</option>
                    <option value="unusable" @selected(old('condition') === 'unusable')>Hors service</option>
                </select>
            </label>
            <label class="field">
                <span>Service responsable <b>*</b></span>
                <input required type="text" name="service" list="service-options" value="{{ old('service') }}" autocomplete="off" placeholder="Saisir ou choisir un service">
                <datalist id="service-options">
                    @foreach ($services as $service)
                        <option value="{{ $service->libelle }}">
                    @endforeach
                </datalist>
            </label>
            <label class="field">
                <span>Site / emplacement <b>*</b></span>
                <input required type="text" name="site" list="site-options" value="{{ old('site') }}" autocomplete="off" placeholder="Saisir ou choisir un site">
                <datalist id="site-options">
                    @foreach ($sites as $site)
                        <option value="{{ $site->libelle }}">
                    @endforeach
                </datalist>
            </label>
            <label class="field">
                <span>Date d’acquisition</span>
                <input type="date" name="acquisition_date" value="{{ old('acquisition_date') }}">
            </label>
            <div class="field">
                <span>Valeur d’acquisition</span>
                <div class="input-pair">
                    <input type="number" min="0" step="0.01" name="acquisition_value" value="{{ old('acquisition_value') }}" placeholder="0,00">
                    <select aria-label="Devise" name="currency">
                        <option value="XOF" @selected(old('currency', 'XOF') === 'XOF')>XOF</option>
                        <option value="EUR" @selected(old('currency') === 'EUR')>EUR</option>
                        <option value="USD" @selected(old('currency') === 'USD')>USD</option>
                    </select>
                </div>
            </div>
            <label class="field field-wide"><span>Description complémentaire</span><textarea name="description" rows="3" placeholder="Détails utiles pour identifier le bien…">{{ old('description') }}</textarea></label>
        </div>
        <p class="form-hint">Le code d’inventaire sera attribué après l’enregistrement.</p>
    </section>
    <aside class="form-aside">
        <section class="panel info-panel"><h2>À savoir</h2><ul><li>Le code est attribué par le système.</li><li>La fiche reste modifiable par un gestionnaire habilité.</li><li>La création est inscrite dans l’historique d’audit.</li></ul></section>
        <section class="privacy-panel"><h2>Protection des données</h2><p>Ne saisissez pas de données personnelles dans la description.</p><p>Les champs obligatoires restent à confirmer avec le ministère.</p></section>
    </aside>
    <div class="form-actions">
        <a class="button button-secondary" href="{{ route('assets.index') }}">Annuler</a>
        <button class="button button-primary" type="submit">Enregistrer</button>
    </div>
</form>
@endsection