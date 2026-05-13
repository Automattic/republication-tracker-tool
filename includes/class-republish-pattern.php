<?php
/**
 * Republish block pattern registration.
 *
 * @package Republication_Tracker_Tool
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the "Republish Section" pattern and its category.
 */
final class Republication_Tracker_Tool_Republish_Pattern {

	/**
	 * Pattern name.
	 */
	public const PATTERN_NAME = 'republication-tracker-tool/republish-section';

	/**
	 * Pattern category slug.
	 */
	public const CATEGORY_SLUG = 'republication-tracker-tool';

	/**
	 * Hook up registration.
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register' ] );
	}

	/**
	 * Register the pattern category and pattern.
	 */
	public static function register() {
		register_block_pattern_category(
			self::CATEGORY_SLUG,
			[ 'label' => __( 'Republication', 'republication-tracker-tool' ) ]
		);

		$description     = __( 'Republish our articles for free, online or in print, under a Creative Commons license.', 'republication-tracker-tool' );
		$button_text     = __( 'Republish This Story', 'republication-tracker-tool' );
		$button_text_enc = wp_json_encode( $button_text );

		$content = <<<HTML
<!-- wp:group {"className":"republication-tracker-tool-republish-section"} -->
<div class="wp-block-group republication-tracker-tool-republish-section">
	<!-- wp:paragraph -->
	<p>{$description}</p>
	<!-- /wp:paragraph -->

	<!-- wp:republication-tracker-tool/republish-button {"buttonText":{$button_text_enc}} /-->

	<!-- wp:republication-tracker-tool/republish-license /-->
</div>
<!-- /wp:group -->
HTML;

		// Hide the pattern from the inserter on classic themes (matches the
		// republish-button block's gating). Stays registered so existing
		// instances and inter-block references keep working.
		$inserter = ! function_exists( 'wp_is_block_theme' ) || wp_is_block_theme();

		register_block_pattern(
			self::PATTERN_NAME,
			[
				'title'       => __( 'Republish Section', 'republication-tracker-tool' ),
				'description' => __( 'A paragraph, republish button, and Creative Commons license badge grouped together.', 'republication-tracker-tool' ),
				'categories'  => [ self::CATEGORY_SLUG ],
				'content'     => $content,
				'inserter'    => $inserter,
			]
		);
	}
}

Republication_Tracker_Tool_Republish_Pattern::init();
