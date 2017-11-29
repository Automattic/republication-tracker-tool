<?php
require_once( '../../../../wp-load.php' );

if ( ! isset( $_GET['post'] ) ) {
	return;
}

$post = get_post( intval( $_GET['post'] ) );
$content = $post->post_content;

$attribution_statement = sprintf( esc_html__( 'This <a target="_blank" href="%s">article</a> first appeared on <a target="_blank" href="%s">%s</a> and is republished here under a Creative Commons license.', 'creative-commons-sharing' ), get_permalink( $post ), home_url(), get_bloginfo() );
$pixel = sprintf( '<script type="text/javascript" id="creative-commons-sharing-source" src="%s" data-postid="%s" data-pluginsdir="%s" async="true"></script>', plugins_url( 'assets/pixel.js', dirname( __FILE__ ) ), $post->ID, plugins_url() );
$license_statement = get_option( 'creative_commons_sharing_policy' );

global $shortcode_tags;
if ( is_array( $shortcode_tags ) ) {
	foreach ( $shortcode_tags as $tag ) {
		$content = str_replace( $tag, '', $content );
	}
}


// remove images
$content = preg_replace( "/<img[^>]+\>/i", " ", $content );

// force UTF-8
$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

echo '<div id="creative-commons-share-modal-content">';
	echo '<div class="creative-commons-close">X</div>';
	echo sprintf( '<h2>%s</h2>', esc_html__( 'Republish this article', 'creative-commons-sharing' ) );
	echo '<div class="cc-policy">';
		echo '<div class="cc-license">';
			echo sprintf( '<a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="%s" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a>', esc_html__( 'Creative Commons License' ) );
			echo wpautop(
				sprintf(
					__( 'This work is licensed under a <a rel="license" target="_blank" href="%s">Creative Commons Attribution-NoDerivatives 4.0 International License</a>.' ),
					'http://creativecommons.org/licenses/by-nd/4.0/'
				)
			);
		echo '</div>';
		echo $license_statement;
	echo '</div>';
	echo '<div class="article-info">';
		echo sprintf(
			'<h1>%s</h1><p class="byline">%s <br />%s</p>',
			$post->post_title,
			esc_html__( 'by', 'creative-commons-sharing' ) . ' ' . get_the_author_meta( 'display_name', $post->post_author ) . ', ' . get_bloginfo( 'name' ),
			date( 'F j, Y', strtotime( $post->post_date ) )
		);
	echo '</div>';
	echo sprintf( '
		<textarea id="creative-commons-shareable-content" rows="5"><h1>%s</h1><p class="byline">%s <br />%s</p>%s</textarea>',
		$post->post_title,
		esc_html__( 'by', 'creative-commons-sharing' ) . ' ' . get_the_author_meta( 'display_name', $post->post_author ),
		get_bloginfo( 'name' ) . ', ' . date( 'F j, Y', strtotime( $post->post_date ) ),
		wpautop( $content . "\n\n" . $attribution_statement . $pixel )
	);
	echo wpautop( sprintf( '<button onclick="copyToClipboard(\'#creative-commons-shareable-content\')">%s</button>', esc_html__( 'Copy to Clipboard', 'creative-commons-sharing' ) ) );
echo '</div>';
