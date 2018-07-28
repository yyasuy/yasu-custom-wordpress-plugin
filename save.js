jQuery( function( $ ){
	var real_publish_button = $( '#publish' );
	$( '#yasu_save' ).click( function( _e ){
		_e.preventDefault();
		real_publish_button.trigger( 'click' );
	} );
} );
