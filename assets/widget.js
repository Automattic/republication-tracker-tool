function copyToClipboard( element ) {
	var $temp = jQuery( '<input>' );
	jQuery( 'body' ).append( $temp );
	$temp.val( jQuery( element ).text() ).select();
	document.execCommand( 'copy' );
	$temp.remove();
}
