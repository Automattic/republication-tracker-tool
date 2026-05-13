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
	 */
	public static function register_block() {
		register_block_type_from_metadata(
			REPUBLICATION_TRACKER_TOOL_PATH . 'src/blocks/republish-license',
			[
				'render_callback' => [ __CLASS__, 'render_block' ],
			]
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

		return sprintf(
			'<div %1$s><a rel="noreferrer license" target="_blank" href="%2$s"><img alt="%3$s" style="border-width:0" src="%4$s" /></a></div>',
			$wrapper_attributes,
			esc_url( $license['url'] ),
			esc_attr( $license['description'] ),
			esc_url( $license['badge'] )
		);
	}
}

Republication_Tracker_Tool_Republish_License_Block::init();
