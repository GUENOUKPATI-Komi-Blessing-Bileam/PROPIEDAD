<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123b45">
    <title>Rapports du patrimoine · Patrimoine État</title>
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
    <script src="{{ asset('js/reports.js') }}" defer></script>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('reports.index') }}" aria-label="Patrimoine État, rapports">
                <span class="brand-mark" aria-hidden="true">+</span>
                <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
            </a>
            <p class="nav-caption">Espace de gestion</p>
            <nav class="primary-nav" aria-label="Navigation principale">
                <a class="nav-link" href="#dashboard"><span class="nav-icon">⌂</span>Tableau de bord</a>
                <a class="nav-link" href="#assets"><span class="nav-icon">▦</span>Biens</a>
                <a class="nav-link" href="#movements"><span class="nav-icon">⇄</span>Mouvements</a>
                <a class="nav-link" href="#inventory"><span class="nav-icon">▣</span>Inventaires</a>
                <a class="nav-link is-active" href="{{ route('reports.index') }}" aria-current="page"><span class="nav-icon">▤</span>Rapports</a>
            </nav>
            <div class="sidebar-user"><span class="avatar">AM</span><span class="user-copy"><strong>A. Manager</strong><small>Gestionnaire central</small></span></div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <button class="menu-toggle" type="button" aria-label="Ouvrir la navigation" aria-controls="sidebar" aria-expanded="false" data-menu-toggle>☰</button>
                <div class="breadcrumbs"><span>Patrimoine</span><span aria-hidden="true">/</span><strong>Rapports</strong></div>
                <div class="topbar-user"><span class="avatar">AM</span><strong>A. Manager</strong></div>
            </header>

            <main class="page-content">
                <div class="page-heading">
                    <div><p class="eyebrow">Analyse patrimoniale</p><h1>Rapports du patrimoine</h1><p class="page-lead">Consultez les indicateurs et exportez les données autorisées.</p></div>
                    <span class="demo-tag">Aperçu de démonstration</span>
                </div>

                <form class="filter-panel" data-report-form>
                    <div class="filter-heading"><h2>Paramètres du rapport</h2><p>Choisissez le périmètre à analyser</p></div>
                    <div class="filter-controls">
                        <label class="field"><span>Période</span><div class="date-range"><input type="date" name="from" value="2026-01-01" aria-label="Date de début"><span aria-hidden="true">–</span><input type="date" name="to" value="2026-11-30" aria-label="Date de fin"></div></label>
                        <label class="field"><span>Service</span><select name="service"><option value="">Tous les services</option><option>Direction des finances</option><option>Direction du patrimoine</option><option>Direction des ressources</option></select></label>
                        <label class="field"><span>Catégorie</span><select name="category"><option value="">Toutes</option><option>Mobilier et équipements</option><option>Matériel informatique</option><option>Véhicules</option><option>Autres</option></select></label>
                        <button class="button button-primary" type="submit">Générer <span aria-hidden="true">→</span></button>
                    </div>
                    <p class="filter-message" role="status" aria-live="polite" data-filter-message hidden></p>
                </form>

                <section class="metric-grid" aria-label="Indicateurs du rapport">
                    <article class="metric-card"><span class="metric-label">Total des biens</span><strong class="metric-value" data-export-value="total">2 486</strong><small>Biens dans le périmètre</small></article>
                    <article class="metric-card"><span class="metric-label">Biens en service</span><strong class="metric-value positive" data-export-value="active">2 301</strong><small>92,6 % du registre</small></article>
                    <article class="metric-card"><span class="metric-label">Mouvements validés</span><strong class="metric-value" data-export-value="movements">146</strong><small>Sur la période sélectionnée</small></article>
                    <article class="metric-card"><span class="metric-label">Écarts d’inventaire</span><strong class="metric-value danger" data-export-value="discrepancies">7</strong><small>À examiner par un gestionnaire</small></article>
                </section>

                <div class="charts-grid">
                    <section class="panel movement-panel">
                        <div class="panel-heading"><div><h2>Évolution des mouvements</h2><p>Entrées et transferts sur la période sélectionnée</p></div><span class="chart-period" data-chart-period>Jan. – Nov. 2026</span></div>
                        <div class="chart-legend"><span><i class="legend-entry"></i>Entrées</span><span><i class="legend-transfer"></i>Transferts</span></div>
                        <div class="bar-chart" role="img" aria-label="Graphique comparant entrées et transferts mensuels">
                            <div class="chart-y-axis"><span>60</span><span>40</span><span>20</span><span>0</span></div>
                            <div class="chart-plot">
                                <div class="gridline gridline-60"></div><div class="gridline gridline-40"></div><div class="gridline gridline-20"></div><div class="gridline gridline-0"></div>
                                <div class="bar-groups">
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 46%"></i><i class="bar transfer" style="--bar-height: 22%"></i></div><span>Juin</span></div>
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 69%"></i><i class="bar transfer" style="--bar-height: 38%"></i></div><span>Juil.</span></div>
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 57%"></i><i class="bar transfer" style="--bar-height: 27%"></i></div><span>Août</span></div>
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 88%"></i><i class="bar transfer" style="--bar-height: 44%"></i></div><span>Sept.</span></div>
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 69%"></i><i class="bar transfer" style="--bar-height: 38%"></i></div><span>Oct.</span></div>
                                    <div class="bar-group"><div class="bar-pair"><i class="bar entry" style="--bar-height: 77%"></i><i class="bar transfer" style="--bar-height: 49%"></i></div><span>Nov.</span></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="panel category-panel">
                        <div class="panel-heading"><div><h2>Par catégorie</h2><p>Répartition des biens enregistrés</p></div></div>
                        <div class="category-list">
                            <div class="category-item"><div><span>Mobilier et équipements</span><strong>42 %</strong></div><div class="progress-track"><i style="--progress: 42%; --category-color: #176b61"></i></div></div>
                            <div class="category-item"><div><span>Matériel informatique</span><strong>28 %</strong></div><div class="progress-track"><i style="--progress: 28%; --category-color: #3e91a0"></i></div></div>
                            <div class="category-item"><div><span>Véhicules</span><strong>18 %</strong></div><div class="progress-track"><i style="--progress: 18%; --category-color: #83b9a6"></i></div></div>
                            <div class="category-item"><div><span>Autres</span><strong>12 %</strong></div><div class="progress-track"><i style="--progress: 12%; --category-color: #e3b76e"></i></div></div>
                        </div>
                    </section>
                </div>

                <section class="export-panel"><div><h2>Rapport prêt à exporter</h2><p>Les données affichées sont fictives et limitées au périmètre de la maquette.</p></div><button class="button button-primary" type="button" data-export><span aria-hidden="true">↓</span> Exporter CSV</button></section>
            </main>
        </div>
    </div>
</body>
</html>