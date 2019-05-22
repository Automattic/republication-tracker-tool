( function() {
  var img, loc, seperator, titleEl, titleText, url;

  if ( ! window.pixel_ping_tracked ) {
    loc = window.location;
	currentUrl = encodeURIComponent( window.location.protocol + "//" + window.location.host + window.location.pathname );
	postId = document.getElementById( 'republication-tracker-tool-source' ).getAttribute( 'data-postid' );
	pluginsdir = document.getElementById( 'republication-tracker-tool-source' ).getAttribute( 'data-pluginsdir' );
    img = document.createElement( 'img' );
    img.setAttribute( 'src', pluginsdir + '/republication-tracker-tool/includes/pixel.php?post=' + postId + '&url=' + currentUrl );
    img.setAttribute( 'width', '1' );
    img.setAttribute( 'height', '1' );
    document.body.appendChild( img );
    window.pixel_ping_tracked = true;
  }

}).call( this );
