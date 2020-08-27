<?php
/**
 * Functions to register client-side assets (scripts and stylesheets) for the
 * Gutenberg block.
 *
 * @package republication-tracker-tool
 */

/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 * @see https://wordpress.org/gutenberg/handbook/designers-developers/developers/tutorials/block-tutorial/applying-styles-with-stylesheets/
 */
function republication_tracker_tool_button_block_init() {
	// Skip block registration if Gutenberg is not enabled/merged.
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}
	$dir = dirname( __FILE__ );

	$index_js = 'button/index.js';
	wp_register_script(
		'button-block-editor',
		plugins_url( $index_js, __FILE__ ),
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
			'wp-components',
		),
		filemtime( "$dir/$index_js" )
	);

	$editor_css = 'button/editor.css';
	wp_register_style(
		'button-block-editor',
		plugins_url( $editor_css, __FILE__ ),
		array(),
		filemtime( "$dir/$editor_css" )
	);

	register_block_type( 'republication-tracker-tool/button', array(
		'attributes'      => array(
			'label' => array(
				'type' => 'string',
			),
		),
		'editor_script'   => 'button-block-editor',
		'editor_style'    => 'button-block-editor',
		'style'           => 'republication-tracker-tool-css',
		'script'          => 'republication-tracker-tool-js',
		'render_callback' => array( 'Republication_Tracker_Tool_Shortcodes', 'button_shortcode' ),
	) );
}
add_action( 'init', 'republication_tracker_tool_button_block_init' );
