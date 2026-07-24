const normalizeText = (value) =>
	(value || '')
		.toString()
		.normalize('NFD')
		.replace(/[\u0300-\u036f]/g, '')
		.toLowerCase()
		.trim();

const getSelectedLabel = (select) => {
	const selectedOption = select.options[select.selectedIndex];
	if (!selectedOption || selectedOption.value === '') {
		return '';
	}

	return selectedOption.textContent.trim();
};

const initSearchableSelect = (select) => {
	if (!select || select.dataset.searchableReady === 'true' || select.disabled) {
		return;
	}

	const limit = Number.parseInt(select.dataset.searchLimit || '10', 10);
	const maxItems = Number.isFinite(limit) && limit > 0 ? limit : 10;
	const placeholder = select.dataset.searchPlaceholder || 'Escribe para buscar...';

	const wrapper = document.createElement('div');
	wrapper.className = 'searchable-select';

	const input = document.createElement('input');
	input.type = 'text';
	input.className = 'input-control w-full searchable-select-input';
	input.placeholder = placeholder;
	input.autocomplete = 'off';

	const dropdown = document.createElement('div');
	dropdown.className = 'searchable-select-dropdown hidden';

	const list = document.createElement('ul');
	list.className = 'searchable-select-list';

	const meta = document.createElement('p');
	meta.className = 'searchable-select-meta hidden';

	dropdown.appendChild(list);
	dropdown.appendChild(meta);

	select.parentNode?.insertBefore(wrapper, select);
	wrapper.appendChild(input);
	wrapper.appendChild(dropdown);
	wrapper.appendChild(select);

	select.classList.add('searchable-select-native');
	select.dataset.searchableReady = 'true';

	const renderOptions = (queryText = '') => {
		const query = normalizeText(queryText);
		const options = Array.from(select.options).filter((option) => {
			if (option.disabled || option.hidden) {
				return false;
			}

			if (option.value === '') {
				return !select.required && query === '';
			}

			return true;
		});

		const startsWithMatches = [];
		const includesMatches = [];

		options.forEach((option) => {
			const label = option.textContent.trim();
			const normalizedLabel = normalizeText(label);

			if (!query) {
				startsWithMatches.push(option);
				return;
			}

			if (normalizedLabel.startsWith(query)) {
				startsWithMatches.push(option);
				return;
			}

			if (normalizedLabel.includes(query)) {
				includesMatches.push(option);
			}
		});

		const matches = [...startsWithMatches, ...includesMatches];
		const visibleMatches = matches.slice(0, maxItems);

		list.innerHTML = '';

		if (visibleMatches.length === 0) {
			const empty = document.createElement('li');
			empty.className = 'searchable-select-empty';
			empty.textContent = 'Sin resultados';
			list.appendChild(empty);
			meta.classList.add('hidden');
			return;
		}

		visibleMatches.forEach((option) => {
			const item = document.createElement('li');
			item.className = 'searchable-select-item';
			if (option.selected) {
				item.classList.add('is-selected');
			}

			item.textContent = option.textContent.trim();
			item.addEventListener('mousedown', (event) => {
				event.preventDefault();
				select.value = option.value;
				select.dispatchEvent(new Event('change', { bubbles: true }));
				dropdown.classList.add('hidden');
			});

			list.appendChild(item);
		});

		if (matches.length > maxItems) {
			meta.classList.remove('hidden');
			meta.textContent = `Mostrando ${maxItems} de ${matches.length} resultados`;
		} else {
			meta.classList.add('hidden');
		}
	};

	const open = () => {
		dropdown.classList.remove('hidden');
		renderOptions(input.value.trim() === '' ? '' : input.value);
	};

	const close = () => {
		dropdown.classList.add('hidden');
	};

	const syncInputFromSelect = () => {
		input.value = getSelectedLabel(select);
	};

	syncInputFromSelect();

	input.addEventListener('focus', open);
	input.addEventListener('click', open);
	input.addEventListener('input', () => {
		renderOptions(input.value);
		dropdown.classList.remove('hidden');
	});

	select.addEventListener('change', () => {
		syncInputFromSelect();
		renderOptions(input.value);
	});

	document.addEventListener('click', (event) => {
		if (!wrapper.contains(event.target)) {
			close();
		}
	});
};

document.querySelectorAll('select[data-searchable="true"]').forEach((select) => {
	initSearchableSelect(select);
});
