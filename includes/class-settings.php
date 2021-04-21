<?php
/**
 * Republication Tracker Tool Settings.
 *
 * @since   1.0
 * @package Republication_Tracker_Tool
 */

/**
 * Republication Tracker Tool Settings class.
 *
 * @since 1.0
 */
class Republication_Tracker_Tool_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Republication_Tracker_Tool
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Republication_Tracker_Tool $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		add_action( 'admin_init', array( $this, 'create_settings' ) );
	}

	/**
	 * Create settings section.
	 *
	 * @since 1.0
	 */
	public function create_settings() {
		add_settings_section(
			'republication_tracker_tool',
			esc_html__( 'Republication Tracker Tool Settings', 'republication-tracker-tool' ),
			array( $this, 'republication_tracker_tool_section_callback' ),
			'reading'
		);

		$settings = [
			[
				'key'      => 'republication_tracker_tool_policy',
				'label'    => esc_html__( 'Republication Tracker Tool Policy', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_policy_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_analytics_id',
				'label'    => esc_html__( 'Republication Tracker Google Analytics ID', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_analytics_id_callback' ),
			],
		];
		foreach ( $settings as $setting ) {
			add_settings_field(
				$setting['key'],
				$setting['label'],
				$setting['callback'],
				'reading',
				'republication_tracker_tool'
			);
			register_setting(
				'reading',
				$setting['key'],
				'wp_kses_post'
			);
		}
	}

	public function republication_tracker_tool_section_callback( $arg ) {
		// if our republication_tracker_tool_analytics_id field has been set and is not empty, let's display
		// a sample tracking code for users to manually input into articles
		if ( get_option( 'republication_tracker_tool_analytics_id' ) && ! empty( get_option( 'republication_tracker_tool_analytics_id' ) ) ) {
			$analytics_id = get_option( 'republication_tracker_tool_analytics_id' );
			$pixel        = Republication_Tracker_Tool::create_tracking_pixel_markup( 'YOUR-POST-ID' );
			printf(
				'
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Republication Tracker Tool Tracking Code</th>
							<td>
								<p>You can copy and paste this tracking code into articles of your choice. Remember to replace <code>YOUR-POST-ID</code> with your actual post ID.</p><br/>
								<code>' . wp_kses_post( htmlspecialchars( $pixel ) ) . '</code>
							</td>
						</tr>
					</tbody>
				</table>
			'
			);
		}

	}

	public function republication_tracker_tool_policy_callback( $arg ) {
		$content = get_option( 'republication_tracker_tool_policy' );
		wp_editor(
			$content,
			'republication_tracker_tool_policy',
			array(
				'wpautop'       => true,
				'media_buttons' => false,
				'textarea_name' => 'republication_tracker_tool_policy',
				'textarea_rows' => 10,
				'teeny'         => true,
			)
		);
		echo sprintf( '<p><em>%s</em></p>', wp_kses_post( 'The Republication Tracker Tool Policy field is where you will be able to input your rules and policies for users to see before they copy and paste your content to republish.As an example of a republication policy hat uses a Creative Commons license, check out the list in this plugin\'s <a href="https://github.com/Automattic/republication-tracker-tool/blob/master/docs/configuring-plugin-settings.md#republication-tracker-tool-policy" target="_blank">documentation</a> on GitHub.' ) );
	}

	public function republication_tracker_tool_analytics_id_callback( $arg ) {
		$content = get_option( 'republication_tracker_tool_analytics_id' );
		echo sprintf(
			'<input type="text" name="%1$s" value="%2$s">',
			'republication_tracker_tool_analytics_id',
			esc_html( $content )
		);
	}

}