/**
 * Republish template script.
 */

/**
 * The main script for the republish template.
 */
document.addEventListener("DOMContentLoaded", () => {
	/**
	 * Handle tab switching for format selection.
	 */
	const tabButtons = document.querySelectorAll(".republish-tab-button");
	const tabContents = document.querySelectorAll(".republish-tab-content");

	tabButtons.forEach((button) => {
		button.addEventListener("click", (event) => {
			event.preventDefault();
			const targetTab = button.getAttribute("data-tab");

			tabButtons.forEach((btn) => btn.classList.remove("active"));
			tabContents.forEach((content) => content.classList.remove("active"));

			button.classList.add("active");
			const targetContent = document.querySelector(`[data-tab-content="${targetTab}"]`);
			if (targetContent) {
				targetContent.classList.add("active");
			}
		});
	});

	/**
	 * Selects the text in the active textarea when it is focused.
	 */
	const textareas = document.querySelectorAll(".republish-content-textarea");
	textareas.forEach((textarea) => {
		textarea.addEventListener("focus", (event) => {
			event.target.select();
		});
	});

	/**
	 * Copies the text in the active textarea to the clipboard when the copy button is clicked.
	 */
	document
		.querySelector(".republish-article__copy-button")
		?.addEventListener("click", (event) => {
			event.preventDefault();

			const activeTextarea = document.querySelector(".republish-tab-content.active");

			if (!activeTextarea) {
				return;
			}

			const success = copyTextToClipboard(activeTextarea.value);

			if (success) {
				event.target.innerText = "Copied!";

				setTimeout(() => {
					event.target.innerText = "Copy to clipboard";
				}, 2000);
			}
		});

	/**
	 * Copies the given text to the clipboard.
	 *
	 * @param {string} text The text to copy to the clipboard.
	 *
	 * @return {boolean} True if the text was copied to the clipboard, false otherwise.
	 */
	const copyTextToClipboard = (text) => {
		// Check if the clipboard API is available.
		if (!navigator.clipboard) {
			return false;
		}
		// Copy the text to the clipboard.
		navigator.clipboard
			.writeText(text)
			.then(() => true)
			.catch(() => false);

		return true;
	};
});
