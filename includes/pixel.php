<?php
require_once("../../../../wp-load.php");

if ( isset( $_GET['post'] ) ) {

	$post_id = absint( $_GET['post'] );
	$value = intval( get_post_meta( $post_id, 'creative_commons_sharing', true ) ) + 1;

	$update = update_post_meta( $post_id, 'creative_commons_sharing', $value );
}

// Output the image
header('Content-Type: image/gif');

// This echo is equivalent to read an image, readfile('pixel.gif')
echo "\x47\x49\x46\x38\x37\x61\x1\x0\x1\x0\x80\x0\x0\xfc\x6a\x6c\x0\x0\x0\x2c\x0\x0\x0\x0\x1\x0\x1\x0\x0\x2\x2\x44\x1\x0\x3b";
