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

		add_settings_section(
			'creative_commons_sharing',
			esc_html__( 'Creative Commons Sharing Settings', 'creative-commons-sharing' ),
			array( $this, 'creative_commons_sharing_section_callback' ),
			'reading'
		);

		add_settings_field(
			'creative_commons_sharing_policy',
			esc_html__( 'Creative Commons Sharing Policy', 'creative-commons-sharing' ),
			array( $this, 'creative_commons_sharing_policy_callback' ),
			'reading',
			'creative_commons_sharing'
		);

		add_settings_field(
			'creative_commons_analytics_id',
			esc_html__( 'Creative Commons Sharing Google Analytics ID', 'creative-commons-sharing' ),
			array( $this, 'creative_commons_analytics_id_callback' ),
			'reading',
			'creative_commons_sharing'
		);

		register_setting(
			'reading',
			'creative_commons_sharing_policy',
			'wp_kses_post'
		);

		register_setting(
			'reading',
			'creative_commons_analytics_id',
			'wp_kses_post'
		);

	}

	public function creative_commons_sharing_section_callback( $arg ){

		// if our creative_commons_analytics_id field has been set and is not empty, let's display
		// a sample tracking code for users to manually input into articles
		if(get_option( 'creative_commons_analytics_id' ) && !empty(get_option( 'creative_commons_analytics_id' ) ) ){
			$analytics_id = get_option( 'creative_commons_analytics_id' );
			$pixel = sprintf(
				// %1$s is the javascript source, %2$s is the post ID, %3$s is the plugins URL
				'<img id="creative-commons-sharing-source" src="%1$s?post=%2$s&ga=%3$s">',
				plugins_url( 'includes/pixel.php', dirname( __FILE__ ) ),
				'YOUR-POST-ID',
				esc_attr( $analytics_id )
			);
			printf('
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Creative Commons Tracking Code</th>
							<td>
								<p>You can copy and paste this tracking code into articles of your choice. Remember to replace <code>YOUR-POST-ID</code> with your actual post ID.</p><br/>
								<code>'.htmlspecialchars($pixel).'</code>
							</td>
						</tr>
					</tbody>
				</table>
			');
		}

	}

	public function creative_commons_sharing_policy_callback( $arg ) {
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
		echo sprintf( '<p><em>%s</em></p>', esc_html__( 'This policy will display in the modal window when someone copies the content of your article for republishing.', 'creative-commons-sharing' ) );
	}

	public function creative_commons_analytics_id_callback( $arg ){
		$content = get_option( 'creative_commons_analytics_id' );
		wp_editor(
			$content,
			'creative_commons_analytics_id',
			array(
				'wpautop' 		=> false,
				'media_buttons' => false,
				'textarea_name' => 'creative_commons_analytics_id',
				'textarea_rows' => 1,
				'tinymce'		=> false,
				'quicktags'     => array()
			)
		);
	}

}
