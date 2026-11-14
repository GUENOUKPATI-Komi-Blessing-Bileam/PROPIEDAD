@extends('layouts.app')

@section('title', 'Fiche du bien')
@section('section', 'Biens / INV-2026-0482')

@section('content')
<a class="back-link" href="{{ route('assets.index') }}"><span aria-hidden="true">‹</span> Retour à la liste des biens</a>
<div class="page-heading detail-heading"><div><p class="eyebrow">Fiche du bien</p><h1>Ordinateur portable Dell Latitude</h1><p class="page-lead">Référence <span class="table-code">{{ request()->route('asset') }}</span></p><span class="status status-ok">En service</span></div><div class="heading-actions"><a class="button button-secondary" href="{{ route('assets.create') }}">Modifier</a><a class="button button-primary" href="{{ route('movements.create') }}"><span aria-hidden="true">＋</span> Mouvement</a></div></div>

<div class="detail-grid">
    <section class="panel details-panel"><div class="panel-heading"><div><h2>Informations du bien</h2><p>Données d’identification et d’affectation</p></div></div><dl class="detail-list">
        <div><dt>Catégorie</dt><dd>Matériel informatique</dd></div><div><dt>État physique</dt><dd>Bon état</dd></div>
        <div><dt>Service responsable</dt><dd>Direction des finances</dd></div><div><dt>Emplacement</dt><dd>Bâtiment A · Bureau 204</dd></div>
        <div><dt>Date d’acquisition</dt><dd>14 mars 2025</dd></div><div><dt>Valeur déclarée</dt><dd>À renseigner</dd></div>
        <div class="detail-wide"><dt>Description</dt><dd>Ordinateur portable affecté au service comptable.</dd></div>
        <div class="detail-wide"><dt>Enregistré par</dt><dd>M. Diallo · 14 mars 2025</dd></div>
    </dl></section>
    <aside class="detail-aside">
        <section class="panel label-panel"><div class="panel-heading"><div><h2>Étiquette d’inventaire</h2><p>Code d’identification</p></div></div><div class="label-content"><div class="qr-placeholder" aria-label="Aperçu du code QR"><i></i><i></i><i></i><b></b></div><div><span class="eyebrow">Code d’inventaire</span><strong class="table-code">{{ request()->route('asset') }}</strong><small>QR code fictif pour la maquette</small></div></div><button class="text-button" type="button" data-print>Imprimer l’étiquette <span aria-hidden="true">↗</span></button></section>
        <section class="panel shortcuts-panel"><div class="panel-heading"><div><h2>Raccourcis</h2></div></div><a class="shortcut-link" href="{{ route('movements.create') }}">⇄ &nbsp; Soumettre un transfert <span>›</span></a><a class="shortcut-link" href="{{ route('inventory.show') }}">▣ &nbsp; Vérifier en inventaire <span>›</span></a></section>
    </aside>
</div>
<section class="panel activity-panel detail-history"><div class="panel-heading"><div><h2>Historique du bien</h2><p>Événements récents liés à cette fiche</p></div></div><div class="table-scroll"><table class="data-table"><thead><tr><th>Date</th><th>Événement</th><th>Auteur</th><th>Détail</th></tr></thead><tbody><tr><td>14 mars 2025</td><td>Création de la fiche</td><td>M. Diallo</td><td><a class="text-link" href="#history">Consulter l’événement</a></td></tr><tr><td>22 mars 2025</td><td>Affectation au service</td><td>A. Manager</td><td><a class="text-link" href="#history">Consulter l’événement</a></td></tr></tbody></table></div></section>
@endsection