const passwordInput = document.querySelector('#password');
const passwordToggle = document.querySelector('[data-password-toggle]');
const message = document.querySelector('[data-form-message]');

passwordToggle?.addEventListener('click', () => {
    const showPassword = passwordInput.type === 'password';
    passwordInput.type = showPassword ? 'text' : 'password';
    passwordToggle.setAttribute('aria-pressed', String(showPassword));
    passwordToggle.setAttribute('aria-label', showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
});

document.querySelector('[data-password-help]')?.addEventListener('click', () => {
    message.hidden = false;
    message.textContent = 'La récupération du mot de passe n’est pas encore configurée. Contactez votre administrateur.';
});

document.querySelector('[data-login-form]')?.addEventListener('submit', (event) => {
    event.preventDefault();
    message.hidden = false;
    message.textContent = 'Le point d’accès de connexion est en cours de configuration. Vos identifiants n’ont pas été envoyés.';
});