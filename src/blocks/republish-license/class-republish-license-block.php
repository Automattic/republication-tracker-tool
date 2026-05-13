<?php
/**
 * Republish License Block.
 *
 * @package Republication_Tracker_Tool
 */

defined( 'ABSPATH' ) || exit;

/**
 * Republish License Block class.
 *
 * Renders the site's currently-configured Creative Commons license badge as a
 * linked image. Dynamic block — output reflects the current site setting at
 * render time, not the value at insert time.
 */
final class Republication_Tracker_Tool_Republish_License_Block {

	/**
	 * Initialize the block.
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_block' ] );
	}

	/**
	 * Register the block.
	 *
	 * On block themes the block is fully available. On classic themes it is
	 * still registered (so existing content does not become an "unknown block")
	 * but hidden from the inserter, matching the republish-button gating.
	 */
	public static function register_block() {
		$args = [
			'render_callback' => [ __CLASS__, 'render_block' ],
		];

		if ( function_exists( 'wp_is_block_theme' ) && ! wp_is_block_theme() ) {
			$args['supports'] = [
				'inserter' => false,
			];
		}

		register_block_type_from_metadata(
			REPUBLICATION_TRACKER_TOOL_PATH . 'src/blocks/republish-license',
			$args
		);
	}

	/**
	 * Render the block on the frontend (and in editor previews via ServerSideRender).
	 *
	 * @return string Rendered HTML, or empty string when no recognizable license is set.
	 */
	public static function render_block() {
		$license_key = get_option( 'republication_tracker_tool_license', REPUBLICATION_TRACKER_TOOL_DEFAULT_LICENSE );

		if ( ! isset( REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ] ) ) {
			return '';
		}

		$license = REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ];

		$wrapper_attributes = get_block_wrapper_attributes();

		// In the editor preview (ServerSideRender → REST block-renderer endpoint)
		// neutralize the link so accidental clicks don't open the real license
		// URL in a new tab. Keep the <a> + href so the markup shape is identical.
		$is_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST;
		$href              = $is_editor_preview ? '#' : esc_url( $license['url'] );
		$target_attr       = $is_editor_preview ? '' : ' target="_blank"';

		return sprintf(
			'<div %1$s><a rel="noreferrer license" href="%2$s"%3$s><img alt="%4$s" style="border-width:0" src="%5$s" /></a></div>',
			$wrapper_attributes,
			$href,
			$target_attr,
			esc_attr( $license['description'] ),
			esc_url( $license['badge'] )
		);
	}
}

Republication_Tracker_Tool_Republish_License_Block::init();
