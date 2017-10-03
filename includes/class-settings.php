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
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		add_action( 'admin_init', array( $this, 'create_settings' ) );
	}

	/**
	 * Create admin page
	 *
	 * @since 1.0
	 */
	public function create_admin_page() {
		add_submenu_page(
			'options-general.php',
			esc_html__( 'Creative Commons Sharing', 'creative-commons-sharing' ),
			esc_html__( 'Creative Commons Sharing', 'creative-commons-sharing' ),
			'manage_options',
			$this->settings_page,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Render admin page html.
	 *
	 * @since 1.0
	 */
	public function render_admin_page() {
		?>
		<div class="wrap options-page <?php echo esc_attr( $this->settings_page ); ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<form method="POST" action="options.php">
				<?php settings_fields( 'creative-commons-sharing' ); ?>
				<?php do_settings_sections( 'creative-commons-sharing' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Create settings section.
	 *
	 * @since 1.0
	 */
	public function create_settings() {
/*
		add_settings_section(
			$section_key,
			$section['label'],
			array( $this, 'section_callback' ),
			$this->settings_page
		);

		add_settings_field(
			$field_key,
			$field['label'],
			array( $this, 'field_callback' ),
			$this->settings_page,
			$section_key,
			array_merge( array( 'key' => $field_key ), $field )
		);
		register_setting( 'creative-commons-sharing', $field_key );
*/
	}

	public function section_callback( $arg ) {}

	public function field_callback( $arg ) {
		if ( 'checkbox' === $arg['type'] ) {
			echo sprintf(
				'<input type="%s" name="%s" id="%s" type="checkbox" value="1" class="code" %s />',
				$arg['type'],
				$arg['key'],
				$arg['key'],
				checked( 1, get_option( $arg['key'] ), false )
			);
		} else {
			// @TODO needs validation/sanitization.
			echo sprintf(
				'<input type="%s" name="%s" id="%s" value="%s" placeholder="Enter a URL" class="regular-text" />',
				$arg['type'],
				$arg['key'],
				$arg['key'],
				get_option( $arg['key'] )
			);
		}
	}
}
