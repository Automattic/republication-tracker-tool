<?php
/**
 * Class RepublishLicenseBlockTest
 *
 * @package Republication_Tracker_Tool
 */

/**
 * Test Republish License Block functionality.
 */
class RepublishLicenseBlockTest extends WP_UnitTestCase {

	/**
	 * Set up test environment.
	 */
	public function set_up() {
		parent::set_up();

		if ( ! WP_Block_Type_Registry::get_instance()->is_registered( 'republication-tracker-tool/republish-license' ) ) {
			register_block_type_from_metadata(
				REPUBLICATION_TRACKER_TOOL_PATH . 'src/blocks/republish-license',
				[
					'render_callback' => [ 'Republication_Tracker_Tool_Republish_License_Block', 'render_block' ],
				]
			);
		}
	}

	/**
	 * Helper to render the block through do_blocks().
	 *
	 * @return string Rendered HTML.
	 */
	private function render_block() {
		return do_blocks( '<!-- wp:republication-tracker-tool/republish-license /-->' );
	}

	/**
	 * Renders the CC badge anchor + image when a known license is set.
	 */
	public function test_renders_badge_when_license_set() {
		update_option( 'republication_tracker_tool_license', 'cc-by-nd-4.0' );

		$output = $this->render_block();

		$this->assertStringContainsString( '<a', $output );
		$this->assertStringContainsString( 'rel="noreferrer license"', $output );
		$this->assertStringContainsString( '<img', $output );
		$this->assertStringContainsString( REPUBLICATION_TRACKER_TOOL_LICENSES['cc-by-nd-4.0']['url'], $output );

		delete_option( 'republication_tracker_tool_license' );
	}

	/**
	 * Renders nothing when the license key isn't in the licenses map.
	 */
	public function test_renders_empty_when_license_unknown() {
		update_option( 'republication_tracker_tool_license', 'not-a-real-license' );

		$output = $this->render_block();

		$this->assertSame( '', trim( $output ) );

		delete_option( 'republication_tracker_tool_license' );
	}

	/**
	 * Output is wrapped with block wrapper attributes so block supports apply.
	 */
	public function test_output_uses_block_wrapper_attributes() {
		update_option( 'republication_tracker_tool_license', 'cc-by-nd-4.0' );

		$output = $this->render_block();

		$this->assertStringContainsString( 'wp-block-republication-tracker-tool-republish-license', $output );

		delete_option( 'republication_tracker_tool_license' );
	}
}
