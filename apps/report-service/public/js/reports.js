const menuToggle = document.querySelector('[data-menu-toggle]');
const sidebar = document.querySelector('#sidebar');

menuToggle?.addEventListener('click', () => {
    const isOpen = sidebar.classList.toggle('is-open');
    menuToggle.setAttribute('aria-expanded', String(isOpen));
    menuToggle.setAttribute('aria-label', isOpen ? 'Fermer la navigation' : 'Ouvrir la navigation');
});

const reportForm = document.querySelector('[data-report-form]');
const filterMessage = document.querySelector('[data-filter-message]');
const chartPeriod = document.querySelector('[data-chart-period]');

reportForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const from = new Date(reportForm.elements.from.value);
    const to = new Date(reportForm.elements.to.value);
    const service = reportForm.elements.service.value || 'tous les services';
    const category = reportForm.elements.category.value || 'toutes les catégories';

    if (Number.isNaN(from.getTime()) || Number.isNaN(to.getTime()) || from > to) {
        filterMessage.hidden = false;
        filterMessage.textContent = 'Vérifiez les dates : la date de début doit précéder la date de fin.';
        return;
    }

    const formatMonth = new Intl.DateTimeFormat('fr', { month: 'short' });
    chartPeriod.textContent = `${formatMonth.format(from)} – ${formatMonth.format(to)} ${to.getFullYear()}`;
    filterMessage.hidden = false;
    filterMessage.textContent = `Aperçu de démonstration pour ${service}, ${category}. Les indicateurs ne sont pas recalculés depuis des données réelles.`;
});

document.querySelector('[data-export]')?.addEventListener('click', () => {
    const rows = [
        ['Indicateur', 'Valeur'],
        ['Total des biens', document.querySelector('[data-export-value="total"]').innerText],
        ['Biens en service', document.querySelector('[data-export-value="active"]').innerText],
        ['Mouvements validés', document.querySelector('[data-export-value="movements"]').innerText],
        ['Écarts d’inventaire', document.querySelector('[data-export-value="discrepancies"]').innerText],
    ];
    const csv = rows.map((row) => row.map((value) => `"${value.replaceAll('"', '""')}"`).join(',')).join('\n');
    const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
    const link = document.createElement('a');
    link.href = url;
    link.download = 'rapport-patrimoine.csv';
    link.click();
    URL.revokeObjectURL(url);
});