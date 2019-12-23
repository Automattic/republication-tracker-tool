<?php
/**
 * This file provides an AJAX response containing the body of a post as well as some sharing information.
 *
 * Expected URLs is something like /wp-content/plugins/republication-tracker-tool/includes/shareable-content.php?post=22078&_=1512494948576 or something
 * We aren't passing a NONCE; this isn't a form.
 */

/**
 * The article WP_Post object
 *
 * @var WP_Post $post the post object
 */
global $post;

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
 * Allow sites to configure which tags are allowed to be output in the republication content
 * 
 * Default value is the standard global $allowedposttags, except form elements.
 *
 * @link https://github.com/INN/republication-tracker-tool/issues/49
 * @link https://developer.wordpress.org/reference/functions/wp_kses_allowed_html/
 * @param Array $allowed_tags_excerpt an associative array of element tags that are allowed
 */
$allowed_tags_excerpt = apply_filters( 'republication_tracker_tool_allowed_tags_excerpt', $allowed_tags_excerpt, $post );

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

// And finally, remove some tags.
$content = wp_kses( $content, $allowed_tags_excerpt );

// remove spare p tags and clean up these paragraphs
$content = str_replace( '<p></p>', '', wpautop( $content ) );

// Force the content to be UTF-8 escaped HTML.
$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

// grab our analytics id to pass as GA param
$analytics_id = get_option( 'republication_tracker_tool_analytics_id' );



/**
 * The article source
 *
 * @var HTML $attribution_statment
 */
$attribution_statement = sprintf(
	// translators: %1$s is a URL, %2$s is the site home URL, and %3$s is the site title.
	esc_html__( 'This <a target="_blank" href="%1$s">article</a> first appeared on <a target="_blank" href="%2$s">%3$s</a> and is republished here under a Creative Commons license.', 'republication-tracker-tool' ),
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
	'<img id="republication-tracker-tool-source" src="%1$s/?republication-pixel=true&post=%2$s&ga=%3$s" style="max-width:1em;">',
	esc_attr( get_site_url( ) ),
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
	__( '<h1>%1$s</h1><p class="byline">by %2$s, %3$s <br />%4$s</p>', 'republication-tracker-tool' ),
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
$license_statement = wp_kses_post( get_option( 'republication_tracker_tool_policy' ) );

echo '<div id="republication-tracker-tool-modal-content" style="display:none;">';
	echo '<div class="republication-tracker-tool-close">X</div>';
	echo sprintf( '<h2>%s</h2>', esc_html__( 'Republish this article', 'republication-tracker-tool' ) );

	// Explain Creative Commons
	echo '<div class="cc-policy">';
		echo '<div class="cc-license">';
			echo sprintf( '<a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="%s" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a>', esc_html__( 'Creative Commons License' ) );
			echo wp_kses_post( 
				wpautop(
					sprintf(
						// translators: %1$s is the URL to the particular Creative Commons license.
						__( 'This work is licensed under a <a rel="license" target="_blank" href="%1$s">Creative Commons Attribution-NoDerivatives 4.0 International License</a>.' ),
						'http://creativecommons.org/licenses/by-nd/4.0/'
					)
				)
			);
		echo '</div>'; // .cc-license
		echo wp_kses_post( $license_statement );
	echo '</div>'; // .cc-policy

	// what we display to the embedder
	echo '<div class="article-info">';
		echo wp_kses_post( $article_info );
	echo '</div>'; // .article-info

	// the text area that is copyable
	echo wp_kses_post( 
		sprintf(
			'<textarea readonly id="republication-tracker-tool-shareable-content" rows="5">%1$s %2$s %3$s</textarea>',
			esc_html( $article_info ),
			$content . "\n\n" ,
			wpautop( $attribution_statement . $pixel )
		)
	);
	?>
	<button onclick="copyToClipboard('#republication-tracker-tool-shareable-content')"><?php echo esc_html__( 'Copy to Clipboard', 'republication-tracker-tool' ); ?></button>
	<?php
echo '</div>'; // #republication-tracker-tool-modal-content
