<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123b45">
    <title>@yield('title', 'Historique') · Patrimoine État</title>
    <link rel="stylesheet" href="{{ asset('css/history.css') }}">
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a class="brand" href="{{ route('history.index') }}" aria-label="Patrimoine État, journal d’audit">
                <span class="brand-mark" aria-hidden="true">+</span>
                <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
            </a>
            <p class="nav-caption">Espace de gestion</p>
            <nav class="primary-nav" aria-label="Navigation principale">
                <a class="nav-link is-active" href="{{ route('history.index') }}"><span class="nav-icon">◷</span>Journal d’audit</a>
            </nav>
            <div class="sidebar-note"><span class="live-dot"></span>Collecte des événements active</div>
            <div class="sidebar-user"><span class="avatar">AM</span><span class="user-copy"><strong>A. Manager</strong><small>Gestionnaire central</small></span></div>
        </aside>
        <div class="workspace">
            <header class="topbar">
                <button class="menu-toggle" type="button" aria-label="Ouvrir la navigation" aria-controls="sidebar" aria-expanded="false" data-menu-toggle>☰</button>
                <div class="breadcrumbs"><span>Patrimoine</span><span aria-hidden="true">/</span><strong>Journal d’audit</strong></div>
                <div class="topbar-user"><span class="avatar">AM</span><strong>A. Manager</strong></div>
            </header>
            <main class="page-content">@yield('content')</main>
        </div>
    </div>
    <script>
        const menuToggle = document.querySelector('[data-menu-toggle]');
        const sidebar = document.querySelector('#sidebar');
        menuToggle.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('is-open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.setAttribute('aria-label', isOpen ? 'Fermer la navigation' : 'Ouvrir la navigation');
        });
    </script>
</body>
</html>