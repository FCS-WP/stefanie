const initClients = (block) => {
	const track = block.querySelector(".home-clients-v2__track");
	const slides = Array.from(block.querySelectorAll(".home-clients-v2__slide"));
	const dots = Array.from(block.querySelectorAll(".home-clients-v2__dot"));

	if (!track || !slides.length) {
		return;
	}

	const goTo = (index) => {
		const nextIndex = Math.max(0, Math.min(index, slides.length - 1));

		track.style.transform = `translateX(-${nextIndex * 100}%)`;

		slides.forEach((slide, slideIndex) => {
			slide.classList.toggle("is-active", slideIndex === nextIndex);
		});

		dots.forEach((dot, dotIndex) => {
			const isActive = dotIndex === nextIndex;
			dot.classList.toggle("is-active", isActive);
			dot.setAttribute("aria-selected", isActive ? "true" : "false");
		});
	};

	dots.forEach((dot) => {
		dot.addEventListener("click", () => goTo(Number(dot.dataset.slide) || 0));
	});

	goTo(0);
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".wp-block-ai-zippy-child-home-clients-v2").forEach(initClients);
});
