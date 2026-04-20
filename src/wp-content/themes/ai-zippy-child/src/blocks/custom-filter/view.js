document.addEventListener("submit", (event) => {
	const form = event.target.closest(".wp-block-ai-zippy-child-custom-filter");

	if (!form) {
		return;
	}

	const groups = new Map();

	form.querySelectorAll("[data-custom-filter-name]:checked").forEach((input) => {
		const name = input.dataset.customFilterName;

		if (!name) {
			return;
		}

		if (!groups.has(name)) {
			groups.set(name, []);
		}

		groups.get(name).push(input.value);
		input.disabled = true;
	});

	form.querySelectorAll("[data-custom-filter-generated]").forEach((input) => {
		input.remove();
	});

	groups.forEach((values, name) => {
		const hidden = document.createElement("input");
		hidden.type = "hidden";
		hidden.name = name;
		hidden.value = values.join(",");
		hidden.dataset.customFilterGenerated = "true";
		form.append(hidden);
	});
});
