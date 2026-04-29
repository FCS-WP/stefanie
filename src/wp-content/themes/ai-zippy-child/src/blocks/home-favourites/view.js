const skeletonMarkup = () =>
	Array.from({ length: 3 })
		.map(
			(_, index) => `
				<article class="home-favourites__slide home-favourites__slide--skeleton${index === 1 ? " is-active" : ""}">
					<div class="home-favourites__card">
						<span class="home-favourites__skeleton home-favourites__skeleton-image"></span>
						<div class="home-favourites__body">
							<span class="home-favourites__skeleton home-favourites__skeleton-title"></span>
							<span class="home-favourites__skeleton home-favourites__skeleton-meta"></span>
							<span class="home-favourites__skeleton home-favourites__skeleton-price"></span>
						</div>
					</div>
				</article>
			`
		)
		.join("");

const initFavourites = (block) => {
	const track = block.querySelector(".home-favourites__track");
	const prevButton = block.querySelector(".home-favourites__arrow--prev");
	const nextButton = block.querySelector(".home-favourites__arrow--next");
	const filterButtons = Array.from(block.querySelectorAll(".home-favourites__filter"));
	const endpoint = block.dataset.endpoint;
	const limit = Number(block.dataset.limit) || 8;
	let currentIndex = 0;
	let requestId = 0;

	if (!track) {
		return;
	}

	const getFilter = (button) => {
		try {
			return JSON.parse(button.dataset.filter || "{}");
		} catch (error) {
			return {};
		}
	};

	const slides = () => Array.from(track.querySelectorAll(".home-favourites__slide"));

	const visibleCount = () => {
		if (window.matchMedia("(min-width: 901px)").matches) {
			return 3;
		}

		if (window.matchMedia("(min-width: 641px)").matches) {
			return 2;
		}

		return 1;
	};

	const minIndex = () => (visibleCount() >= 3 && slides().length > 2 ? 1 : 0);

	const maxIndex = () => {
		const allSlides = slides();

		if (visibleCount() >= 3 && allSlides.length > 2) {
			return Math.max(1, allSlides.length - 2);
		}

		return Math.max(0, allSlides.length - 1);
	};

	const setActive = () => {
		slides().forEach((slide, index) => {
			slide.classList.toggle("is-active", index === currentIndex);
		});

		if (prevButton) {
			prevButton.disabled = currentIndex <= minIndex();
		}

		if (nextButton) {
			nextButton.disabled = currentIndex >= maxIndex();
		}
	};

	const goTo = (index) => {
		const allSlides = slides();

		if (!allSlides.length) {
			return;
		}

		currentIndex = Math.max(minIndex(), Math.min(index, maxIndex()));

		const activeSlide = allSlides[currentIndex];
		const viewport = block.querySelector(".home-favourites__viewport");
		const viewportCenter = viewport ? viewport.offsetWidth / 2 : 0;
		const slideCenter = activeSlide.offsetLeft + activeSlide.offsetWidth / 2;
		track.style.transform = `translateX(${viewportCenter - slideCenter}px)`;
		setActive();
	};

	const refresh = async (button) => {
		if (!endpoint || !button) {
			return;
		}

		const thisRequest = requestId + 1;
		requestId = thisRequest;

		filterButtons.forEach((filterButton) => {
			const isActive = filterButton === button;
			filterButton.classList.toggle("is-active", isActive);
			filterButton.setAttribute("aria-selected", isActive ? "true" : "false");
		});

		block.classList.add("is-loading");
		track.innerHTML = skeletonMarkup();
		goTo(1);

		try {
			const response = await fetch(endpoint, {
				method: "POST",
				credentials: "same-origin",
				headers: {
					"Content-Type": "application/json",
				},
				body: JSON.stringify({
					filter: getFilter(button),
					limit,
				}),
			});

			if (!response.ok) {
				throw new Error("Product request failed");
			}

			const data = await response.json();

			if (thisRequest !== requestId) {
				return;
			}

			track.innerHTML = data.html || "";
			block.classList.remove("is-loading");
			goTo(slides().length > 1 ? 1 : 0);
		} catch (error) {
			if (thisRequest === requestId) {
				block.classList.remove("is-loading");
				track.innerHTML = '<p class="home-favourites__empty">Products could not be loaded.</p>';
				goTo(0);
			}
		}
	};

	if (prevButton) {
		prevButton.addEventListener("click", () => goTo(currentIndex - 1));
	}

	if (nextButton) {
		nextButton.addEventListener("click", () => goTo(currentIndex + 1));
	}

	filterButtons.forEach((button) => {
		button.addEventListener("click", () => refresh(button));
	});

	window.addEventListener("resize", () => goTo(currentIndex));
	goTo(slides().length > 1 ? 1 : 0);
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".wp-block-ai-zippy-child-home-favourites").forEach(initFavourites);
});
