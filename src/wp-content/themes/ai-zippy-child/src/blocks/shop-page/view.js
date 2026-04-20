document.addEventListener("click", (event) => {
	const open = event.target.closest("[data-shop-filter-open]");
	const close = event.target.closest("[data-shop-filter-close]");
	const drawer = event.target.closest("[data-shop-filter-drawer]");

	if (!open && !close && !drawer) {
		return;
	}

	const shop = event.target.closest(".wp-block-ai-zippy-child-shop-page");

	if (!shop) {
		return;
	}

	if (open) {
		shop.classList.add("is-filter-open");
		document.documentElement.classList.add("has-shop-filter-open");
		return;
	}

	if (close || event.target === drawer) {
		shop.classList.remove("is-filter-open");
		document.documentElement.classList.remove("has-shop-filter-open");
	}
});

document.addEventListener("change", (event) => {
	const shop = event.target.closest(".wp-block-ai-zippy-child-shop-page");

	if (!shop) {
		return;
	}

	if (event.target.matches("[data-shop-sort]")) {
		event.target.form?.submit();
		return;
	}

	if (!event.target.matches("[data-shop-filter-input]")) {
		return;
	}

	const forms = shop.querySelectorAll("[data-shop-filter-form]");
	let count = 0;

	forms.forEach((form) => {
		const formCount = form.querySelectorAll("[data-shop-filter-input]:checked").length;
		count = Math.max(count, formCount);
	});

	shop.querySelectorAll("[data-filter-count]").forEach((badge) => {
		badge.textContent = String(count);
		badge.hidden = count === 0;
	});
});
