<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#123b45">
    <title>Connexion · Patrimoine État</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <script src="{{ asset('js/auth.js') }}" defer></script>
</head>
<body>
    <main class="login-page">
        <section class="brand-panel" aria-labelledby="brand-heading">
            <div class="brand-lockup">
                <span class="brand-mark" aria-hidden="true">+</span>
                <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
            </div>
            <div class="brand-message">
                <p class="eyebrow">Gestion publique · Accès sécurisé</p>
                <h1 id="brand-heading">Un patrimoine public<br>mieux protégé.</h1>
                <p class="brand-description">La plateforme de suivi et d’inventaire des biens de l’État, pour les services habilités.</p>
                <div class="brand-feature"><span class="feature-rule"></span><span><strong>Inventaire centralisé</strong><small>Une vision claire des biens et de leur localisation.</small></span></div>
            </div>
            <p class="brand-footer">Plateforme de gestion du patrimoine public</p>
        </section>

        <section class="auth-panel" aria-labelledby="login-heading">
            <div class="auth-content">
                <p class="secure-label"><span aria-hidden="true">●</span> Espace sécurisé</p>
                <h2 id="login-heading">Bienvenue</h2>
                <p class="auth-intro">Connectez-vous pour accéder à votre espace.</p>

                <form class="login-form" data-login-form>
                    <label class="field" for="email">Adresse e-mail</label>
                    <input id="email" name="email" type="email" autocomplete="username" inputmode="email" placeholder="nom@ministere.gov" required>

                    <div class="password-heading">
                        <label class="field" for="password">Mot de passe</label>
                        <button class="text-action" type="button" data-password-help>Mot de passe oublié ?</button>
                    </div>
                    <div class="password-field">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Saisissez votre mot de passe" required>
                        <button class="password-toggle" type="button" aria-label="Afficher le mot de passe" aria-pressed="false" data-password-toggle><span aria-hidden="true">◉</span></button>
                    </div>

                    <button class="submit-button" type="submit">Se connecter <span aria-hidden="true">→</span></button>
                    <p class="form-message" role="status" aria-live="polite" data-form-message hidden></p>
                </form>

                <div class="access-note"><span class="note-rule"></span><p>L’accès est réservé aux utilisateurs autorisés.<br>En cas de problème, contactez votre administrateur.</p></div>
            </div>
            <p class="mobile-footer">Plateforme de gestion du patrimoine public</p>
        </section>
    </main>
</body>
</html>