<?php
/**
 * Republication Tracker Tool Media.
 *
 * @since   1.0
 * @package Republication_Tracker_Tool
 */

/**
 * Republication Tracker Tool Media class.
 *
 * @since 1.0
 */
class Republication_Tracker_Tool_Media {
	/**
	 * Should the media element be distributed?
	 *
	 * @param int $media_id ID of the media element.
	 * @return bool True if the media can be distributed, false otherwise.
	 */
	public static function can_distribute( $media_id ) {
		if ( class_exists( '\Newspack\Newspack_Image_Credits' ) ) {
			return (bool) get_post_meta( $media_id, \Newspack\Newspack_Image_Credits::MEDIA_CREDIT_CAN_DISTRIBUTE_META, true );
		}

		return false;
	}

	/**
	 * Check if global media distribution is enabled.
	 *
	 * @return bool True if global distribution is enabled, false otherwise.
	 */
	public static function is_global_distribution_enabled() {
		$media_distribution = get_option( 'republication_tracker_tool_media_distribution', 'on' );
		return 'on' === $media_distribution;
	}
}
