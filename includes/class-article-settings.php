<?php
/**
 * Creative Commons Sharing Article Settings.
 *
 * @since   1.0
 * @package Creative_Commons_Sharing
 */

/**
 * Creative Commons Sharing Article Settings class.
 *
 * @since 1.0
 */
class Creative_Commons_Sharing_Article_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Creative_Commons_Sharing
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Creative_Commons_Sharing $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
	}

	/**
	 * Add custom metaboxes.
	 *
	 * @since 1.0
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'creative-commons-sharing',
			esc_html__( 'Creative Commons Sharing', 'creative-commons-sharing' ),
			array( $this, 'render_metabox' ),
			array( 'post', 'page' ),
			'side',
			'default'
		);
	}

	/**
	 * Render a custom metabox
	 *
	 * @since 1.0
	 * @param obj $post Post object.
	 * @param obj $args Arguments object.
	 */
	public function render_metabox( $post, $args ) {
		echo sprintf( 'This article has been shared %d times.', intval( get_post_meta( $post->ID, 'creative_commons_sharing', true ) ) );
	}
}
