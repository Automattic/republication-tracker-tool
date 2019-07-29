function copyToClipboard( element ) {
	var $temp = jQuery( '<input>' );
	jQuery( 'body' ).append( $temp );
	$temp.val( jQuery( element ).text() ).select();
	document.execCommand( 'copy' );
	$temp.remove();
}

function modal_actions(){
	// Remove captions from shareable text
	var $ = jQuery;
	var $shareable = $('#republication-tracker-tool-shareable-content');
	var html = $shareable.text();

	var parser = new DOMParser();
	var doc = parser.parseFromString(html, "text/html");
	$(doc).find('.wp-caption').remove();
	var captionless = $(doc).find('body').html();
	$shareable.html(captionless);

	// Responsive modal
	var $modal = $('#republication-tracker-tool-modal');
	var $modal_content = $('#republication-tracker-tool-modal-content');
	var $btn = $('#cc-btn');
	var $close = $('.republication-tracker-tool-close');

	$btn.click(function(){
		//$modal.html( html );
		$modal.show();
		$modal_content.show();
		$('body').addClass('modal-open-disallow-scrolling');
		$('#republication-tracker-tool-modal-content').unbind().click(function(e) {
			e.stopPropagation();
		});
	});

	$modal.click(function(){
		$('body').removeClass('modal-open-disallow-scrolling');
		$modal.hide();
	});

	$close.click(function(){
		$('body').removeClass('modal-open-disallow-scrolling');
		$modal.hide();
	});
}

jQuery(document).ready(function(){
	var $ = jQuery,
		postId = $( '#republication-tracker-tool-modal' ).attr( 'data-postid' ),
		pluginsdir = $( '#republication-tracker-tool-modal' ).attr( 'data-pluginsdir' );

		$('#republication-tracker-tool-modal').append($('#republication-tracker-tool-modal-content'));
		$('body').append($('#republication-tracker-tool-modal'));

		modal_actions();

});
