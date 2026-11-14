@extends('layouts.app')

@section('title', 'Enregistrer un bien')
@section('section', 'Biens / Nouveau bien')

@section('content')
<a class="back-link" href="{{ route('assets.index') }}"><span aria-hidden="true">‹</span> Retour aux biens</a>
<div class="page-heading form-heading"><div><p class="eyebrow">Registre patrimonial</p><h1>Enregistrer un bien</h1><p class="page-lead">Renseignez les informations connues. Les champs marqués d’un * sont obligatoires.</p></div></div>

<form class="form-layout" data-demo-form>
    <section class="panel form-panel">
        <div class="panel-heading"><div><h2>Informations générales</h2><p>Identifiez le bien et son affectation</p></div></div>
        <div class="form-grid">
            <label class="field field-wide"><span>Désignation <b>*</b></span><input required name="libelle" placeholder="Ex. Ordinateur portable Dell Latitude"></label>
            <label class="field"><span>Catégorie <b>*</b></span><select required name="categorie"><option value="">Sélectionner une catégorie</option><option>Mobilier</option><option>Matériel informatique</option><option>Véhicules</option><option>Équipement</option><option>Autres</option></select></label>
            <label class="field"><span>État physique <b>*</b></span><select required name="etat"><option value="">Sélectionner un état</option><option>Bon état</option><option>À réparer</option><option>Hors service</option></select></label>
            <label class="field"><span>Service responsable <b>*</b></span><select required name="service"><option value="">Sélectionner un service</option><option>Direction des finances</option><option>Direction du patrimoine</option><option>Direction des ressources</option></select></label>
            <label class="field"><span>Site / emplacement <b>*</b></span><select required name="site"><option value="">Sélectionner un site</option><option>Bâtiment A</option><option>Bâtiment B</option><option>Parc automobile</option></select></label>
            <label class="field"><span>Date d’acquisition</span><input type="date" name="date_acquisition"></label>
            <div class="field"><span>Valeur d’acquisition</span><div class="input-pair"><input type="number" min="0" step="0.01" name="valeur" placeholder="0,00"><select aria-label="Devise" name="devise"><option>XOF</option><option>EUR</option><option>USD</option></select></div></div>
            <label class="field field-wide"><span>Description complémentaire</span><textarea name="description" rows="3" placeholder="Détails utiles pour identifier le bien…"></textarea></label>
        </div>
        <p class="form-hint">Le code d’inventaire sera attribué après l’enregistrement.</p>
    </section>
    <aside class="form-aside">
        <section class="panel info-panel"><h2>À savoir</h2><ul><li>Le code est attribué par le système.</li><li>La fiche reste modifiable par un gestionnaire habilité.</li><li>La création est inscrite dans l’historique d’audit.</li></ul></section>
        <section class="privacy-panel"><h2>Protection des données</h2><p>Ne saisissez pas de données personnelles dans la description.</p><p>Les champs obligatoires restent à confirmer avec le ministère.</p></section>
    </aside>
    <div class="form-actions"><a class="button button-secondary" href="{{ route('assets.index') }}">Annuler</a><button class="button button-primary" type="submit">Enregistrer</button></div>
</form>
@endsection