<?php
/**
 * This file provides an AJAX response containing the body of a post as well as some sharing information.
 *
 * This operates within The Loop. $post is set to a specific post.
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
 * The content of the aforementioned post
 *
 * @var HTML $content
 */
$content = Republication_Tracker_Tool_Content::get_republishable_content( $post->post_content );

$content_footer = Republication_Tracker_Tool::create_content_footer( $post );

/**
 * The article title, byline, source site, and date
 *
 * @var HTML $article_info The article title, etc.
 */
$article_info = sprintf(
	// translators: %1$s is the post title, %2$s is the byline, %3$s is the site name, %4$s is the date in the format F j, Y.
	__( '<h1>%1$s</h1><p class="byline">by %2$s, %3$s <br />%4$s</p>', 'republication-tracker-tool' ),
	wp_kses_post( get_the_title( $post ) ),
	/**
	 * Allow filtering of the byline that is output in the share dialog and the copyable plaintext.
	 *
	 * This is to provide support for plugins that do not implement
	 * a filter on 'the_author', or in cases where the 'the_author'
	 * filter returns incomplete information.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_the_author/
	 * @link https://github.com/INN/republication-tracker-tool/issues/46
	 */
	wp_kses_post( apply_filters( 'republication_tracker_tool_byline', get_the_author() ) ),
	wp_kses_post( get_bloginfo( 'name' ) ),
	wp_kses_post( date( 'F j, Y', strtotime( $post->post_date ) ) )
);
// strip empty tags after automatically applying p tags.
$article_info = str_replace( '<p></p>', '', wpautop( $article_info ) );

/**
 * The licensing statement from this plugin
 *
 * @var HTML $license_statement
 */
$license_statement = wp_kses_post( get_option( 'republication_tracker_tool_policy' ) );
$license_key = get_option( 'republication_tracker_tool_license', 'cc-by-nd-4.0' );

echo '<div id="republication-tracker-tool-modal-content" ' . ( $is_amp ? '' : 'style="display:none;"' ) . '>';
	echo '<button ' . ( $is_amp ? 'on="tap:republication-tracker-tool-modal.close"' : '' ) . ' class="republication-tracker-tool-close">';
	echo '<span class="screen-reader-text">' . esc_html( 'Close window', 'republication-tracker-tool' ) . '</span> <span aria-hidden="true">X</span></button>';
	printf( '<h2 id="republish-modal-label">%s</h2>', esc_html__( 'Republish this article', 'republication-tracker-tool' ) );

	// Explain Creative Commons
	echo '<div class="cc-policy">';
		echo '<div class="cc-license">';
			printf( '<a rel="noreferrer license" target="_blank" href="%s" /></a>', REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ]['badge'], esc_html__( 'Creative Commons License', 'republication-tracker-tool' ) );
			echo wp_kses_post(
				wpautop(
					sprintf(
						// translators: %1$s is the URL to the particular Creative Commons license.
						__( 'This work is licensed under a <a rel="noreferrer license" target="_blank" href="%1$s">%2$s</a>.', 'republication-tracker-tool' ),
						REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ]['url'],
						REPUBLICATION_TRACKER_TOOL_LICENSES[ $license_key ]['description'],
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
			?>
				<textarea readonly id="republication-tracker-tool-shareable-content" rows="5">
					<?php echo esc_html( $article_info ); ?>
					<?php echo $content; ?>

					<?php echo htmlspecialchars( $content_footer, ENT_QUOTES, 'UTF-8' ); ?>
				</textarea>
			<?php
			if ( ! $is_amp ) {
				?>
			<button onclick="copyToClipboard('#republication-tracker-tool-shareable-content', this)"><?php echo esc_html__( 'Copy to Clipboard', 'republication-tracker-tool' ); ?></button>
				<?php
			}

			echo '</div>'; // #republication-tracker-tool-modal-content
