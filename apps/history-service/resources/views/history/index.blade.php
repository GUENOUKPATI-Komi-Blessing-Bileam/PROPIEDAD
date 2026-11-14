@extends('layouts.history')

@section('title', 'Journal d’audit')

@section('content')
@php
    $events = [
        [
            'time' => 'Aujourd’hui, 10:42:18',
            'type' => 'asset.created',
            'source' => 'asset-service',
            'aggregate' => 'Bien · INV-2026-0482',
            'actor' => 'M. Diallo',
            'correlation' => 'c7b2…a91f',
            'payload' => ['code_inventaire' => 'INV-2026-0482', 'libelle' => 'Ordinateur portable Dell Latitude'],
        ],
        [
            'time' => 'Aujourd’hui, 10:18:04',
            'type' => 'movement.submitted',
            'source' => 'asset-service',
            'aggregate' => 'Mouvement · MVT-2026-0317',
            'actor' => 'A. Manager',
            'correlation' => '7d9a…f021',
            'payload' => ['type' => 'transfert', 'statut' => 'en_attente', 'bien' => 'INV-2026-0482'],
        ],
        [
            'time' => 'Aujourd’hui, 09:56:41',
            'type' => 'inventory.check.recorded',
            'source' => 'asset-service',
            'aggregate' => 'Inventaire · INV-2026-0479',
            'actor' => 'S. Traoré',
            'correlation' => '1ef0…346b',
            'payload' => ['resultat' => 'etat_a_corriger', 'site_observe' => 'Parc automobile'],
        ],
        [
            'time' => 'Aujourd’hui, 09:42:09',
            'type' => 'asset.updated',
            'source' => 'asset-service',
            'aggregate' => 'Bien · INV-2026-0442',
            'actor' => 'A. Koné',
            'correlation' => '921c…0d4a',
            'payload' => ['champ' => 'etat', 'ancienne_valeur' => 'a_reparer', 'nouvelle_valeur' => 'en_service'],
        ],
        [
            'time' => 'Hier, 16:18:32',
            'type' => 'movement.approved',
            'source' => 'asset-service',
            'aggregate' => 'Mouvement · MVT-2026-0309',
            'actor' => 'A. Manager',
            'correlation' => '5ac4…ea38',
            'payload' => ['statut' => 'valide', 'bien' => 'INV-2026-0317'],
        ],
        [
            'time' => 'Hier, 14:06:55',
            'type' => 'auth.session.created',
            'source' => 'auth-service',
            'aggregate' => 'Session · 8b35e2a1',
            'actor' => 'A. Manager',
            'correlation' => '97fe…bc20',
            'payload' => ['methode' => 'mot_de_passe', 'resultat' => 'succes'],
        ],
    ];
@endphp

<div class="page-heading">
    <div><p class="eyebrow">Traçabilité du patrimoine</p><h1>Journal d’audit</h1><p class="page-lead">Consultez les événements enregistrés par les services de la plateforme.</p></div>
    <button class="button button-primary" type="button" data-export><span aria-hidden="true">↓</span> Exporter CSV</button>
</div>

<section class="metrics" aria-label="Synthèse du journal">
    <article class="metric"><span>Événements enregistrés</span><strong>12 846</strong><small>Depuis le début de l’exercice</small></article>
    <article class="metric"><span>Sources actives</span><strong>4</strong><small>Services connectés</small></article>
    <article class="metric"><span>Événements aujourd’hui</span><strong>128</strong><small>Dernière réception il y a 18 s</small></article>
</section>

<form class="filters" data-event-filters>
    <label class="field field-search"><span>Recherche</span><input type="search" name="q" placeholder="Événement, ressource, acteur…"></label>
    <label class="field"><span>Source</span><select name="source"><option value="">Toutes les sources</option><option value="asset-service">Asset service</option><option value="auth-service">Auth service</option><option value="report-service">Report service</option><option value="gateway">Gateway</option></select></label>
    <label class="field"><span>Type d’événement</span><select name="type"><option value="">Tous les événements</option><option value="asset">Biens</option><option value="movement">Mouvements</option><option value="inventory">Inventaires</option><option value="auth">Authentification</option></select></label>
    <label class="field"><span>Période</span><select name="period"><option value="">Toutes les dates</option><option value="today">Aujourd’hui</option><option value="yesterday">Hier</option></select></label>
    <button class="button button-soft" type="submit">Filtrer</button>
</form>

