<?php
require_once( '../../../../wp-load.php' );

if ( isset( $_GET['post'] ) && isset( $_GET['url'] ) ) {

	$post_id = absint( $_GET['post'] );
	$url = esc_url_raw( $_GET['url'] );
	$value = get_post_meta( $post_id, 'creative_commons_sharing', true );
	if ( isset( $value[ $url ] ) ) {
		$value[ $url ]++;
	} else {
		$value[ $url ] = 1;
	}

	$update = update_post_meta( $post_id, 'creative_commons_sharing', $value );
}
