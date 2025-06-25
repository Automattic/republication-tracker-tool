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
				'label'    => esc_html__( 'Policy', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_policy_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_analytics_ga4_id',
				'label'    => esc_html__( 'Google Analytics 4 ID', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_analytics_ga4_id_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_analytics_ga4_secret',
				'label'    => esc_html__( 'Google Analytics 4 API Secret', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_analytics_ga4_secret_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_display_attribution',
				'label'    => esc_html__( 'Display attribution', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_display_attribution_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_media_distribution',
				'label'    => esc_html__( 'Media Distribution', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_media_distribution_callback' ),
			],
			[
				'key'      => 'republication_tracker_tool_license',
				'label'    => esc_html__( 'License', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_license_callback' ),
			],
			[
				'key'               => 'republication_tracker_additional_tracking_code',
				'label'             => esc_html__( 'Additional Tracking Code', 'republication-tracker-tool' ),
				'callback'          => array( $this, 'republication_tracker_additional_tracking_code_callback' ),
				'sanitize_callback' => 'htmlentities',
			],
			[
				'key'      => 'republication_tracker_tool_default_post_distribution',
				'label'    => esc_html__( 'Default Post Distribution', 'republication-tracker-tool' ),
				'callback' => array( $this, 'republication_tracker_tool_default_post_distribution_callback' ),
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
				$setting['sanitize_callback'] ?? 'wp_kses_post'
			);
		}
	}

	public function republication_tracker_tool_section_callback() {
		// if our republication_tracker_tool_analytics_id field has been set and is not empty, let's display
		// a sample tracking code for users to manually input into articles
		if ( ! empty( get_option( 'republication_tracker_tool_analytics_ga4_id', false ) ) && ! empty( get_option( 'republication_tracker_tool_analytics_ga4_secret', false ) ) ) {
			$pixel = Republication_Tracker_Tool::create_tracking_pixel_markup( 'YOUR-POST-ID' );
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

	/**
	 * Additional tracking code render callback.
	 */
	public function republication_tracker_additional_tracking_code_callback() {
		$content = html_entity_decode( get_option( 'republication_tracker_additional_tracking_code' ) );
		echo sprintf( '<p><em>%s</em></p>', wp_kses_post( __( 'Use placeholders: <strong>{{post-id}}</strong> will be replaced with the post ID, and <strong>{{post-url}}</strong> will be replaced with the post URL.', 'republication-tracker-tool' ) ) );
		echo sprintf(
			'<textarea name="%1$s" rows="5" cols="150">%2$s</textarea>',
			'republication_tracker_additional_tracking_code',
			esc_textarea( $content )
		);
		echo sprintf( '<p><em>%s</em></p>', wp_kses_post( __( 'The additional Tracking Code field is where you will be able to input your additional tracking code that will be appended to your copied content.', 'republication-tracker-tool' ) ) );
	}

	public function republication_tracker_tool_policy_callback() {
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
		printf( '<p><em>%s</em></p>', wp_kses_post( __( 'The Republication Tracker Tool Policy field is where you will be able to input your rules and policies for users to see before they copy and paste your content to republish.As an example of a republication policy hat uses a Creative Commons license, check out the list in this plugin\'s <a href="https://github.com/Automattic/republication-tracker-tool/blob/trunk/docs/configuring-plugin-settings.md#republication-tracker-tool-policy" target="_blank">documentation</a> on GitHub.', 'republication-tracker-tool' ) ) );
	}

	public function republication_tracker_tool_analytics_id_callback() {
		$content = get_option( 'republication_tracker_tool_analytics_id' );
		printf(
			'<input type="text" name="%1$s" value="%2$s">%3$s',
			'republication_tracker_tool_analytics_id',
			esc_html( $content ),
			wp_kses_post( '<p><em>' . __( 'Your Google Analytics Universal Analytics ID. Note that <a href="https://support.google.com/analytics/answer/11583528">this will be deprecated on July 1, 2023</a>, so add a GA4 ID and API secret below to avoid interruptions in analytics tracking.', 'republication-tracker-tool' ) . '</em></p>' )
		);
	}

	public function republication_tracker_tool_analytics_ga4_id_callback() {
		$content = get_option( 'republication_tracker_tool_analytics_ga4_id' );
		printf(
			'<input type="text" name="%1$s" value="%2$s">%3$s',
			'republication_tracker_tool_analytics_ga4_id',
			esc_html( $content ),
			wp_kses_post( '<p><em>' . __( 'Your Google Analytics 4 tag ID. <a href="https://support.google.com/analytics/answer/9539598">How to get this</a>.', 'republication-tracker-tool' ) . '</em></p>' )
		);
	}

	public function republication_tracker_tool_analytics_ga4_secret_callback() {
		$content = get_option( 'republication_tracker_tool_analytics_ga4_secret' );
		printf(
			'<input type="text" name="%1$s" value="%2$s">%3$s',
			'republication_tracker_tool_analytics_ga4_secret',
			esc_html( $content ),
			wp_kses_post( '<p><em>' . __( 'Your Google Analytics 4 API secret. <a href="https://developers.google.com/analytics/devguides/collection/protocol/ga4/sending-events?client_type=gtag#required_parameters">How to get this</a>.', 'republication-tracker-tool' ) . '</em></p>' )
		);
	}

	public function republication_tracker_tool_display_attribution_callback() {
		$display_attribution = get_option( 'republication_tracker_tool_display_attribution', 'on' );
		?>
			<input
				type="checkbox"
				id="<?php echo esc_attr( 'republication_tracker_tool_display_attribution' ); ?>"
				name="<?php echo esc_attr( 'republication_tracker_tool_display_attribution' ); ?>"
				<?php if ( 'on' === $display_attribution ) : ?>
					checked
				<?php endif; ?>
			/>
			<p><em><?php echo esc_html__( 'If checked, an attribution statement will be appended to the copied content.', 'republication-tracker-tool' ); ?></em></p>
		<?php
	}

	public function republication_tracker_tool_media_distribution_callback() {
		$media_distribution = get_option( 'republication_tracker_tool_media_distribution', 'on' );
		?>
			<input
				type="checkbox"
				id="<?php echo esc_attr( 'republication_tracker_tool_media_distribution' ); ?>"
				name="<?php echo esc_attr( 'republication_tracker_tool_media_distribution' ); ?>"
				<?php if ( 'on' === $media_distribution ) : ?>
					checked
				<?php endif; ?>
			/>
			<p><em><?php echo esc_html__( 'When you check the box, all the media from the original article will be included in the republished article. If you don’t want this to happen, mark media elements with “Can distribute?” toggle in your media library and leave this box unchecked. This way, republished articles will only show the elements you’ve marked as distributable.', 'republication-tracker-tool' ); ?></em></p>
		<?php
	}

	public function republication_tracker_tool_license_callback() {
		$selected = get_option( 'republication_tracker_tool_license', REPUBLICATION_TRACKER_TOOL_DEFAULT_LICENSE );

		$licenses = REPUBLICATION_TRACKER_TOOL_LICENSES;

		?>
		<fieldset>
			<p>
			<?php foreach ( $licenses as $license_key => $license_values ) : ?>
				<label>
					<input
						type="radio"
						id="<?php echo esc_attr( 'republication_tracker_tool_license' ) . '_' . $license_key; ?>"
						name="<?php echo esc_attr( 'republication_tracker_tool_license' ); ?>"
						<?php if ( $license_key === $selected ) : ?>
							checked
						<?php endif; ?>
						value="<?php esc_attr_e( $license_key ); ?>"
					/>
					<?php esc_html_e( $license_values['label'] . ' - ' . $license_values['description'] ); ?>
				</label>
				<br>
			<?php endforeach; ?>
			</p>
		</fieldset>
		<?php
	}

	public function republication_tracker_tool_default_post_distribution_callback() {
		$default_post_distribution = get_option( 'republication_tracker_tool_default_post_distribution', 'off' );
		?>
			<input
				type="checkbox"
				id="<?php echo esc_attr( 'republication_tracker_tool_default_post_distribution' ); ?>"
				name="<?php echo esc_attr( 'republication_tracker_tool_default_post_distribution' ); ?>"
				<?php if ( 'on' === $default_post_distribution ) : ?>
					checked
				<?php endif; ?>
			/>
			<p><em><?php echo esc_html__( 'If checked, "Hide Republication Widget" will be enabled by default for new posts.', 'republication-tracker-tool' ); ?></em></p>
		<?php
	}

}
