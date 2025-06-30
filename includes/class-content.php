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
	 * @param int    $post_id Optional. Current post ID by default.
	 */
	public static function get_republishable_content( $content, $post_id = false ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		// Remove shortcodes from the content.
		$content = strip_shortcodes( $content );

		// Remove comments from the content. (Lookin' at you, Gutenberg.)
		$content = preg_replace( '/<!--(.|\s)*?-->/i', ' ', $content );

		/**
		 * What tags do we want to keep in the embed?
		 * Not things from our server.
		 *
		 * Generall: wp_kses_post, but not allowing the terms listed below because
		 * - they're referencing assets on our server: audio, figure, img, track, video
		 * - they're referencing the referenced asset: figure, figcaption
		 * - they're not likely to work: form, button
		 *
		 * @var array $allowed_tags_excerpt
		 * @link https://codex.wordpress.org/Function_Reference/wp_kses
		 */
		global $allowedposttags;
		$allowed_tags_excerpt = $allowedposttags;
		unset( $allowed_tags_excerpt['form'] );

		/**
		 * The article WP_Post object
		 *
		 * @var WP_Post $post the post object
		 */
		global $post;

		/**
		 * Allow sites to configure which tags are allowed to be output in the republication content
		 *
		 * Default value is the standard global $allowedposttags, except form elements.
		 *
		 * @link https://github.com/Automattic/republication-tracker-tool/issues/49
		 * @link https://developer.wordpress.org/reference/functions/wp_kses_allowed_html/
		 * @param Array $allowed_tags_excerpt an associative array of element tags that are allowed
		 */
		$allowed_tags_excerpt = apply_filters( 'republication_tracker_tool_allowed_tags_excerpt', $allowed_tags_excerpt, $post );

		// And finally, remove some tags.
		$content = wp_kses( $content, $allowed_tags_excerpt );

		// remove spare p tags and clean up these paragraphs
		$content = str_replace( '<p></p>', '', wpautop( $content ) );

		// Remove non-distributable images.
		$content = self::remove_non_distributable_images( $content );

		// Force the content to be UTF-8 escaped HTML.
		$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

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

	/**
	 * Remove non-distributable images from the content.
	 *
	 * @param string $content The post content.
	 * @param bool   $bypass_global_distribution_check Optional. If true, will bypass the distribution check and remove all images.
	 * @return string The content with non-distributable images removed.
	 */
	public static function remove_non_distributable_images( $content, $bypass_global_distribution_check = false ) {
		if ( ! class_exists( 'Republication_Tracker_Tool_Media' ) ) {
			return $content;
		}

		// Handle media.
		preg_match_all( '/<img[^>]+class="wp-image-(\d+)"[^>]*>/', $content, $matches );
		$found_images = [];

		foreach ( $matches[1] as $key => $attachment_id ) {
			if ( ! $bypass_global_distribution_check && ! Republication_Tracker_Tool_Media::can_distribute( $attachment_id ) ) {
				$found_images[] = [ $attachment_id, $matches[0][ $key ] ];
			} elseif ( $bypass_global_distribution_check && ! Republication_Tracker_Tool_Media::get_can_distribute_meta( $attachment_id ) ) {
				$found_images[] = [ $attachment_id, $matches[0][ $key ] ];
			}
		}

		// Remove the found images.
		foreach ( $found_images as [ $attachment_id, $found_image ] ) {
			// Remove the figure and figcaption of $found_image using regex.
			$pattern = '/<figure[^>]*>' . preg_quote( $found_image, '/' ) . '.*?<\/figure>/s';
			$content = preg_replace( $pattern, "<!-- RTT removed image ({$attachment_id}) -->", $content );
		}

		return $content;
	}
}
