const getSlidesToShow = (block) => {
	const width = window.innerWidth;

	if (width <= 640) {
		return Number(block.dataset.mobileSlides) || 1;
	}

	if (width <= 980) {
		return Number(block.dataset.tabletSlides) || 3;
	}

	return Number(block.dataset.desktopSlides) || 6;
};

const initCarousel = (block) => {
	const track = block.querySelector(".home-categories__track");
	const slides = Array.from(block.querySelectorAll(".home-categories__slide"));
	const prevButton = block.querySelector(".home-categories__arrow--prev");
	const nextButton = block.querySelector(".home-categories__arrow--next");
	const dotsWrap = block.querySelector(".home-categories__dots");
	let currentIndex = 0;

	if (!track || slides.length === 0) {
		return;
	}

	const getMaxIndex = () => Math.max(0, slides.length - getSlidesToShow(block));

	const goTo = (index) => {
		const maxIndex = getMaxIndex();
		currentIndex = Math.max(0, Math.min(index, maxIndex));
		track.style.transform = `translateX(-${slides[currentIndex].offsetLeft}px)`;

		if (prevButton) {
			prevButton.disabled = currentIndex === 0;
		}

		if (nextButton) {
			nextButton.disabled = currentIndex >= maxIndex;
		}

		if (dotsWrap) {
			Array.from(dotsWrap.children).forEach((dot, dotIndex) => {
				dot.classList.toggle("is-active", dotIndex === currentIndex);
			});
		}
	};

	const rebuildDots = () => {
		if (!dotsWrap) {
			return;
		}

		const maxIndex = getMaxIndex();
		dotsWrap.innerHTML = "";

		for (let index = 0; index <= maxIndex; index += 1) {
			const dot = document.createElement("button");
			dot.type = "button";
			dot.className = "home-categories__dot";
			dot.setAttribute("aria-label", `Go to category slide ${index + 1}`);
			dot.addEventListener("click", () => goTo(index));
			dotsWrap.appendChild(dot);
		}
	};

	if (prevButton) {
		prevButton.addEventListener("click", () => goTo(currentIndex - 1));
	}

	if (nextButton) {
		nextButton.addEventListener("click", () => goTo(currentIndex + 1));
	}

	window.addEventListener("resize", () => {
		rebuildDots();
		goTo(currentIndex);
	});

	rebuildDots();
	goTo(0);
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".wp-block-ai-zippy-child-home-categories").forEach(initCarousel);
});
