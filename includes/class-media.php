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
	 */
	public static function can_distribute( $media_id ) {
		$media_distribution = get_option( 'republication_tracker_tool_media_distribution', 'on' );
		if ( $media_distribution === 'on' ) {
			// All media should be distributed, regardless of the individual setting.
			return true;
		}
		// Otherwise, check the individual setting.
		if ( class_exists( '\Newspack\Newspack_Image_Credits' ) ) {
			return (bool) get_post_meta( $media_id, \Newspack\Newspack_Image_Credits::MEDIA_CREDIT_CAN_DISTRIBUTE_META, true );
		}

		return false;
	}
}
