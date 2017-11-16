<?php
/**
 * Creative Commons Sharing Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */
/**
 * Creative Commons Sharing Settings class.
 *
 * @since 1.0
 */
 class Creative_Commons_Sharing_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'creative_commons_sharing',
			'description' => 'Creative Commons Sharing',
		);
		parent::__construct( 'creative_commons_sharing', 'Creative Commons Sharing', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( ! is_single() ) {
			return;
		}

		global $post;

		wp_enqueue_script( 'creative-commons-sharing-js', plugins_url( 'assets/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0', false );
		wp_enqueue_style( 'creative-commons-sharing-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), '1.0' );
		add_action( 'wp_ajax_my_action', 'my_action' );
		add_action( 'wp_ajax_nopriv_my_action', 'my_action' );

		echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

		$attribution_statement = sprintf( 'This <a target="_blank" href="%s">article</a> first appeared on <a target="_blank" href="%s">%s</a> and is republished here under a Creative Commons license.', get_permalink( $post ), home_url(), get_bloginfo() );
//		$pixel = sprintf( '<img src="%s" />', home_url( '/wp-content/plugins/creative-commons-sharing/includes/pixel.php?post=' . $post->ID ) );
		$pixel = sprintf( '<script type="text/javascript" id="creative-commons-sharing-source" src="%s" data-postid="%s" data-homeurl="%s" async="true"></script>', home_url( '/wp-content/plugins/creative-commons-sharing/assets/pixel.js' ), $post->ID, home_url() );
		$license_statement = get_option( 'creative_commons_sharing_policy' );

		$content = $post->post_content;
		global $shortcode_tags;
		if ( is_array( $shortcode_tags ) ) {
			foreach ( $shortcode_tags as $tag ) {
				$content = str_replace( $tag, '', $content );
			}
		}

		// remove images
		$content = preg_replace( "/<img[^>]+\>/i", " ", $content );

		// remove captions
		$content = preg_replace( "/<(div|figure)[^>]+[wp\-caption][^>]+\>.*<\/(div|figure)>/i", " ", $content );

		$content = htmlspecialchars( $content, ENT_HTML5, 'UTF-8', true );

		echo '<div id="creative-commons-share-modal" style="display:none;">';
			echo '<div id="creative-commons-share-modal-content">';
				echo '<div class="creative-commons-close">X</div>';
				echo '<h2>Republish this article</h2>';
				echo '<div class="cc-policy">';
					echo '<div class="cc-license"><a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a><p>This work is licensed under a <a rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/">Creative Commons Attribution-NoDerivatives 4.0 International License</a>.</p></div>';
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
				echo wpautop( '<button onclick="copyToClipboard(\'#creative-commons-shareable-content\')">Copy to Clipboard</button>' );
			echo '</div>';
		echo '</div>';

		echo '<div class="license">';
			echo '<p><button name="Republish This Story" id="cc-btn" class="creative-commons-button">Republish this article</button></p>';
			echo '<p><a class="license" rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a></p>';
		echo '</div>';

		echo sprintf( '<div class="message">%s</div>', wpautop( esc_html__( $instance['text'], 'creative-commons-sharing' ) ) );
		?>


		<?php
        echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		echo sprintf( '<p><em>%s</em></p>', esc_html__( 'This widget will only display on single articles.', 'creative-commons-sharing' ) );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        $text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( 'Republish our articles for free, online or in print, under Creative Commons license.', 'text_domain' );
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
        </p>
        <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';

        return $instance;
	}
}
