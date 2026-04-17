const now = () => Date.now();

const getCookie = (name) => {
	const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`));
	return match ? decodeURIComponent(match[1]) : "";
};

const setCookie = (name, value, days) => {
	const expires = new Date(now() + days * 864e5).toUTCString();
	document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
};

const storageGet = (mode, key) => {
	if (mode === "cookie") {
		return getCookie(key);
	}

	if (mode === "sessionStorage") {
		return window.sessionStorage.getItem(key);
	}

	if (mode === "localStorage") {
		return window.localStorage.getItem(key);
	}

	return "";
};

const storageSet = (mode, key, value, days) => {
	if (mode === "none") {
		return;
	}

	if (mode === "cookie") {
		setCookie(key, value, days);
		return;
	}

	if (mode === "sessionStorage") {
		window.sessionStorage.setItem(key, value);
		return;
	}

	window.localStorage.setItem(key, value);
};

const isSuppressed = (block, suppressKey) => {
	const mode = block.dataset.storageMode || "localStorage";
	const value = storageGet(mode, suppressKey);

	if (!value) {
		return false;
	}

	if (mode === "sessionStorage") {
		return true;
	}

	return Number(value) > now();
};

const initPopup = (block) => {
	const popupId = block.dataset.popupId || "site-popup";
	const overlay = block.querySelector(".site-popup__overlay");
	const dialog = block.querySelector(".site-popup__dialog");
	const closeButton = block.querySelector(".site-popup__close");
	const dismissButton = block.querySelector(".site-popup__dismiss");
	const launcher = block.querySelector(".site-popup__launcher");
	const mode = block.dataset.storageMode || "localStorage";
	const suppressDays = Number(block.dataset.suppressDays) || 14;
	const suppressKey = `az-popup:${popupId}:suppressed`;
	const visitedKey = `az-popup:${popupId}:visited:${window.location.pathname}`;
	let hasOpened = false;

	if (!overlay || !dialog || isSuppressed(block, suppressKey)) {
		return;
	}

	const markVisited = () => storageSet(mode, visitedKey, String(now() + 365 * 864e5), 365);
	const hasVisited = () => Boolean(storageGet(mode, visitedKey));
	const remember = () => storageSet(mode, suppressKey, String(now() + suppressDays * 864e5), suppressDays);

	const open = () => {
		if (hasOpened || isSuppressed(block, suppressKey)) {
			return;
		}

		hasOpened = true;
		overlay.hidden = false;
		dialog.hidden = false;
		block.classList.add("is-open");
		document.documentElement.classList.add("has-site-popup");
		dialog.focus?.();
	};

	const close = (shouldRemember = block.dataset.rememberOnClose === "true") => {
		overlay.hidden = true;
		dialog.hidden = true;
		block.classList.remove("is-open");
		document.documentElement.classList.remove("has-site-popup");

		if (shouldRemember) {
			remember();
		}
	};

	const shouldSkipForVisitRule = () => {
		const visited = hasVisited();

		if (block.dataset.firstVisitOnly === "true" && visited) {
			return true;
		}

		if (block.dataset.skipFirstVisit === "true" && !visited) {
			markVisited();
			return true;
		}

		markVisited();
		return false;
	};

	const schedule = () => {
		if (shouldSkipForVisitRule()) {
			return;
		}

		const trigger = block.dataset.trigger || "delay";

		if (trigger === "immediate") {
			open();
			return;
		}

		if (trigger === "scroll") {
			const scrollPercent = Number(block.dataset.scrollPercent) || 40;
			const onScroll = () => {
				const doc = document.documentElement;
				const available = doc.scrollHeight - window.innerHeight;
				const progress = available > 0 ? (window.scrollY / available) * 100 : 100;

				if (progress >= scrollPercent) {
					window.removeEventListener("scroll", onScroll);
					open();
				}
			};
			window.addEventListener("scroll", onScroll, { passive: true });
			onScroll();
			return;
		}

		if (trigger === "exit") {
			const onExit = (event) => {
				if (event.clientY <= 0) {
					document.removeEventListener("mouseleave", onExit);
					open();
				}
			};
			document.addEventListener("mouseleave", onExit);
			return;
		}

		if (trigger === "button") {
			return;
		}

		window.setTimeout(open, (Number(block.dataset.delay) || 0) * 1000);
	};

	closeButton?.addEventListener("click", () => close());
	dismissButton?.addEventListener("click", () => close(true));
	overlay.addEventListener("click", () => {
		if (block.dataset.closeOnOverlay === "true") {
			close();
		}
	});
	launcher?.addEventListener("click", () => open());

	if (block.dataset.targetSelector) {
		document.querySelectorAll(block.dataset.targetSelector).forEach((target) => {
			target.addEventListener("click", (event) => {
				event.preventDefault();
				open();
			});
		});
	}

	document.addEventListener("keydown", (event) => {
		if (event.key === "Escape" && block.classList.contains("is-open")) {
			close();
		}
	});

	schedule();
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".wp-block-ai-zippy-child-site-popup").forEach(initPopup);
});
