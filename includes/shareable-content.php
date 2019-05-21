<?php
/**
 * This file provides an AJAX response containing the body of a post as well as some sharing information.
 *
 * Expected URLs is something like /wp-content/plugins/creative-commons-sharing/includes/shareable-content.php?post=22078&_=1512494948576 or something
 * We aren't passing a NONCE; this isn't a form.
 */

require_once( '../../../../wp-load.php' );

if ( ! isset( $_GET['post'] ) ) {
	return;
}

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
unset( $allowed_tags_excerpt['audio'] );
unset( $allowed_tags_excerpt['figure'] );
unset( $allowed_tags_excerpt['figcaption'] );
unset( $allowed_tags_excerpt['img'] );
unset( $allowed_tags_excerpt['form'] );
unset( $allowed_tags_excerpt['button'] );
unset( $allowed_tags_excerpt['track'] );
unset( $allowed_tags_excerpt['video'] );


/**
 * The article WP_Post object
 *
 * @var WP_Post $post the post ID
 */
$post = get_post( intval( $_GET['post'] ) );

/**
 * The content of the aforementioned post
 *
 * @var HTML $content
 */
$content = $post->post_content;

// Remove shortcodes from the content.
$content = strip_shortcodes( $content );

// Remove comments from the content. (Lookin' at you, Gutenberg.)
$content = preg_replace( '/<!--(.|\s)*?-->/i', ' ', $content );

// Remove captions and figures from the content
$content = preg_replace( '/<figure[^>]?\>(.|\s)*?<\/figure>/i', ' ', $content );
$content = preg_replace( '/<figcaption[^>]?\>(.|\s)*?<\/figcaption>/i', ' ', $content );

// And finally, remove some tags.
$content = wp_kses( $content, $allowed_tags_excerpt );

// remove spare p tags and clean up these paragraphs
$content = str_replace( '<p></p>', '', wpautop( $content ) );

// Force the content to be UTF-8 escaped HTML.
$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

// grab our analytics id to pass as GA param
$analytics_id = get_option( 'creative_commons_analytics_id' );



/**
 * The article source
 *
 * @var HTML $attribution_statment
 */
$attribution_statement = sprintf(
	// translators: %1$s is a URL, %2$s is the site home URL, and %3$s is the site title.
	esc_html__( 'This <a target="_blank" href="%1$s">article</a> first appeared on <a target="_blank" href="%2$s">%3$s</a> and is republished here under a Creative Commons license.', 'creative-commons-sharing' ),
	get_permalink( $post ),
	home_url(),
	esc_html( get_bloginfo() )
);

/**
 * The "pixel" tag for tracking embeds
 * WordPress Core PHPCS complains about this, but it's invalid
 *
 * @var HTML $pixel The tracking tag, which is a script tag.
 */
$pixel = sprintf(
	// %1$s is the javascript source, %2$s is the post ID, %3$s is the plugins URL
	'<img id="creative-commons-sharing-source" src="%1$s?post=%2$s&ga=%3$s">',
	plugins_url( 'includes/pixel.php', dirname( __FILE__ ) ),
	esc_attr( $post->ID ),
	esc_attr( $analytics_id )
);

/**
 * The article title, byline, source site, and date
 *
 * @var HTML $article_info The article title, etc.
 */
$article_info = sprintf(
	// translators: %1$s is the post title, %2$s is the byline, %3$s is the site name, %4$s is the date in the format F j, Y
	__( '<h1>%1$s</h1><p class="byline">by %2$s, %3$s <br />%4$s</p>', 'creative-commons-sharing' ),
	wp_kses_post( get_the_title( $post ) ),
	wp_kses_post( get_the_author_meta( 'display_name', $post->post_author ) ),
	wp_kses_post( get_bloginfo( 'name' ) ),
	wp_kses_post( date( 'F j, Y', strtotime( $post->post_date ) ) )
);
// strip empty tags after automatically applying p tags
$article_info = str_replace( '<p></p>', '', wpautop( $article_info ) );

/**
 * The licensing statement from this plugin
 *
 * @var HTML $license_statement
 */
$license_statement = wp_kses_post( get_option( 'creative_commons_sharing_policy' ) );

echo '<div id="creative-commons-share-modal-content">';
	echo '<div class="creative-commons-close">X</div>';
	echo sprintf( '<h2>%s</h2>', esc_html__( 'Republish this article', 'creative-commons-sharing' ) );

	// Explain Creative Commons
	echo '<div class="cc-policy">';
		echo '<div class="cc-license">';
			echo sprintf( '<a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="%s" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a>', esc_html__( 'Creative Commons License' ) );
			echo wpautop(
				sprintf(
					// translators: %1$s is the URL to the particular Creative Commons license.
					__( 'This work is licensed under a <a rel="license" target="_blank" href="%1$s">Creative Commons Attribution-NoDerivatives 4.0 International License</a>.' ),
					'http://creativecommons.org/licenses/by-nd/4.0/'
				)
			);
		echo '</div>'; // .cc-license
		echo wp_kses_post( $license_statement );
	echo '</div>'; // .cc-policy

	// what we display to the embedder
	echo '<div class="article-info">';
		echo $article_info;
	echo '</div>'; // .article-info

	// the text area that is copyable
	echo sprintf(
		'<textarea readonly id="creative-commons-shareable-content" rows="5">%1$s %2$s %3$s</textarea>',
		esc_html( $article_info ),
		$content . "\n\n" ,
		wpautop( $attribution_statement . $pixel )
	);
	echo wpautop(
		sprintf( '<button onclick="copyToClipboard(\'#creative-commons-shareable-content\')">%s</button>', esc_html__( 'Copy to Clipboard', 'creative-commons-sharing' ) )
	);
echo '</div>'; // #creative-commons-share-modal-content
