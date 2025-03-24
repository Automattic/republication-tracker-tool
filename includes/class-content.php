<?php
/**
 * Republication Tracker Tool Content.
 *
 * @since   1.0
 * @package Republication_Tracker_Tool
 */

/**
 * Republication Tracker Tool Content class.
 *
 * @since 1.0
 */
class Republication_Tracker_Tool_Content {
	/**
	 * Filter the content for the republication.
	 *
	 * @param string $content The post content.
	 * @param int    $post_id The post ID – should be supplied if different than the current post ID.
	 */
	public static function get_republishable_content( $content, $post_id = false ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Remove shortcodes from the content.
		$content = strip_shortcodes( $content );

		// Remove comments from the content. (Lookin' at you, Gutenberg.)
		$content = preg_replace( '/<!--(.|\s)*?-->/i', ' ', $content );

		// And finally, remove some tags.
		$content = wp_kses( $content, $allowed_tags_excerpt );

		// remove spare p tags and clean up these paragraphs
		$content = str_replace( '<p></p>', '', wpautop( $content ) );

		// Force the content to be UTF-8 escaped HTML.
		$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

		// Handle media.
		preg_match_all( '/<img[^>]+class="wp-image-(\d+)"[^>]*>/', $content, $matches );
		$found_images = [];

		foreach ( $matches[1] as $key => $attachment_id ) {
			if ( ! Republication_Tracker_Tool_Media::can_distribute( $attachment_id ) ) {
				$found_images[ $attachment_id ] = $matches[0][ $key ];
			}
		}

		// Suppress the found images conditionally.
		foreach ( $found_images as $attachment_id => $found_image ) {
			// Remove the figure and figcaption of $found_image using regex.
			$pattern = '/<figure[^>]*>' . preg_quote( $found_image, '/' ) . '.*?<\/figure>/s';
			$content = preg_replace( $pattern, '', $content );
		}

		$post_object = get_post( $post_id );

		/**
		 * Filters the content of the republished post.
		 *
		 * @param string $content The content of the post.
		 * @param WP_Post $post_object The post object.
		 * @return string The filtered content.
		 */
		$content = apply_filters( 'republication_tracker_tool_republish_content', $content, $post_object );

		return $content;
	}
}
