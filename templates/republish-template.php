<?php
/**
 * Republish page template.
 *
 * @since 1.6.0
 *
 * @package Republication_Tracker_Tool
 */

use Newspack\Newspack_Image_Credits;

get_header();

global $allowedposttags;
$allowed_tags_excerpt = $allowedposttags;
unset( $allowed_tags_excerpt['form'] );

/**
 * Allow sites to configure which tags are allowed to be output in the republication content
 *
 * Default value is the standard global $allowedposttags, except form elements.
 *
 * @link https://github.com/Automattic/republication-tracker-tool/issues/49
 * @link https://developer.wordpress.org/reference/functions/wp_kses_allowed_html/
 *
 * @param Array $allowed_tags_excerpt an associative array of element tags that are allowed
 */
$allowed_tags_excerpt = apply_filters( 'republication_tracker_tool_allowed_tags_excerpt', $allowed_tags_excerpt, $post );

// Get post ID from query var.
$republish_post_id = get_query_var( 'republish_post_id' );

// Get post object.
$post_object = get_post( $republish_post_id );

// Check if the post object is valid.
if ( ! $post_object instanceof WP_Post ) {
	wp_die( esc_html__( 'Invalid post ID.', 'republication-tracker-tool' ) );
}

$content = $post_object->post_content;

// Remove shortcodes.
$content = strip_shortcodes( $content );

// Remove comments from content.
$content = preg_replace( '/<!--(.|\s)*?-->/i', '', $content );

// Remove some tags.
$content = wp_kses( $content, $allowed_tags_excerpt );

// Remove empty paragraphs.
$content = str_replace( '<p></p>', '', wpautop( $content ) );

// Remove the image if "can distribute" is not set.
if ( class_exists( '\Newspack\Newspack_Image_Credits' ) ) {

	// Find all the attachments id in the content.
	preg_match_all( '/<img[^>]+class="wp-image-(\d+)"[^>]*>/', $content, $matches );
	$found_images = array();

	foreach ( $matches[1] as $key => $attachment_id ) {
		$can_distribute = get_post_meta( $attachment_id, Newspack_Image_Credits::MEDIA_CREDIT_CAN_DISTRIBUTE_META, true );

		if ( empty( $can_distribute ) ) {
			$found_images[ $attachment_id ] = $matches[0][ $key ];
		}
	}

	// Suppress the found images if "can distribute" is not set.
	foreach ( $found_images as $attachment_id => $found_image ) {
		// Remove the figure and figcaption of $found_image using regex.
		$pattern = '/<figure[^>]*>' . preg_quote( $found_image, '/' ) . '.*?<\/figure>/s';
		$content = preg_replace( $pattern, '', $content );
	}
}

// Apply filters to the content.
$content = apply_filters( 'republication_tracker_tool_republish_content', $content, $post_object );

// Get the license statement.
$license_statement = get_option( 'republication_tracker_tool_policy' );

// Article title.
$article_title = get_the_title( $republish_post_id );

// Get article subtitle.
$article_subtitle = get_post_meta( $republish_post_id, 'newspack_post_subtitle', true );

// Get article author byline.
$author_byline = apply_filters( 'republication_tracker_tool_author_byline', '', $republish_post_id );

// Get article date and time in current timezone.
$article_date_gmt = get_post_time( 'U', true, $republish_post_id ); // Get post date in GMT.

// Convert GMT date to the current timezone.
$time_zone_string = get_option( 'timezone_string', 'UTC' );

if ( empty( $time_zone_string ) ) {
	$time_zone_string = 'UTC';
}

$current_timezone = new DateTimeZone( $time_zone_string ); // Get current timezone.
$article_date     = new DateTime();
$article_date->setTimestamp( $article_date_gmt );
$article_date->setTimezone( $current_timezone );
$article_date = $article_date->format( 'M j g:ia T' );

// Featured image.
$featured_image = get_the_post_thumbnail( $republish_post_id, 'full' );

