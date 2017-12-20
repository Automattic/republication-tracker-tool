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

$post = get_post( intval( $_GET['post'] ) );
$content = $post->post_content;

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
	'<script type="text/javascript" id="creative-commons-sharing-source" src="%1$s" data-postid="%2$s" data-pluginsdir="%3$s" async="true"></script>',
	plugins_url( 'assets/pixel.js', dirname( __FILE__ ) ),
	esc_attr( $post->ID ),
	plugins_url()
);

/**
 * The article title, byline, source site, and date
 *
 * @var HTML $article_info The article title, etc.
 */
$article_info = sprintf(
	// translators: %1$s is the post title, %2$s is the byline, %3$s is the site name, %4$s is the date in the format F j, Y
	esc_html__( '<h1>%1$s</h1><p class="byline">by %2$s, %3$s <br />%4$s</p>', 'creative-commons-sharing' ),
	wp_kses_post( get_the_title( $post ) ),
	wp_kses_post( get_the_author_meta( 'display_name', $post->post_author ) ),
	wp_kses_post( get_bloginfo( 'name' ) ),
	wp_kses_post( date( 'F j, Y', strtotime( $post->post_date ) ) )
);

/**
 * The licensing statement from this plugin
 *
 * @var HTML $license_statement
 */
$license_statement = wp_kses_post( get_option( 'creative_commons_sharing_policy' ) );

global $shortcode_tags;
if ( is_array( $shortcode_tags ) ) {
	foreach ( $shortcode_tags as $tag ) {
		$content = str_replace( $tag, '', $content );
	}
}


// Remove images from the content
$content = preg_replace( '/<img[^>]+\>/i', ' ', $content );

// force the content to be UTF-8 escaped HTML.
$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

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
		'<textarea id="creative-commons-shareable-content" rows="5">%2$s</textarea>',
		wpautop( $article_info . $content . "\n\n" . $attribution_statement . $pixel )
	);
	echo wpautop(
		sprintf( '<button onclick="copyToClipboard(\'#creative-commons-shareable-content\')">%s</button>', esc_html__( 'Copy to Clipboard', 'creative-commons-sharing' ) )
	);
echo '</div>'; // #creative-commons-share-modal-content
