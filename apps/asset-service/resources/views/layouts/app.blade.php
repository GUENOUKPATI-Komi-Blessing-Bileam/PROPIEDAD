<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123b45">
    <title>@yield('title', 'Patrimoine') · Patrimoine État</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('dashboard') }}" aria-label="Patrimoine État, accueil">
                <span class="brand-mark" aria-hidden="true">+</span>
                <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
            </a>

            <p class="nav-caption">Espace de gestion</p>
            <nav class="primary-nav" aria-label="Navigation principale">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><span class="nav-icon">⌂</span>Tableau de bord</a>
                <a class="nav-link {{ request()->routeIs('assets.*') ? 'is-active' : '' }}" href="{{ route('assets.index') }}"><span class="nav-icon">▦</span>Biens</a>
                <a class="nav-link {{ request()->routeIs('movements.*') ? 'is-active' : '' }}" href="{{ route('movements.index') }}"><span class="nav-icon">⇄</span>Mouvements</a>
                <a class="nav-link {{ request()->routeIs('inventory.*') ? 'is-active' : '' }}" href="{{ route('inventory.show') }}"><span class="nav-icon">▣</span>Inventaires</a>
                <span class="nav-link nav-link-muted"><span class="nav-icon">▤</span>Rapports</span>
            </nav>

            <p class="nav-caption nav-caption-admin">Administration</p>
            <span class="nav-link nav-link-muted"><span class="nav-icon">⚙</span>Utilisateurs</span>

            <div class="sidebar-user">
                <span class="avatar">AM</span>
                <span class="user-copy"><strong>A. Manager</strong><small>Gestionnaire central</small></span>
            </div>
        </aside>

        <div class="workspace">
            <header class="topbar">
                <button class="icon-button menu-toggle" type="button" aria-label="Ouvrir la navigation" aria-controls="sidebar" aria-expanded="false" data-menu-toggle>
                    <span aria-hidden="true">☰</span>
                </button>
                <div class="breadcrumbs"><span>Patrimoine</span><span aria-hidden="true">/</span><strong>@yield('section', 'Tableau de bord')</strong></div>
                <div class="topbar-user"><span class="avatar">AM</span><strong>A. Manager</strong></div>
            </header>

            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>