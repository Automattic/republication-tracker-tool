<?php
/**
 * Plugin Name:     Creative Commons Sharing
 * Description:     Allow readers to share your content via a creative commons license.
 * Author:          INN Labs
 * Author URI:      https://labs.inn.org
 * Text Domain:     creative-commons-sharing
 * Domain Path:     /languages
 * Version:         1.0.1
 *
 * @package         Creative_Commons_Sharing
 */

require 'includes/class-settings.php';
require 'includes/class-article-settings.php';
require 'includes/class-widget.php';

/**
* Main initiation class.
*
* @since  1.0
*/
final class Creative_Commons_Sharing {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const VERSION = '1.0.1';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Creative_Commons_Sharing
	 * @since  1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of Creative_Commons_Sharing_Settings
	 *
	 * @since 1.0
	 * @var Creative_Commons_Sharing_Settings
	 */
	protected $settings;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0
	 * @return  Creative_Commons_Sharing A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'creative-commons-sharing', false, dirname( $this->basename ) . '/languages/' );

		$this->settings = new Creative_Commons_Sharing_Settings( $this );
		$this->article_settings = new Creative_Commons_Sharing_Article_Settings( $this );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

	}


	/**
	 * Register our widgets.
	 *
	 * @since 1.0
	 */
	public function register_widgets() {
		register_widget( 'Creative_Commons_Sharing_Widget' );
	}


	/**
	 * Activate the plugin.
	 *
	 * @since  1.0
	 */
	public function _activate() {}

	/**
	 * Deactivate the plugin.
	 *
	 * @since  1.0
	 */
	public function _deactivate() {}
}

/**
* Grab the Creative_Commons_Sharing object and return it.
* Wrapper for Creative_Commons_Sharing::get_instance().
*
* @since  1.0
* @return Creative_Commons_Sharing  Singleton instance of plugin class.
*/
function Creative_Commons_Sharing() {
	return Creative_Commons_Sharing::get_instance();
}

add_action( 'plugins_loaded', array( Creative_Commons_Sharing(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( Creative_Commons_Sharing(), '_activate' ) );
register_deactivation_hook( __FILE__, array( Creative_Commons_Sharing(), '_deactivate' ) );
