<?php
/**
 * Republish Button Block.
 *
 * @package Republication_Tracker_Tool
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Republish Button Block class.
 */
final class Republication_Tracker_Tool_Republish_Button_Block {

	/**
	 * Initialize the block.
	 */
	public static function init() {
		add_action( 'init', [ __CLASS__, 'register_block' ] );
	}

	/**
	 * Register the block. Only available on block themes.
	 */
	public static function register_block() {
		if ( ! function_exists( 'wp_is_block_theme' ) || ! wp_is_block_theme() ) {
			return;
		}

		register_block_type_from_metadata(
			REPUBLICATION_TRACKER_TOOL_PATH . 'src/blocks/republish-button',
			[
				'render_callback' => [ __CLASS__, 'render_block' ],
			]
		);
	}

	/**
	 * Render the block on the frontend.
	 *
	 * @param array $attrs Block attributes.
	 * @return string Rendered block HTML.
	 */
	public static function render_block( $attrs ) {
		global $post;

		// Guard: only render on singular views.
		if ( ! is_singular() || ! $post instanceof \WP_Post ) {
			return '';
		}

		// Guard: check allowed post types.
		$allowed_post_types = (array) apply_filters( 'republication_tracker_tool_post_types', [ 'post' ] );
		if ( ! in_array( get_post_type( $post ), $allowed_post_types, true ) ) {
			return '';
		}

		// Guard: check if widget is hidden for this post (same filter as the widget).
		$hide = apply_filters(
			'hide_republication_widget',
			get_post_meta( $post->ID, 'republication-tracker-tool-hide-widget', true ),
			$post
		);
		if ( true == $hide ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
			return '';
		}

		// Translated defaults (block.json defaults are not translatable).
		$default_attrs = [
			'buttonText'  => __( 'Republish This Story', 'republication-tracker-tool' ),
			'message'     => __( 'Republish our articles for free, online or in print, under a Creative Commons license.', 'republication-tracker-tool' ),
			'displayMode' => 'modal',
		];
		$attrs = wp_parse_args( $attrs, $default_attrs );

		// Validate displayMode against allowed values.
		if ( ! in_array( $attrs['displayMode'], [ 'modal', 'page' ], true ) ) {
			$attrs['displayMode'] = 'modal';
		}

		// Fall back to translated default when attribute is empty string.
		$button_text  = '' === trim( (string) $attrs['buttonText'] ) ? $default_attrs['buttonText'] : $attrs['buttonText'];
		$message_text = '' === trim( (string) $attrs['message'] ) ? $default_attrs['message'] : $attrs['message'];
		$display_mode = $attrs['displayMode'];

		// License badge.
		$license_key   = get_option( 'republication_tracker_tool_license', REPUBLICATION_TRACKER_TOOL_DEFAULT_LICENSE );
		$using_license = isset( REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ] );

		// Block wrapper attributes go on the outer div (anchor, alignment, layout, block supports).
		$wrapper_attributes = get_block_wrapper_attributes();

		// Start building output.
		$html = '<div ' . $wrapper_attributes . '>';

		// Message.
		$html .= '<p class="wp-block-republication-tracker-tool-republish-button__message">' . wp_kses_post( $message_text ) . '</p>';

		// Button or link (inherits colors from wrapper via CSS).
		$button_class = 'wp-block-republication-tracker-tool-republish-button__button';
		if ( 'page' === $display_mode ) {
			$endpoint      = apply_filters( 'republication_tracker_tool_endpoint', 'republish' );
			$republish_url = home_url( '/' . $endpoint . wp_make_link_relative( get_permalink( $post->ID ) ) );
			$html         .= '<a class="' . esc_attr( $button_class ) . '" href="' . esc_url( $republish_url ) . '">' . esc_html( $button_text ) . '</a>';
		} else {
			$html .= '<button class="' . esc_attr( $button_class ) . '" data-modal-trigger="republish">' . esc_html( $button_text ) . '</button>';
		}

		// License badge.
		if ( $using_license ) {
			$html .= sprintf(
				'<p><a class="license" rel="noreferrer license" target="_blank" href="%s"><img alt="%s" style="border-width:0" src="%s" /></a></p>',
				esc_url( REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ]['url'] ),
				esc_html__( 'Creative Commons License', 'republication-tracker-tool' ),
				esc_url( plugin_dir_url( __DIR__ ) . 'assets/img/' . $license_key . '.png' )
			);
		}

		$html .= '</div>';

		// Modal (only for modal mode, only once per page).
		if ( 'modal' === $display_mode ) {
			self::enqueue_modal_assets();

			if ( ! Republication_Tracker_Tool::$modal_rendered ) {
				Republication_Tracker_Tool::$modal_rendered = true;

				$is_amp             = false; // Block themes do not support AMP.
				$modal_content_path = REPUBLICATION_TRACKER_TOOL_PATH . 'includes/shareable-content.php';

				ob_start();
				?>
				<div id="republication-tracker-tool-modal" style="display:none;" data-postid="<?php echo esc_attr( $post->ID ); ?>" data-pluginsdir="<?php echo esc_attr( plugins_url() ); ?>" role="dialog" aria-modal="true" aria-labelledby="republish-modal-label">
					<?php include $modal_content_path; ?>
				</div>
				<?php
				$html .= ob_get_clean();
			}
		}

		return $html;
	}

	/**
	 * Enqueue modal-specific assets.
	 */
	private static function enqueue_modal_assets() {
		// Modal styles (same handle as widget for deduplication).
		wp_enqueue_style(
			'republication-tracker-tool-css',
			REPUBLICATION_TRACKER_TOOL_URL . 'assets/widget.css',
			[],
			REPUBLICATION_TRACKER_TOOL_VERSION
		);

		// Clipboard utilities (shared with widget).
		wp_enqueue_script(
			'republication-tracker-tool-clipboard-utils',
			REPUBLICATION_TRACKER_TOOL_URL . 'assets/clipboard-utils.js',
			[],
			REPUBLICATION_TRACKER_TOOL_VERSION,
			true
		);

		// Block frontend script.
		$asset_file = REPUBLICATION_TRACKER_TOOL_PATH . 'dist/republish-button-view.asset.php';
		$asset      = file_exists( $asset_file )
			? include $asset_file
			: [ 'dependencies' => [], 'version' => REPUBLICATION_TRACKER_TOOL_VERSION ];

		wp_enqueue_script(
			'republication-tracker-tool-republish-button-view',
			REPUBLICATION_TRACKER_TOOL_URL . 'dist/republish-button-view.js',
			array_merge( $asset['dependencies'], [ 'republication-tracker-tool-clipboard-utils' ] ),
			$asset['version'],
			true
		);
	}
}

Republication_Tracker_Tool_Republish_Button_Block::init();
