const menuToggle = document.querySelector('[data-menu-toggle]');
const sidebar = document.querySelector('#sidebar');

menuToggle?.addEventListener('click', () => {
	const isOpen = sidebar?.classList.toggle('is-open') ?? false;
	menuToggle.setAttribute('aria-expanded', String(isOpen));
	menuToggle.setAttribute('aria-label', isOpen ? 'Fermer la navigation' : 'Ouvrir la navigation');
});

const assetFilters = document.querySelector('[data-filter-form]');
const assetRows = [...document.querySelectorAll('.asset-table tbody tr')];

if (assetFilters && assetRows.length) {
	const filterRows = () => {
		const query = assetFilters.querySelector('[name="q"]').value.trim().toLocaleLowerCase();
		const category = assetFilters.querySelector('[name="category"]').value.toLocaleLowerCase();
		const state = assetFilters.querySelector('[name="state"]').value.toLocaleLowerCase();
		const service = assetFilters.querySelector('[name="service"]').value.toLocaleLowerCase();
		let visibleCount = 0;

		assetRows.forEach((row) => {
			const cells = [...row.cells];
			const matches = cells.map((cell) => cell.innerText.toLocaleLowerCase());
			const isVisible = matches.join(' ').includes(query)
				&& (!category || matches[2].includes(category))
				&& (!state || matches[4].includes(state))
				&& (!service || matches[3].includes(service));

			row.hidden = !isVisible;
			visibleCount += Number(isVisible);
		});

		const resultCount = document.querySelector('[data-result-count]');
		if (resultCount && (query || category || state || service)) {
			resultCount.textContent = `${visibleCount} résultat${visibleCount === 1 ? '' : 's'} sur cette page`;
		} else if (resultCount) {
			resultCount.textContent = '2 486 résultats';
		}
	};

	assetFilters.addEventListener('submit', (event) => event.preventDefault());
	assetFilters.addEventListener('input', filterRows);
	assetFilters.addEventListener('change', filterRows);
	filterRows();
}

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

document.querySelectorAll('[data-demo-form]').forEach((form) => {
	form.addEventListener('submit', (event) => {
		event.preventDefault();
		const submitButton = form.querySelector('[type="submit"]');
		if (submitButton) {
			submitButton.textContent = 'Envoi indisponible';
			submitButton.disabled = true;
		}
	});
});

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
