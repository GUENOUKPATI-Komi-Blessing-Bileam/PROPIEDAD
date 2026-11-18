@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('section', 'Tableau de bord')

@section('content')
<div class="page-heading">
    <div>
        <p class="eyebrow">Vue générale</p>
        <h1>Tableau de bord</h1>
        <p class="page-lead">Vue d'ensemble du patrimoine géré par votre organisation.</p>
    </div>
    <a class="button button-primary" href="{{ route('assets.create') }}"><span aria-hidden="true">＋</span> Enregistrer un bien</a>
</div>

<section class="metric-grid" aria-label="Indicateurs du patrimoine">

    <article class="metric-card">
        <span class="metric-label">Biens enregistrés</span>
        <strong class="metric-value">2 486</strong>
        <span class="metric-note note-positive">↑ 8,2 % depuis le dernier trimestre</span>
    </article>

    <article class="metric-card">
        <span class="metric-label">Valeur déclarée</span>
        <strong class="metric-value">1,84 Md</strong>
        <span class="metric-note">Montant indicatif à confirmer</span>
    </article>

    <article class="metric-card">
        <span class="metric-label">En attente de validation</span>
        <strong class="metric-value">18</strong>
        <a class="metric-note note-warning" href="{{ route('movements.create') }}">● Mouvements à traiter</a>
    </article>

    <article class="metric-card">
        <span class="metric-label">Écarts d’inventaire</span>
        <strong class="metric-value">7</strong>
        <a class="metric-note note-danger" href="{{ route('inventory.show') }}">● À examiner par un gestionnaire</a>
    </article>

</section>

<div class="dashboard-grid">
    <section class="panel category-panel">

        <div class="panel-heading">
            <div><h2>Biens par catégorie</h2>
            <p>Répartition indicative du registre</p>
        </div>
        <a class="text-link" href="{{ route('assets.index') }}">Voir les biens <span aria-hidden="true">→</span></a>
        <div class="category-content">
            <div class="category-legend">
                <div>
                    <i class="swatch swatch-teal"></i>
                    <span>Mobilier et équipements</span>
                    <strong>42 %</strong>
                </div>
                <div>
                    <i class="swatch swatch-blue"></i>
                    <span>Matériel informatique</span>
                    <strong>28 %</strong>
                </div>
                <div>
                    <i class="swatch swatch-sage"></i>
                    <span>Véhicules</span>
                    <strong>18 %</strong>
                </div>
                <div>
                    <i class="swatch swatch-gold"></i>
                    <span>Autres</span>
                    <strong>12 %</strong>
                </div>
            </div>
            <div class="donut" role="img" aria-label="Répartition des 2 486 biens par catégorie"><div><strong>2 486</strong><span>biens</span></div></div>
        </div>
    </section>

    <section class="panel attention-panel">
        <div class="panel-heading">
            <div><h2>À suivre</h2>
            <p>Actions nécessitant votre attention</p>
        </div>
        <a class="attention-row" href="{{ route('movements.create') }}">
            <span class="attention-mark mark-warning">!</span>
            <span>
                <strong>18 mouvements à valider</strong>
                <small>Dernière demande reçue il y a 2 h</small>
            </span>
            <span class="row-arrow">›</span>
        </a>
        <a class="attention-row" href="{{ route('inventory.show') }}">
            <span class="attention-mark mark-danger">!</span>
            <span>
                <strong>7 écarts d’inventaire</strong>
                <small>Campagne « Inventaire 2026 »</small>
            </span>
            <span class="row-arrow">›</span>
        </a>
        <a class="attention-row" href="{{ route('inventory.show') }}">
            <span class="attention-mark mark-success">✓</span>
            <span>
                <strong>Campagne en cours</strong>
                <small>68 % des biens vérifiés</small>
            </span>
            <span class="row-arrow">›</span>
        </a>
    </section>
</div>

<section class="panel activity-panel">
    <div class="panel-heading">
        <div>
            <h2>Activité récente</h2>
            <p>Dernières opérations du registre</p>
        </div>
    </div>
    <div class="table-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Événement</th>
                    <th>Référence</th>
                    <th>Utilisateur</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bien ajouté au registre</td>
                    <td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0482') }}">INV-2026-0482</a></td>
                    <td>M. Diallo</td>
                    <td>Aujourd’hui, 09:42</td>
                </tr>
                <tr>
                    <td>Transfert soumis</td>
                    <td><a class="table-code" href="{{ route('assets.show', 'INV-2026-0317') }}">INV-2026-0317</a></td>
                    <td>A. Manager</td>
                    <td>Hier, 16:18</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection