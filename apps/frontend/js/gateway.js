(() => {
    const apiRoot = '/api/v1';
    const tokenKey = 'patrimoine.access_token';
    const app = document.querySelector('#app');
    const state = {
        token: sessionStorage.getItem(tokenKey),
        user: null,
    };

    const pages = {
        '/': { title: 'Tableau de bord', section: 'Vue générale' },
        '/assets': { title: 'Biens', section: 'Registre patrimonial' },
        '/assets/create': { title: 'Nouveau bien', section: 'Registre patrimonial' },
        '/movements': { title: 'Mouvements', section: 'Suivi des mouvements' },
        '/movements/create': { title: 'Soumettre un mouvement', section: 'Suivi des mouvements' },
        '/inventories': { title: 'Inventaires', section: 'Campagnes de contrôle' },
        '/inventories/create': { title: 'Nouvelle campagne', section: 'Campagnes de contrôle' },
        '/history': { title: 'Historique', section: 'Journal d’audit' },
        '/reports': { title: 'Rapports', section: 'Indicateurs et exports' },
    };

    const navItems = [
        ['/', '⌂', 'Tableau de bord'],
        ['/assets', '▦', 'Biens'],
        ['/movements', '⇄', 'Mouvements'],
        ['/inventories', '▣', 'Inventaires'],
        ['/history', '◷', 'Historique'],
        ['/reports', '▤', 'Rapports'],
    ];

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (character) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        })[character]);
    }

    function currentPage() {
        const path = window.location.pathname.replace(/\/+$/, '') || '/';
        if (path.startsWith('/assets/')) {
            return { title: 'Fiche du bien', section: 'Registre patrimonial', kind: 'asset-detail' };
        }
        if (path.startsWith('/inventories/')) {
            return { title: 'Détail de la campagne', section: 'Campagnes de contrôle', kind: 'inventory-detail' };
        }
        return pages[path] || pages['/'];
    }

    function activePath() {
        const path = window.location.pathname;
        if (path.startsWith('/assets')) return '/assets';
        if (path.startsWith('/movements')) return '/movements';
        if (path.startsWith('/inventories')) return '/inventories';
        return pages[path] ? path : '/';
    }

    function shell() {
        const page = currentPage();
        const active = activePath();
        app.className = 'app-shell';
        app.innerHTML = `
            <aside class="sidebar">
                <a class="brand" href="/" aria-label="Patrimoine État, accueil">
                    <span class="brand-mark" aria-hidden="true">+</span>
                    <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
                </a>
                <p class="nav-caption">Espace de gestion</p>
                <nav class="primary-nav" aria-label="Navigation principale">
                    ${navItems.map(([href, icon, label]) => `
                        <a class="nav-link ${active === href ? 'is-active' : ''}" href="${href}">
                            <span class="nav-icon" aria-hidden="true">${icon}</span>${label}
                        </a>`).join('')}
                </nav>
                <div class="sidebar-bottom">
                    <span class="user-name">${escapeHtml(state.user?.name || 'Utilisateur')}</span>
                    <span class="user-email">${escapeHtml(state.user?.email || '')}</span>
                    <button class="logout-button" type="button" data-action="logout">Se déconnecter</button>
                </div>
            </aside>
            <div class="workspace">
                <header class="topbar">
                    <div class="breadcrumbs"><span>Patrimoine</span><span aria-hidden="true">/</span><strong>${escapeHtml(page.title)}</strong></div>
                    <span class="topbar-note">Plateforme de gestion du patrimoine</span>
                </header>
                <main class="page-content" id="page-content"></main>
            </div>`;
        return document.querySelector('#page-content');
    }

    function showLogin(message = '') {
        app.className = 'login-screen';
        app.innerHTML = `
            <section class="login-card">
                <a class="brand" href="/" aria-label="Patrimoine État">
                    <span class="brand-mark" aria-hidden="true">+</span>
                    <span class="brand-name">PATRIMOINE<small>ÉTAT</small></span>
                </a>
                <h1>Connexion</h1>
                <p class="page-lead">Connectez-vous pour accéder au registre, aux inventaires, à l’historique et aux rapports.</p>
                <div id="login-message" role="alert"></div>
                <form id="login-form">
                    <div class="field"><label for="email">Adresse e-mail</label><input id="email" name="email" type="email" autocomplete="username" required></div>
                    <div class="field"><label for="password">Mot de passe</label><input id="password" name="password" type="password" autocomplete="current-password" required></div>
                    <button class="button button-primary" type="submit">Se connecter</button>
                </form>
                <p class="footer-note">Un seul accès sécurisé à l’ensemble des services.</p>
            </section>`;

        document.querySelector('#login-form').addEventListener('submit', login);
        if (message) setNotice(document.querySelector('#login-message'), message, 'error');
    }

    function setNotice(container, message, type = '') {
        container.innerHTML = '';
        if (!message) return;
        const notice = document.createElement('div');
        notice.className = `notice ${type ? `notice-${type}` : ''}`;
        notice.textContent = message;
        container.append(notice);
    }

    async function request(path, options = {}) {
        const headers = new Headers(options.headers || {});
        headers.set('Accept', 'application/json');
        if (state.token) headers.set('Authorization', `Bearer ${state.token}`);
        if (options.body && typeof options.body !== 'string') {
            headers.set('Content-Type', 'application/json');
            options.body = JSON.stringify(options.body);
        }
        if (options.idempotent) headers.set('Idempotency-Key', crypto.randomUUID());

        let response;
        try {
            response = await fetch(`${apiRoot}${path}`, { ...options, headers });
        } catch {
            throw new Error('Impossible de joindre le Gateway. Vérifiez que la plateforme est démarrée.');
        }

        if (response.status === 204) return null;
        const contentType = response.headers.get('content-type') || '';
        const payload = contentType.includes('json') ? await response.json() : await response.text();
        if (!response.ok) {
            const detail = typeof payload === 'object'
                ? (payload.detail || payload.title || `Erreur ${response.status}`)
                : `Erreur ${response.status}`;
            const requestId = typeof payload === 'object' ? payload.request_id : null;
            throw new Error(`${detail}${requestId ? ` (référence : ${requestId})` : ''}`);
        }
        return payload;
    }

    async function login(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = form.querySelector('button[type="submit"]');
        button.disabled = true;
        button.textContent = 'Connexion…';
        try {
            const credentials = Object.fromEntries(new FormData(form));
            const result = await request('/auth/login', { method: 'POST', body: credentials });
            if (!result.access_token) throw new Error('Le service d’authentification n’a pas retourné de jeton.');
            state.token = result.access_token;
            state.user = result.user || null;
            sessionStorage.setItem(tokenKey, state.token);
            render();
        } catch (error) {
            setNotice(document.querySelector('#login-message'), error.message, 'error');
            button.disabled = false;
            button.textContent = 'Se connecter';
        }
    }

    async function logout() {
        let message = 'Vous êtes déconnecté.';
        try {
            await request('/auth/logout', { method: 'POST' });
        } catch (error) {
            message = `La session locale a été fermée, mais le service d’authentification n’a pas confirmé la révocation : ${error.message}`;
        }
        state.token = null;
        state.user = null;
        sessionStorage.removeItem(tokenKey);
        showLogin(message);
    }

    function heading(title, lead, action = '') {
        return `<div class="page-heading"><div><p class="eyebrow">${escapeHtml(currentPage().section)}</p><h1>${escapeHtml(title)}</h1><p class="page-lead">${escapeHtml(lead)}</p></div>${action}</div>`;
    }

    function table(container, rows, columns, actions) {
        if (!Array.isArray(rows) || rows.length === 0) {
            container.innerHTML = '<div class="table-empty">Aucune donnée à afficher pour le moment.</div>';
            return;
        }
        const wrapper = document.createElement('div');
        wrapper.className = 'table-wrap';
        const tableElement = document.createElement('table');
        tableElement.className = 'data-table';
        const head = document.createElement('thead');
        const headRow = document.createElement('tr');
        columns.forEach(([, label]) => {
            const cell = document.createElement('th');
            cell.textContent = label;
            headRow.append(cell);
        });
        if (actions) {
            const cell = document.createElement('th');
            cell.textContent = 'Actions';
            headRow.append(cell);
        }
        head.append(headRow);
        tableElement.append(head);
        const body = document.createElement('tbody');
        rows.forEach((row) => {
            const tr = document.createElement('tr');
            columns.forEach(([key]) => {
                const td = document.createElement('td');
                const value = key.split('.').reduce((result, part) => result?.[part], row);
                td.textContent = value == null ? '—' : String(value);
                tr.append(td);
            });
            if (actions) {
                const td = document.createElement('td');
                td.append(actions(row));
                tr.append(td);
            }
            body.append(tr);
        });
        tableElement.append(body);
        wrapper.append(tableElement);
        container.replaceChildren(wrapper);
    }

    function actionButton(label, action, id, type = 'secondary') {
        const button = document.createElement('button');
        button.className = `button button-small button-${type}`;
        button.type = 'button';
        button.dataset.action = action;
        button.dataset.id = id;
        button.textContent = label;
        return button;
    }

    function rowsFrom(payload) {
        if (Array.isArray(payload)) return payload;
        if (Array.isArray(payload?.data)) return payload.data;
        return [];
    }

    async function loadInto(container, path, renderData) {
        try {
            const payload = await request(path);
            renderData(payload);
        } catch (error) {
            setNotice(container, `${error.message} Le service métier correspondant doit exposer cette route pour activer cette fonction.`, 'error');
        }
    }

    async function dashboard(container) {
        container.innerHTML = `${heading('Tableau de bord', 'Une vue unique sur les services de gestion du patrimoine.')}
            <div id="dashboard-message"></div>
            <section class="metric-grid" id="metrics"></section>
            <div class="dashboard-grid">
                <section class="panel"><div class="panel-heading"><div><h2>Accès rapides</h2><p>Retrouvez les modules de la plateforme</p></div></div>
                    <div class="panel-content"><div class="quick-links">
                        <a class="quick-link" href="/assets"><span class="quick-icon">▦</span><span><strong>Registre des biens</strong><small>Consulter et mettre à jour</small></span></a>
                        <a class="quick-link" href="/movements"><span class="quick-icon">⇄</span><span><strong>Mouvements</strong><small>Suivre les demandes</small></span></a>
                        <a class="quick-link" href="/inventories"><span class="quick-icon">▣</span><span><strong>Inventaires</strong><small>Gérer les campagnes</small></span></a>
                        <a class="quick-link" href="/history"><span class="quick-icon">◷</span><span><strong>Historique</strong><small>Consulter le journal d’audit</small></span></a>
                    </div></div>
                </section>
                <section class="panel"><div class="panel-heading"><div><h2>Services connectés</h2><p>Les modules passent par le même point d’entrée sécurisé.</p></div></div>
                    <div class="panel-content"><div class="quick-links">
                        <a class="quick-link" href="/assets"><span class="quick-icon">01</span><span><strong>Authentification</strong><small>Connexion centralisée</small></span></a>
                        <a class="quick-link" href="/assets"><span class="quick-icon">02</span><span><strong>Gestion des biens</strong><small>API via le Gateway</small></span></a>
                        <a class="quick-link" href="/history"><span class="quick-icon">03</span><span><strong>Historique</strong><small>API via le Gateway</small></span></a>
                        <a class="quick-link" href="/reports"><span class="quick-icon">04</span><span><strong>Rapports</strong><small>API via le Gateway</small></span></a>
                    </div></div>
                </section>
            </div>
            <section class="panel"><div class="panel-heading"><div><h2>Activité récente</h2><p>Dernières opérations consignées</p></div><a class="button button-secondary button-small" href="/history">Voir l’historique</a></div>
                <div id="recent-events"></div></section>`;

        const metrics = document.querySelector('#metrics');
        const summaryPromise = request('/reports/summary').then((summary) => {
            const items = [
                ['Biens enregistrés', summary.assets_total],
                ['Biens actifs', summary.assets_active],
                ['Mouvements en attente', summary.pending_movements],
                ['Écarts d’inventaire', summary.inventory_discrepancies],
            ];
            metrics.innerHTML = items.map(([label, value]) => `<article class="metric-card"><span class="metric-label">${escapeHtml(label)}</span><strong class="metric-value">${escapeHtml(value ?? '—')}</strong><span class="metric-note">Données du service de rapports</span></article>`).join('');
        }).catch((error) => {
            metrics.innerHTML = '';
            setNotice(document.querySelector('#dashboard-message'), `${error.message} Les indicateurs apparaîtront lorsque le service de rapports sera disponible.`, 'error');
        });
        const historyPromise = request('/history?per_page=5').then((payload) => {
            const events = rowsFrom(payload);
            table(document.querySelector('#recent-events'), events, [
                ['event_type', 'Événement'], ['aggregate_type', 'Type'], ['aggregate_id', 'Référence'], ['occurred_at', 'Date'],
            ]);
        }).catch((error) => {
            setNotice(document.querySelector('#recent-events'), `${error.message} Le journal apparaîtra lorsque le service d’historique sera disponible.`, 'error');
        });
        await Promise.allSettled([summaryPromise, historyPromise]);
    }

    async function assets(container) {
        const createLink = '<a class="button button-primary" href="/assets/create">＋ Enregistrer un bien</a>';
        container.innerHTML = `${heading('Biens', 'Recherchez et consultez les biens du registre.', createLink)}
            <div id="list-message"></div>
            <form class="toolbar" id="asset-search"><input name="q" placeholder="Rechercher par libellé ou code"><button class="button button-secondary" type="submit">Rechercher</button></form>
            <section class="panel"><div id="asset-table"></div></section>`;
        async function search(query = '') {
            await loadInto(document.querySelector('#list-message'), `/assets?per_page=20${query ? `&q=${encodeURIComponent(query)}` : ''}`, (payload) => {
                const rows = rowsFrom(payload);
                table(document.querySelector('#asset-table'), rows, [
                    ['inventory_code', 'Code inventaire'], ['label', 'Libellé'], ['status', 'Statut'], ['condition', 'État'],
                ], (row) => {
                    const link = document.createElement('a');
                    link.className = 'button button-small button-secondary';
                    link.href = `/assets/${encodeURIComponent(row.id)}`;
                    link.textContent = 'Consulter';
                    return link;
                });
            });
        }
        document.querySelector('#asset-search').addEventListener('submit', (event) => {
            event.preventDefault();
            search(new FormData(event.currentTarget).get('q').trim());
        });
        await search();
    }

    function formField(label, name, type = 'text', options = {}) {
        const full = options.full ? ' field-full' : '';
        const required = options.required ? ' required' : '';
        const hint = options.hint ? `<small>${escapeHtml(options.hint)}</small>` : '';
        const control = options.options
            ? `<select id="${name}" name="${name}"${required}>${options.placeholder ? `<option value="">${escapeHtml(options.placeholder)}</option>` : ''}${options.options.map(([value, text]) => `<option value="${escapeHtml(value)}">${escapeHtml(text)}</option>`).join('')}</select>`
            : type === 'textarea'
                ? `<textarea id="${name}" name="${name}"${required}></textarea>`
                : `<input id="${name}" name="${name}" type="${type}"${required}${options.step ? ` step="${options.step}"` : ''}>`;
        return `<div class="field${full}"><label for="${name}">${escapeHtml(label)}</label>${control}${hint}</div>`;
    }

    async function createForm(container, { title, lead, endpoint, fields, successPath, referenceLists }) {
        if (referenceLists) {
            try {
                const references = await request('/assets/references');
                fields = fields.map((field) => {
                    if (!field.reference) return field;
                    const options = references[field.reference] || [];
                    if (options.length === 0) {
                        throw new Error(`Aucun référentiel disponible pour « ${field.label} ». Contactez un administrateur.`);
                    }
                    return formField(field.label, field.name, 'text', {
                        required: true,
                        options: options.map((option) => [option.id, option.label]),
                        placeholder: 'Sélectionner…',
                    });
                });
            } catch (error) {
                container.innerHTML = `${heading(title, lead)}<div id="form-message"></div>`;
                setNotice(document.querySelector('#form-message'), error.message, 'error');
                return;
            }
        }

        container.innerHTML = `${heading(title, lead)}
            <div id="form-message"></div>
            <form class="form-panel" id="create-form"><div class="form-grid">${fields}</div>
                <div class="form-actions"><a class="button button-secondary" href="${successPath}">Annuler</a><button class="button button-primary" type="submit">Enregistrer</button></div>
            </form>`;
        document.querySelector('#create-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const form = event.currentTarget;
            const values = Object.fromEntries(new FormData(form));
            Object.keys(values).forEach((key) => { if (values[key] === '') delete values[key]; });
            if (values.effective_at) values.effective_at = new Date(values.effective_at).toISOString();
            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            try {
                await request(endpoint, { method: 'POST', body: values, idempotent: true });
                setNotice(document.querySelector('#form-message'), 'Enregistrement effectué.', 'success');
                window.location.assign(successPath);
            } catch (error) {
                setNotice(document.querySelector('#form-message'), error.message, 'error');
                button.disabled = false;
            }
        });
    }

    async function movements(container) {
        const createLink = '<a class="button button-primary" href="/movements/create">＋ Soumettre un mouvement</a>';
        container.innerHTML = `${heading('Mouvements', 'Suivez les transferts, affectations, retours et sorties.', createLink)}
            <div id="list-message"></div><section class="panel"><div id="movement-table"></div></section>`;
        await loadInto(document.querySelector('#list-message'), '/movements?per_page=20', (payload) => {
            table(document.querySelector('#movement-table'), rowsFrom(payload), [
                ['id', 'Référence'], ['asset_id', 'Bien'], ['type', 'Type'], ['status', 'Statut'], ['effective_at', 'Date d’effet'],
            ], (row) => {
                const group = document.createElement('div');
                group.className = 'table-actions';
                if (row.status === 'pending') {
                    group.append(actionButton('Approuver', 'approve-movement', row.id));
                    group.append(actionButton('Rejeter', 'reject-movement', row.id, 'danger'));
                } else {
                    group.textContent = '—';
                }
                return group;
            });
        });
    }

    async function inventories(container) {
        const createLink = '<a class="button button-primary" href="/inventories/create">＋ Créer une campagne</a>';
        container.innerHTML = `${heading('Inventaires', 'Planifiez les campagnes et suivez leur progression.', createLink)}
            <div id="list-message"></div><section class="panel"><div id="inventory-table"></div></section>`;
        await loadInto(document.querySelector('#list-message'), '/inventory-campaigns?per_page=20', (payload) => {
            table(document.querySelector('#inventory-table'), rowsFrom(payload), [
                ['label', 'Campagne'], ['status', 'Statut'], ['start_date', 'Début'], ['progress.checked', 'Contrôlés'], ['progress.expected', 'Attendus'],
            ], (row) => {
                const link = document.createElement('a');
                link.className = 'button button-small button-secondary';
                link.href = `/inventories/${encodeURIComponent(row.id)}`;
                link.textContent = 'Détail';
                return link;
            });
        });
    }

    async function history(container) {
        container.innerHTML = `${heading('Historique', 'Journal centralisé des opérations métier.')}
            <div id="list-message"></div><section class="panel"><div id="history-table"></div></section>`;
        await loadInto(document.querySelector('#list-message'), '/history?per_page=50', (payload) => {
            table(document.querySelector('#history-table'), rowsFrom(payload), [
                ['event_type', 'Événement'], ['source', 'Service'], ['aggregate_type', 'Type'], ['aggregate_id', 'Référence'], ['actor_id', 'Utilisateur'], ['occurred_at', 'Date'],
            ]);
        });
    }

    async function reports(container) {
        container.innerHTML = `${heading('Rapports', 'Consultez les indicateurs et exportez les données autorisées.')}
            <div id="report-message"></div><section class="panel"><div class="panel-heading"><div><h2>Indicateurs du patrimoine</h2><p>Synthèse fournie par le service de rapports</p></div>
                <button class="button button-secondary" type="button" data-action="download-report">Exporter les biens (CSV)</button></div>
                <div class="panel-content" id="report-summary"></div></section>`;
        await loadInto(document.querySelector('#report-message'), '/reports/summary', (summary) => {
            const items = [
                ['Biens enregistrés', summary.assets_total], ['Biens actifs', summary.assets_active],
                ['Mouvements en attente', summary.pending_movements], ['Écarts d’inventaire', summary.inventory_discrepancies],
                ['Valeur déclarée', summary.declared_value == null ? '—' : `${summary.declared_value} ${summary.currency || ''}`],
            ];
            document.querySelector('#report-summary').innerHTML = `<div class="metric-grid">${items.map(([label, value]) => `<article class="metric-card"><span class="metric-label">${escapeHtml(label)}</span><strong class="metric-value">${escapeHtml(value ?? '—')}</strong></article>`).join('')}</div>`;
        });
    }

    async function assetDetail(container) {
        const id = decodeURIComponent(window.location.pathname.split('/').pop());
        container.innerHTML = `${heading('Fiche du bien', 'Détail et données du registre.')}<div id="detail-message"></div><section class="panel"><div class="panel-content" id="asset-detail"></div></section>`;
        await loadInto(document.querySelector('#detail-message'), `/assets/${encodeURIComponent(id)}`, (payload) => details(document.querySelector('#asset-detail'), payload.data || payload));
    }

    async function inventoryDetail(container) {
        const id = decodeURIComponent(window.location.pathname.split('/').pop());
        container.innerHTML = `${heading('Détail de la campagne', 'Progression et vérifications de la campagne.') }
            <div id="detail-message"></div><section class="panel"><div class="panel-content" id="inventory-detail"></div></section>
            <h2 style="margin:22px 0 12px;font-size:15px">Enregistrer une vérification</h2><div id="form-message"></div>
            <form class="form-panel" id="check-form"><div class="form-grid">
                ${formField('UUID du bien', 'asset_id', 'text', { required: true })}
                ${formField('Résultat', 'result', 'text', { required: true, options: [['present', 'Présent'], ['absent', 'Absent'], ['condition_mismatch', 'État différent'], ['location_mismatch', 'Emplacement différent']] })}
                ${formField('Commentaire', 'comment', 'textarea', { full: true })}
            </div><div class="form-actions"><button class="button button-primary" type="submit">Enregistrer le contrôle</button></div></form>`;
        await loadInto(document.querySelector('#detail-message'), `/inventory-campaigns/${encodeURIComponent(id)}`, (payload) => details(document.querySelector('#inventory-detail'), payload.data || payload));
        document.querySelector('#check-form').addEventListener('submit', async (event) => {
            event.preventDefault();
            const values = Object.fromEntries(new FormData(event.currentTarget));
            try {
                await request(`/inventory-campaigns/${encodeURIComponent(id)}/checks`, { method: 'POST', body: values, idempotent: true });
                setNotice(document.querySelector('#form-message'), 'Vérification enregistrée.', 'success');
                event.currentTarget.reset();
            } catch (error) {
                setNotice(document.querySelector('#form-message'), error.message, 'error');
            }
        });
    }

    function details(container, item) {
        if (!item || typeof item !== 'object') {
            container.textContent = 'Aucun détail disponible.';
            return;
        }
        const grid = document.createElement('div');
        grid.className = 'details-grid';
        Object.entries(item).forEach(([key, value]) => {
            const cell = document.createElement('div');
            cell.className = 'detail-item';
            const label = document.createElement('span');
            label.textContent = key.replaceAll('_', ' ');
            const content = document.createElement('strong');
            content.textContent = value && typeof value === 'object' ? JSON.stringify(value) : String(value ?? '—');
            cell.append(label, content);
            grid.append(cell);
        });
        container.replaceChildren(grid);
    }

    async function render() {
        if (!state.token) {
            showLogin();
            return;
        }
        try {
            state.user = await request('/auth/me');
        } catch (error) {
            state.token = null;
            state.user = null;
            sessionStorage.removeItem(tokenKey);
            showLogin(error.message);
            return;
        }

        const container = shell();
        const path = window.location.pathname.replace(/\/+$/, '') || '/';
        if (path === '/') return dashboard(container);
        if (path === '/assets') return assets(container);
        if (path === '/assets/create') return createForm(container, {
            title: 'Enregistrer un bien',
            lead: 'Les catégories, services et sites disponibles sont chargés depuis les référentiels.',
            endpoint: '/assets',
            successPath: '/assets',
            referenceLists: true,
            fields: [
                { label: 'Catégorie', name: 'category_id', reference: 'categories' },
                { label: 'Service responsable', name: 'service_id', reference: 'services' },
                { label: 'Site', name: 'site_id', reference: 'sites' },
                formField('Libellé', 'label', 'text', { required: true }),
                formField('État', 'condition', 'text', { required: true, options: [['good', 'Bon'], ['fair', 'Moyen'], ['poor', 'Mauvais'], ['unusable', 'Inutilisable']] }),
                formField('Date d’acquisition', 'acquisition_date', 'date'),
                formField('Valeur d’acquisition', 'acquisition_value', 'number', { step: '0.01' }),
                formField('Devise (ex. XOF)', 'currency', 'text'),
                formField('Description', 'description', 'textarea', { full: true }),
            ],
        });
        if (path.startsWith('/assets/')) return assetDetail(container);
        if (path === '/movements') return movements(container);
        if (path === '/movements/create') return createForm(container, {
            title: 'Soumettre un mouvement',
            lead: 'La demande sera transmise au service des biens pour validation.',
            endpoint: '/movements',
            successPath: '/movements',
            fields: [
                formField('UUID du bien', 'asset_id', 'text', { required: true }),
                formField('Type', 'type', 'text', { required: true, options: [['transfer', 'Transfert'], ['assignment', 'Affectation'], ['return', 'Retour'], ['disposal', 'Sortie']] }),
                formField('Date d’effet', 'effective_at', 'datetime-local', { required: true }),
                formField('UUID service de destination', 'destination_service_id', 'text'),
                formField('UUID site de destination', 'destination_site_id', 'text'),
                formField('Motif', 'reason', 'textarea', { required: true, full: true }),
            ],
        });
        if (path === '/inventories') return inventories(container);
        if (path === '/inventories/create') return createForm(container, {
            title: 'Créer une campagne',
            lead: 'Définissez une campagne d’inventaire pour une période ou un périmètre.',
            endpoint: '/inventory-campaigns',
            successPath: '/inventories',
            fields: [
                formField('Libellé', 'label', 'text', { required: true }),
                formField('Date de début', 'start_date', 'date', { required: true }),
                formField('Date de fin', 'end_date', 'date'),
                formField('UUID service (facultatif)', 'service_id', 'text'),
                formField('UUID site (facultatif)', 'site_id', 'text'),
            ],
        });
        if (path.startsWith('/inventories/')) return inventoryDetail(container);
        if (path === '/history') return history(container);
        if (path === '/reports') return reports(container);
        return dashboard(container);
    }

    app.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;
        if (button.dataset.action === 'logout') return logout();
        if (button.dataset.action === 'download-report') {
            try {
                const headers = new Headers({ Authorization: `Bearer ${state.token}`, Accept: 'text/csv' });
                const response = await fetch(`${apiRoot}/reports/assets.csv`, { headers });
                if (!response.ok) throw new Error(`L’export a échoué (erreur ${response.status}).`);
                const url = URL.createObjectURL(await response.blob());
                const link = document.createElement('a');
                link.href = url;
                link.download = 'biens.csv';
                link.click();
                URL.revokeObjectURL(url);
            } catch (error) {
                const container = document.querySelector('#report-message');
                if (container) setNotice(container, error.message, 'error');
            }
        }
        if (button.dataset.action === 'approve-movement' || button.dataset.action === 'reject-movement') {
            const action = button.dataset.action === 'approve-movement' ? 'approve' : 'reject';
            const reason = action === 'reject' ? window.prompt('Motif du rejet') : null;
            if (action === 'reject' && !reason) return;
            try {
                await request(`/movements/${encodeURIComponent(button.dataset.id)}/${action}`, {
                    method: 'POST',
                    ...(reason ? { body: { reason } } : {}),
                    idempotent: true,
                });
                render();
            } catch (error) {
                const container = document.querySelector('#list-message');
                if (container) setNotice(container, error.message, 'error');
            }
        }
    });

    render();
})();
