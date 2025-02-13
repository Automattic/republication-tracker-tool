/**
 * Republish template script.
 */

/**
 * The main script for the republish template.
 */
document.addEventListener("DOMContentLoaded", () => {
	/**
	 * Selects the text in the textarea when it is focused.
	 */
	document
		.querySelector(".republish-article .republish-article__info textarea")
		?.addEventListener("focus", (event) => {
			event.target.select();
		});

	/**
	 * Copies the text in the textarea to the clipboard when the copy button is clicked.
	 */
	document
		.querySelector(".republish-article .republish-article__copy-button")
		?.addEventListener("click", (event) => {
			event.preventDefault();
			const textarea = document.querySelector(
				".republish-article .republish-article__info textarea"
			);
			const success = copyTextToClipboard(textarea.value);

			if (success) {
				event.target.innerText = __("Copied!", "the-city-features");

				setTimeout(() => {
					event.target.innerText = __(
						"Copy to clipboard",
						"the-city-features"
					);
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
