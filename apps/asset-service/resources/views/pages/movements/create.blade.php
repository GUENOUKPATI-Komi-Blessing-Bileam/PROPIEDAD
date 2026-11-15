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

<div role="status" data-movement-form-message></div>
<form class="form-layout movement-layout" data-movement-form>
    <section class="panel form-panel"><div class="panel-heading"><div><h2>Détails de la demande</h2><p>Les champs marqués d’un * sont obligatoires</p></div></div>
        <div class="form-grid">
            <div class="field field-wide">
                <span>Bien concerné <b>*</b></span>
                <div class="selected-asset">
                    <span class="asset-initial">B</span>
                    <span>
                       <strong>INV-2026-0482 · Ordinateur portable Dell Latitude</strong>
                       <small>Situation actuelle : Direction des finances · Bâtiment A · Bureau 204</small>
                    </span>
                </div></div>
            <label class="field">
                <span>Type de mouvement <b>*</b></span>
                <select name="type" required>
                    <option value="Transfert">Transfert</option>
                    <option value="Sortie temporaire">Sortie temporaire</option>
                    <option value="Retour">Retour</option>
                </select>
            </label>
            <label class="field">
                <span>Date d’effet <b>*</b></span>
                <input name="effective_at" required type="date">
            </label>
            <label class="field">
                <span>Service de destination <b>*</b></span>
                <select name="destination_service" required>
                    <option value="">Sélectionner un service</option>
                    <option>Direction du patrimoine</option>
                    <option>Direction des ressources</option>
                </select>
            </label>
            <label class="field">
                <span>Site / emplacement de destination <b>*</b></span>
                <select name="destination_site" required>
                    <option value="">Sélectionner un site</option>
                    <option>Bâtiment B</option>
                    <option>Parc automobile</option>
                </select>
            </label>
            <label class="field field-wide">
                <span>Personne ou équipe destinataire</span>
                <input name="recipient" placeholder="Nom ou service destinataire (facultatif)">
            </label>
            <label class="field field-wide">
                <span>Motif de la demande <b>*</b></span>
                <textarea name="reason" required rows="3" placeholder="Précisez la raison du transfert…"></textarea>
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