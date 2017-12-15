function copyToClipboard( element ) {
	var $temp = jQuery( '<input>' );
	jQuery( 'body' ).append( $temp );
	$temp.val( jQuery( element ).text() ).select();
	document.execCommand( 'copy' );
	$temp.remove();
}

function ajaxCallback(data){
	// Remove captions from shareable text
	var $ = jQuery;
	var $shareable = $('#creative-commons-shareable-content');
	var html = $shareable.text();

	var parser = new DOMParser();
	var doc = parser.parseFromString(html, "text/html");
	$(doc).find('.wp-caption').remove();
	var captionless = $(doc).find('body').html()
	$shareable.text(captionless);

	// Responsive modal
	var $modal = $('#creative-commons-share-modal');
	var $btn = $('#cc-btn');
	var $close = $('.creative-commons-close');

	$btn.click(function(){
		//$modal.html( html );
		$modal.show();
		$('#creative-commons-share-modal-content').unbind().click(function(e) {
			e.stopPropagation();
		});
	});

	$modal.click(function(){
		$modal.hide();
	});

	$close.click(function(){
		$modal.hide();
	});
}

jQuery(document).ready(function(){
	var $ = jQuery,
		postId = $( '#creative-commons-share-modal' ).attr( 'data-postid' ),
		pluginsdir = $( '#creative-commons-share-modal' ).attr( 'data-pluginsdir' );

	$.ajax({
		url: pluginsdir + '/creative-commons-sharing/includes/shareable-content.php?post=' + postId,
		cache: false,
		success: function( data ){
			$('#creative-commons-share-modal').append(data);
			ajaxCallback(data);
		}
	});

});
