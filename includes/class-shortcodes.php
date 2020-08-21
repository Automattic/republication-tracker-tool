<?php
/**
 * The [republication_button] shortcode and related functions
 *
 * @package Republication_Tracker_Tool
 * @link https://github.com/INN/republication-tracker-tool/issues/66#issuecomment-671607012
 */

class Republication_Tracker_Tool_Shortcodes {
	/**
	 * A shortcode to output the button that opens the Republication Tracking Tool modal
	 *
	 * This function also powers the Republication Modal Button Block output in Gutenberg,
	 * as the render callback for a dynamic block
	 *
	 * @uses Republication_Tracker_Tool_Widget::button_output()
	 * @uses Republication_Tracker_Tool_Widget::maybe_print_modal_content()
	 * @param Array  $atts    the attributes passed in the shortcode.
	 * @param String $content the enclosed content; should be empty for this shortcode.
	 * @param String $tag     the shortcode tag.
	 * @return String the button HTML
	 */
	public static function button_shortcode( $atts = array(), $content = '', $tag = '' ) {
		global $post;

		wp_enqueue_script( 'republication-tracker-tool-js', plugins_url( 'assets/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), Republication_Tracker_Tool::VERSION, false );
		wp_enqueue_style( 'republication-tracker-tool-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), Republication_Tracker_Tool::VERSION );
		add_action( 'wp_ajax_my_action', 'my_action' );
		add_action( 'wp_ajax_nopriv_my_action', 'my_action' );

		echo Republication_Tracker_Tool_Widget::button_output();
		Republication_Tracker_Tool_Widget::maybe_print_modal_content();
	}
}
