const menuToggle = document.querySelector('[data-menu-toggle]');
const sidebar = document.querySelector('#sidebar');

menuToggle?.addEventListener('click', () => {
	const isOpen = sidebar?.classList.toggle('is-open') ?? false;
	menuToggle.setAttribute('aria-expanded', String(isOpen));
	menuToggle.setAttribute('aria-label', isOpen ? 'Fermer la navigation' : 'Ouvrir la navigation');
});

const inventoryFilter = document.querySelector('[data-inventory-filter]');
const inventorySearch = document.querySelector('[data-inventory-search]');
const inventoryRows = [...document.querySelectorAll('.inventory-table tbody tr')];

if (inventoryRows.length) {
	const filterInventory = () => {
		const query = inventorySearch?.querySelector('input').value.trim().toLocaleLowerCase() ?? '';
		const result = inventoryFilter?.value === 'Tous les résultats' ? '' : inventoryFilter?.value.toLocaleLowerCase();

		inventoryRows.forEach((row) => {
			const cells = [...row.cells];
			const matchesQuery = cells[0].innerText.toLocaleLowerCase().includes(query);
			const matchesResult = !result || cells[2].innerText.toLocaleLowerCase().includes(result);
			row.hidden = !(matchesQuery && matchesResult);
		});
	};

	inventorySearch?.addEventListener('submit', (event) => event.preventDefault());
	inventoryFilter?.addEventListener('change', filterInventory);
	inventorySearch?.querySelector('input').addEventListener('input', filterInventory);
	filterInventory();
}

document.querySelectorAll('[data-export]').forEach((button) => {
	button.addEventListener('click', () => {
		const rows = [...document.querySelectorAll('.data-table tbody tr')];
		const csv = rows.map((row) => [...row.cells]
			.map((cell) => `"${cell.innerText.trim().replaceAll('"', '""')}"`)
			.join(',')).join('\n');
		const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
		const link = document.createElement('a');
		link.href = url;
		link.download = 'patrimoine-export.csv';
		link.click();
		URL.revokeObjectURL(url);
	});
});

document.querySelector('[data-print]')?.addEventListener('click', () => window.print());
