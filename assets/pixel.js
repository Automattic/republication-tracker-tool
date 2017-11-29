( function() {
  var img, loc, seperator, titleEl, titleText, url;

  if ( ! window.pixel_ping_tracked ) {
    loc = window.location;
	currentUrl = encodeURIComponent( window.location.protocol + "//" + window.location.host + window.location.pathname );
	postId = document.getElementById( 'creative-commons-sharing-source' ).getAttribute( 'data-postid' );
	pluginsdir = document.getElementById( 'creative-commons-sharing-source' ).getAttribute( 'data-pluginsdir' );
    img = document.createElement( 'img' );
    img.setAttribute( 'src', pluginsdir + '/creative-commons-sharing/includes/pixel.php?post=' + postId + '&url=' + currentUrl );
    img.setAttribute( 'width', '1' );
    img.setAttribute( 'height', '1' );
    document.body.appendChild( img );
    window.pixel_ping_tracked = true;
  }

}).call( this );
