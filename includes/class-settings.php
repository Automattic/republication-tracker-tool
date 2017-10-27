<?php
/**
 * Creative Commons Sharing Settings.
 *
 * @since   1.0
 * @package Creative_Commons_Sharing
 */

/**
 * Creative Commons Sharing Settings class.
 *
 * @since 1.0
 */
class Creative_Commons_Sharing_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Creative_Commons_Sharing
	 * @since  1.0
	 */
	protected $plugin = null;

	protected $settings_page = 'creative-commons-sharing';

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
		add_action( 'admin_init', array( $this, 'create_settings' ) );
	}

	/**
	 * Create settings section.
	 *
	 * @since 1.0
	 */
	public function create_settings() {

		add_settings_field(
			'creative_commons_sharing_policy',
			esc_html__( 'Creative Commons Sharing Policy', 'creative-commons-sharing' ),
			array( $this, 'creative_commons_sharing_callback' ),
			'reading'
		);

		register_setting(
			'reading',
			'creative_commons_sharing_policy',
			'wp_kses_post'
		);
	}

	public function creative_commons_sharing_callback( $arg ) {
		$content = get_option( 'creative_commons_sharing_policy' );
		wp_editor(
			$content,
			'creative_commons_sharing_policy',
			array(
	            'wpautop'       => true,
	            'media_buttons' => false,
	            'textarea_name' => 'creative_commons_sharing_policy',
	            'textarea_rows' => 10,
	            'teeny'         => true,
	        )
		);
		echo sprintf( '<p><em>%s</em></p>', esc_html__( 'This policy will display in the modal window when someone copies the content of your article for republishing.' ) );
	}
}
