<?php
/**
 * Creative Commons Sharing Article Settings.
 *
 * @since   1.0
 * @package Creative_Commons_Sharing
 */

/**
 * Creative Commons Sharing Article Settings class.
 *
 * @since 1.0
 */
class Creative_Commons_Sharing_Article_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Creative_Commons_Sharing
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Creative_Commons_Sharing $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
	}

	/**
	 * Add custom metaboxes.
	 *
	 * @since 1.0
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'creative-commons-sharing',
			esc_html__( 'Creative Commons Sharing', 'creative-commons-sharing' ),
			array( $this, 'render_metabox' ),
			array( 'post', 'page' ),
			'advanced',
			'default'
		);
	}

	/**
	 * Render a custom metabox
	 *
	 * @since 1.0
	 * @param obj $post Post object.
	 * @param obj $args Arguments object.
	 */
	public function render_metabox( $post, $args ) {
		$shares = get_post_meta( $post->ID, 'creative_commons_sharing', true );
		echo '<table class="wp-list-table widefat fixed striped posts">';
			echo '<thead>';
				echo '<th scope="col" id="url" class="manage-column column-primary"><span>URL</span><span class="sorting-indicator"></span></th>';
				echo '<th scope="col" id="views" class="manage-column "># of Views</th>';
			echo '</thead>';
			echo '<tbody id="the-list">';
				foreach ( $shares as $url => $count ) {
					echo sprintf(
						'<tr><td class="column-primary" data-colname="URL"><a href="%1$s" target="_blank">%1$s</a></td><td class="views" data-colname="Views">%2$s</td></tr>',
						$url,
						$count
					);
				}
			echo '</tbody>';
			echo '<tfoot>';
				echo '<th scope="col" id="url" class="manage-column column-primary"><span>URL</span><span class="sorting-indicator"></span></th>';
				echo '<th scope="col" id="views" class="manage-column"># of Views</th>';
			echo '</tfoot>';
		echo '</table>';
	}
}
