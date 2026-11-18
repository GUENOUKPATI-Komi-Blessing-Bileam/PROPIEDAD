@extends('layouts.app')

@section('title', 'Soumettre un mouvement')
@section('section', 'Mouvements / Nouvelle demande')

@section('content')
<a class="back-link" href="{{ route('movements.index') }}">
    <span aria-hidden="true">‹</span> Retour aux mouvements
</a>

<div class="page-heading form-heading">
    <div>
        <p class="eyebrow">Mouvement de bien</p>
        <h1>Soumettre un mouvement</h1>
        <p class="page-lead">Une demande validée mettra à jour la situation actuelle du bien.</p>
    </div>
</div>

@if ($errors->any())
    <div class="validation-summary" role="alert">
        <strong>Vérifiez les informations saisies :</strong>
        <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form class="form-layout movement-layout" method="POST" action="{{ route('movements.store') }}">
    @csrf
    <section class="panel form-panel">

        <div class="panel-heading">
            <div>
                <h2>Détails de la demande</h2>
                <p>Les champs marqués d’un * sont obligatoires</p>
            </div>
        </div>

        <div class="form-grid">

            <label class="field field-wide">
                <span>Bien concerné <b>*</b></span>
                <select name="bien_id" required>
                    <option value="">Sélectionner un bien</option>
                    @foreach ($assets as $asset)
                        <option value="{{ $asset->id }}" @selected(old('bien_id') === $asset->id)>
                            {{ $asset->code_inventaire }} · {{ $asset->libelle }} — {{ $asset->service }} · {{ $asset->site }}
                        </option>
                    @endforeach
                </select>
                @if ($assets->isEmpty())
                    <small>Aucun bien n’est enregistré. Enregistrez d’abord un bien pour soumettre un mouvement.</small>
                @endif
            </label>

            <label class="field">
                <span>Type de mouvement <b>*</b></span>
                <select name="type" required>
                    <option value="Transfert" @selected(old('type', 'Transfert') === 'Transfert')>Transfert</option>
                    <option value="Sortie temporaire" @selected(old('type') === 'Sortie temporaire')>Sortie temporaire</option>
                    <option value="Retour" @selected(old('type') === 'Retour')>Retour</option>
                </select>
            </label>
            <label class="field">
                <span>Date d’effet <b>*</b></span>
                <input name="date_effet" value="{{ old('date_effet') }}" required type="date">
            </label>
            <label class="field">
                <span>Service de destination <b>*</b></span>
                <select name="service_destination_id" required>
                    <option value="">Sélectionner un service</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected(old('service_destination_id') === $service->id)>{{ $service->libelle }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span>Site / emplacement de destination <b>*</b></span>
                <select name="site_destination_id" required>
                    <option value="">Sélectionner un site</option>
                    @foreach ($sites as $site)
                        <option value="{{ $site->id }}" @selected(old('site_destination_id') === $site->id)>{{ $site->libelle }}</option>
                    @endforeach
                </select>
            </label>
            <label class="field field-wide">
                <span>Personne ou équipe destinataire</span>
                <input name="destinataire" value="{{ old('destinataire') }}" placeholder="Nom ou service destinataire (facultatif)">
            </label>
            <label class="field field-wide">
                <span>Motif de la demande <b>*</b></span>
                <textarea name="motif" required rows="3" placeholder="Précisez la raison du transfert…">{{ old('motif') }}</textarea>
            </label>
        </div>
    </section>
    
    <aside class="form-aside">
        <section class="panel info-panel">
            <h2>Circuit de validation</h2>
            <div class="step-row">
                <span class="step-number">1</span>
                <span>
                    <strong>Soumission</strong>
                    <small>Par vous, après confirmation.</small>
                </span>
            </div>
            <div class="step-row">
                <span class="step-number step-muted">2</span>
                <span><strong>Validation</strong><small>Par un gestionnaire habilité.</small></span>
            </div>
        </section>
        <section class="notice-panel">
            <span class="attention-mark mark-warning">!</span>
            <h2>Avant de soumettre</h2>
            <p>La situation du bien ne change qu’après validation de la demande.</p>
            <p>La demande et son auteur seront conservés dans l’historique.</p>
            <p>Vérifiez la destination avant envoi.</p>
        </section>
    </aside>

    <div class="form-actions">
        <a class="button button-secondary" href="{{ route('movements.index') }}">Annuler</a>
        <button class="button button-primary" type="submit">Soumettre</button>
    </div>
</form>
@endsection