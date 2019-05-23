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
	var $shareable = $('#republication-tracker-tool-shareable-content');
	var html = $shareable.text();

	var parser = new DOMParser();
	var doc = parser.parseFromString(html, "text/html");
	$(doc).find('.wp-caption').remove();
	var captionless = $(doc).find('body').html()
	$shareable.text(captionless);

	// Responsive modal
	var $modal = $('#republication-tracker-tool-modal');
	var $btn = $('#cc-btn');
	var $close = $('.republication-tracker-tool-close');

	$btn.click(function(){
		//$modal.html( html );
		$modal.show();
		$('#republication-tracker-tool-modal-content').unbind().click(function(e) {
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
		postId = $( '#republication-tracker-tool-modal' ).attr( 'data-postid' ),
		pluginsdir = $( '#republication-tracker-tool-modal' ).attr( 'data-pluginsdir' );

	$.ajax({
		url: pluginsdir + '/republication-tracker-tool/includes/shareable-content.php?post=' + postId,
		cache: false,
		success: function( data ){
			$('body').append($('#republication-tracker-tool-modal'));
			$('#republication-tracker-tool-modal').append(data);
			ajaxCallback(data);
		}
	});

});
