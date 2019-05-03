<?php
require_once( '../../../../wp-load.php' );

// function to get the title of the referring url
function get_page_title( $url ){
	
	// create curl request with a max timeout of 2 seconds
	$ch = curl_init();

	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 2 );

	// execute our curl request
	// then close it out
	$response = curl_exec( $ch );
	$error = curl_error( $ch );
	curl_close( $ch );

	// if there was an error, don't continue
	if( $error ){

		return;
	
	// everything looks good, let's carry on getting the title
	} else {

		// find the title element inside of the response body
		$response = preg_match( "/<title>(.*)<\/title>/siU", $response, $title_matches );

		// if a title element was found, let's get the text from it
		if( $title_matches ){

			// clean up title: remove EOL's and excessive whitespace.
			$title = preg_replace( '/\s+/', ' ', $title_matches[1] );
			$title = trim( $title );
			$title = urlencode( $title );

			// return our found title
			return $title;
		
		// if there were no title matches found, don't continue
		} else {

			return;

		}

	}

}
		

if ( isset( $_GET['post'] ) ) {

	// set up all of our post vars we want to track
	$post_id = absint( $_GET['post'] );
	$post = get_post($post_id);
	$post_slug = urlencode($post->post_name);
	$post_permalink = get_permalink($post_id);

	if( array_key_exists( 'HTTP_REFERER', $_SERVER ) ){

		$url = $_SERVER['HTTP_REFERER'];

		$url_title = get_page_title( $url );
		$url_title = str_replace( ' ', '%20', $url_title );

		$url_host = parse_url( $url, PHP_URL_HOST );
		$url_path = parse_url( $url, PHP_URL_PATH );

	} else {

		$url = '';
		$url_title = '';

	}

	$value = get_post_meta( $post_id, 'creative_commons_sharing', true );
	if ( $value ) {
		if ( isset( $value[ $url ] ) ) {
			$value[ $url ]++;
		} else {
			$value[ $url ] = 1;
		}
	} else {
		$value = array(
			$url => 1
		);
	}
	$update = update_post_meta( $post_id, 'creative_commons_sharing', $value );

	// if our google analytics tag is set, let's push data to it
	if( isset( $_GET['ga'] ) &&  !empty($_GET['ga'] ) ) {

		// our base url to ping GA at
		$analytics_ping_url = "https://www.google-analytics.com/collect?v=1";

		// create all of our necessary params to track
		// the docs for these params can be found at: https://developers.google.com/analytics/devguides/collection/analyticsjs/events
		$analytics_ping_params = array(
			'tid' => $_GET['ga'],
			'cid' => '555',
			't' => 'pageview',
			'dl' => $post_permalink,
			'dh' => $url_host,
			'dp' => $page_slug,
			'dr' => $url,
			'dt' => $url_title,
			'an' => 'Republication',
			'aid' => $url_host,
			'av' => 'Republication Tracker v1'
		);

		// create query based on our params array
		$analytics_ping_params = http_build_query( $analytics_ping_params );

		// create curl request
		$ch = curl_init(); 

		curl_setopt( $ch, CURLOPT_URL, $analytics_ping_url.'&'.$analytics_ping_params ); 
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

		// $response contains the response string 
		$response = curl_exec( $ch ); 

		// close curl resource to free up system resources 
		curl_close( $ch );      

	}

}

// grab our site icon and redirect to it once the script finishes
$site_icon_url = get_site_icon_url();
wp_safe_redirect($site_icon_url, 303);
exit;