<section class="panel event-panel">
    <div class="panel-heading"><div><h2>Événements récents</h2><p data-event-count>{{ count($events) }} événements affichés · Triés du plus récent au plus ancien</p></div><span class="live-state"><i></i> Flux actif</span></div>
    <div class="table-scroll">
        <table class="event-table">
            <thead><tr><th>Date et heure</th><th>Événement</th><th>Source</th><th>Ressource</th><th>Acteur</th><th>Corrélation</th><th><span class="sr-only">Détails</span></th></tr></thead>
            <tbody>
            @foreach ($events as $index => $event)
                <tr data-event-row data-date="{{ str_contains($event['time'], 'Aujourd’hui') ? 'today' : 'yesterday' }}" data-type="{{ Str::before($event['type'], '.') }}" data-source="{{ $event['source'] }}">
                    <td class="event-date">{{ $event['time'] }}</td>
                    <td><span class="event-name">{{ $event['type'] }}</span><small class="event-kind">{{ Str::before($event['type'], '.') }}</small></td>
                    <td><span class="source-tag">{{ $event['source'] }}</span></td>
                    <td class="aggregate-cell">{{ $event['aggregate'] }}</td>
                    <td>{{ $event['actor'] }}</td>
                    <td><code>{{ $event['correlation'] }}</code></td>
                    <td><button class="icon-action" type="button" aria-label="Afficher le payload de l’événement {{ $event['type'] }}" aria-expanded="false" data-detail-toggle="{{ $index }}">⌄</button></td>
                </tr>
                <tr class="payload-row" data-payload-row="{{ $index }}" hidden><td colspan="7"><div class="payload"><strong>Payload</strong><pre>{{ json_encode($event['payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre></div></td></tr>
            @endforeach
                <tr data-empty-row hidden><td colspan="7" class="empty-state">Aucun événement ne correspond à ces filtres.</td></tr>
            </tbody>
        </table>
    </div>
    <div class="table-footer"><span data-pagination-count>Affichage de 1 à {{ count($events) }} sur 12 846 événements</span><nav class="pagination" aria-label="Pagination"><button type="button" aria-label="Page précédente" disabled>‹</button><button type="button" class="current" aria-current="page">1</button><button type="button" aria-label="Page 2">2</button><button type="button" aria-label="Page 3">3</button><button type="button" aria-label="Page suivante">›</button></nav></div>
</section>

<script>
    const filterForm = document.querySelector('[data-event-filters]');
    const eventRows = [...document.querySelectorAll('[data-event-row]')];

    const applyFilters = () => {
        const query = filterForm.elements.q.value.trim().toLocaleLowerCase();
        const source = filterForm.elements.source.value;
        const type = filterForm.elements.type.value;
        const period = filterForm.elements.period.value;
        let visible = 0;

        eventRows.forEach((row) => {
            const text = row.innerText.toLocaleLowerCase();
            const matches = text.includes(query)
                && (!source || row.dataset.source === source)
                && (!type || row.dataset.type === type)
                && (!period || row.dataset.date === period);
            row.hidden = !matches;
            document.querySelector(`[data-payload-row="${row.querySelector('[data-detail-toggle]').dataset.detailToggle}"]`).hidden = !matches
                || !row.querySelector('[data-detail-toggle]').getAttribute('aria-expanded').includes('true');
            visible += Number(matches);
        });

        document.querySelector('[data-event-count]').textContent = `${visible} événement${visible === 1 ? '' : 's'} affiché${visible === 1 ? '' : 's'} · Triés du plus récent au plus ancien`;
        document.querySelector('[data-empty-row]').hidden = visible > 0;
        document.querySelector('[data-pagination-count]').textContent = visible === 0
            ? 'Aucun événement à afficher'
            : `${visible} événement${visible === 1 ? '' : 's'} affiché${visible === 1 ? '' : 's'} sur 12 846`;
    };

    filterForm.addEventListener('submit', (event) => event.preventDefault());
    filterForm.addEventListener('input', applyFilters);
    filterForm.addEventListener('change', applyFilters);

    document.querySelectorAll('[data-detail-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', String(!expanded));
            button.textContent = expanded ? '⌄' : '⌃';
            document.querySelector(`[data-payload-row="${button.dataset.detailToggle}"]`).hidden = expanded;
        });
    });

    document.querySelector('[data-export]').addEventListener('click', () => {
        const rows = eventRows.filter((row) => !row.hidden);
        const csv = rows.map((row) => [...row.cells].slice(0, 6)
            .map((cell) => `"${cell.innerText.trim().replaceAll('"', '""')}"`)
            .join(',')).join('\n');
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
        link.download = 'journal-audit.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    });
</script>
@endsection