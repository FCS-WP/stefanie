document.addEventListener("click", (event) => {
	const toggle = event.target.closest("[data-site-header-toggle]");
	const close = event.target.closest("[data-site-header-close]");
	const drawerLink = event.target.closest(".site-header__drawer a");
	const megaTrigger = event.target.closest(".site-header__nav-item--mega > a");

	if (!toggle && !close && !drawerLink && !megaTrigger) {
		document
			.querySelectorAll(".site-header__nav-item--mega.is-open")
			.forEach((item) => item.classList.remove("is-open"));
		return;
	}

	const header = event.target.closest(".wp-block-ai-zippy-child-site-header");

	if (!header) {
		return;
	}

	if (toggle) {
		const isOpen = header.classList.toggle("is-menu-open");
		toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
		document.documentElement.classList.toggle("has-site-header-menu", isOpen);
		return;
	}

	if (megaTrigger && !event.target.closest(".site-header__drawer")) {
		const megaItem = megaTrigger.closest(".site-header__nav-item--mega");

		if (megaItem && !megaItem.classList.contains("is-open")) {
			event.preventDefault();
			header
				.querySelectorAll(".site-header__nav-item--mega.is-open")
				.forEach((item) => item.classList.remove("is-open"));
			megaItem.classList.add("is-open");
			return;
		}
	}

	header.classList.remove("is-menu-open");
	document.documentElement.classList.remove("has-site-header-menu");

	const headerToggle = header.querySelector("[data-site-header-toggle]");

	if (headerToggle) {
		headerToggle.setAttribute("aria-expanded", "false");
	}
});
