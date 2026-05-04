const initFaqSection = (block) => {
	const items = Array.from(block.querySelectorAll(".faq-section__item"));

	items.forEach((item) => {
		const button = item.querySelector(".faq-section__question");
		const answer = item.querySelector(".faq-section__answer");

		if (!button || !answer) {
			return;
		}

		const setOpen = (isOpen) => {
			item.classList.toggle("is-open", isOpen);
			button.setAttribute("aria-expanded", isOpen ? "true" : "false");
			answer.hidden = !isOpen;
		};

		setOpen(button.getAttribute("aria-expanded") !== "false");
		button.addEventListener("click", () => {
			setOpen(button.getAttribute("aria-expanded") !== "true");
		});
	});
};

document.addEventListener("DOMContentLoaded", () => {
	document.querySelectorAll(".wp-block-ai-zippy-child-faq-section").forEach(initFaqSection);
});