if ( class_exists( '\Newspack\Newspack_Image_Credits' ) ) {
	$featured_media_id             = get_post_thumbnail_id( $republish_post_id );
	$can_distribute_featured_image = get_post_meta( $featured_media_id, Newspack_Image_Credits::MEDIA_CREDIT_CAN_DISTRIBUTE_META, true );

	if ( empty( $can_distribute_featured_image ) ) {
		$featured_image = '';
	}
}

// Canonical URL.
$canonical_url = get_permalink( $republish_post_id );
$canonical_tag = sprintf(
	'<link rel="canonical" href="%s" />',
	esc_url( $canonical_url )
);

// Get the content footer.
$content_footer = Republication_Tracker_Tool::create_content_footer( $post_object );

// Republish content.
$republish_content = '';

// Add the article title to the republish content.
if ( ! empty( $article_title ) ) {
	$republish_content .= sprintf(
		'<h1>%s</h1>',
		esc_html( $article_title )
	);
}

// Add the article subtitle to the republish content. Retrievable via Newspack's newspack_post_subtitle meta key.
if ( ! empty( $article_subtitle ) ) {
	$republish_content .= sprintf(
		"\n\n<h2>%s</h2>",
		esc_html( $article_subtitle )
	);
}

// Add the article author to the republish content.
if ( ! empty( $author_byline ) ) {
	$republish_content .= sprintf(
		"\n\n<div>%s</div>",
		$author_byline
	);
}

// Add the article date to the republish content.
if ( ! empty( $article_date ) ) {
	$republish_content .= sprintf(
		"\n\n<time>%s</time>",
		esc_html( $article_date )
	);
}

// Add the featured image to the republish content.
if ( ! empty( $featured_image ) ) {
	$republish_content .= sprintf(
		"\n\n%s",
		$featured_image
	);
}

// Add the content to the republish content.
if ( ! empty( $content ) ) {
	$republish_content .= sprintf(
		"\n%s",
		$content
	);
}

// Add the canonical URL to the republish content.
if ( ! empty( $canonical_tag ) ) {
	$republish_content .= sprintf(
		"\n\n%s",
		$canonical_tag
	);
}

// Add the content footer to the republish content.
if ( ! empty( $content_footer ) ) {
	$republish_content .= sprintf(
		"\n\n%s",
		html_entity_decode( $content_footer )
	);
}

// Remove css classes from content.
$republish_content = preg_replace( '/ class=".*?"/', '', $republish_content );

// Remove srcset attribute from images.
$republish_content = preg_replace( '/ srcset=".*?"/', '', $republish_content );

// Remove sizes attribute from images.
$republish_content = preg_replace( '/ sizes=".*?"/', '', $republish_content );

// Filter the republish content.
$republish_content = apply_filters( 'republication_tracker_tool_republish_article_markup', $republish_content, $post_object );
?>

<section id="primary" class="content-area">
		<main class="site-main">
			<article class="republish-article">
				<h3><?php esc_html_e( 'Republish this article', 'republication-tracker-tool' ); ?></h3>
				<h1><?php echo esc_html( $post_object->post_title ); ?></h1>

				<?php do_action( 'republication_tracker_tool_before_republish_content', $post_object ); ?>

				<div class="republish-article__content">
					<?php if ( ! empty( $license_statement ) ) : ?>
						<section class="republish-article__license">
							<?php echo wp_kses_post( $license_statement ); ?>
						</section>
					<?php endif; ?>
					<section class="republish-article__info">
						<textarea rows="19" readonly aria-readonly="true" aria-label="<?php esc_attr_e( 'Republish this article', 'republication-tracker-tool' ); ?>"><?php echo esc_html( $republish_content ); ?></textarea>
						<button class="republish-article__copy-button" aria-label="<?php esc_attr_e( 'Copy to clipboard', 'republication-tracker-tool' ); ?>">
							<?php esc_html_e( 'Copy to clipboard', 'republication-tracker-tool' ); ?>
						</button>
					</section>
				</div>

				<?php do_action( 'republication_tracker_tool_after_republish_content', $post_object ); ?>
			</article>
		</main>
</section>

<?php

get_footer();
