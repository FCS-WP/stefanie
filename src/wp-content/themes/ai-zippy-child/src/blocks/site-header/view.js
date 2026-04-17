document.addEventListener("click", (event) => {
	const toggle = event.target.closest("[data-site-header-toggle]");
	const close = event.target.closest("[data-site-header-close]");
	const drawerLink = event.target.closest(".site-header__drawer a");

	if (!toggle && !close && !drawerLink) {
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

	header.classList.remove("is-menu-open");
	document.documentElement.classList.remove("has-site-header-menu");

	const headerToggle = header.querySelector("[data-site-header-toggle]");

	if (headerToggle) {
		headerToggle.setAttribute("aria-expanded", "false");
	}
});
