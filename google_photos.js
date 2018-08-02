const CLIENT_ID = '650098967029-mceign27csrnlkoqkmsbepjjib2vdvvl.apps.googleusercontent.com';
const SCOPES = [
	'https://www.googleapis.com/auth/photoslibrary.readonly'
];
var token_type;
var access_token = "not_set";

function y_gapi_onload(){
	//console.log( 'on_load' );
	gapi.auth.authorize( {
			client_id: CLIENT_ID,
			scope: SCOPES,
			immediate: true
		},
		function( _auth_result ){
			//console.log( _auth_result );
			if( typeof _auth_result.error == 'undefined' ){
				token_type = _auth_result.token_type;
				access_token = _auth_result.access_token;
				//console.log( access_token );
				y_get_photo_thumbs();
			}else{
				gapi.auth.authorize( {
					client_id: CLIENT_ID,
					scope: SCOPES,
					immediate: false
				},
				function( _auth_result ){
					token_type = _auth_result.token_type;
					access_token = _auth_result.access_token;
					//console.log( access_token );
					y_get_photo_thumbs();
				} );
			}
		}
	)
}

function y_get_photo_thumbs(){
	jQuery( function( $ ){
		if( $( '.thumbs' ).length == 0 ) return; // Do nothing

		var year;
		var month;
		var day;
		var date;
		$( '.thumbs' ).each( function( _i, _obj ){
			id = $( this ).attr( 'id' );
			year = $( this ).attr( 'data-year' );
			month = $( this ).attr( 'data-month' );
			day = $( this ).attr( 'data-day' );
			date = { "year":year, "month":month, "day":day };

			var data = {
					"filters":{
						"dateFilter":{
							"dates":date
						},
					},
					"pageSize":100
			};
			console.log( JSON.stringify( data ) );

			var authorization = token_type + ' ' + access_token;
			$.ajax( {
				type: 'POST',
				url: 'https://photoslibrary.googleapis.com/v1/mediaItems:search',
				contentType: 'application/json',
				headers: {
					'Authorization': authorization
				},
				//data: '{ "filters": { "dateFilter": { "dates": [ { "year":2018, "month":7, "day":22 } ] } }, "pageSize": 60 }',
				data: JSON.stringify( data ),
				dataType: 'json'
			} ).done( function( _data ){
				console.log( _data );
				if( _data.mediaItems == null ) return;
				var img_html = '';
				for( i = _data.mediaItems.length - 1; i >= 0; i-- ){
					created = _data.mediaItems[ i ].mediaMetadata.creationTime; // e.g. i"2016-12-31T21:44:33Z"
					var d = new Date( created );
					id = d.getFullYear().toString() + ( '0' + d.getMonth() + 1 ).slice( -2 ).toString() + ( '0' + d.getDate() ).slice( -2 ).toString(); // 20170101
					//console.log( id );
					var thumb_url = _data.mediaItems[ i ].baseUrl + '=h100-w100-c';
					img_html = '<img src="' + thumb_url + '"/>';
					$( '#' + id ).append( img_html );
				}
			} );
		} );
	} );
}

function y_list_albums(){
	jQuery( function( $ ){
		var authorization = token_type + ' ' + access_token;
		$.ajax( {
			type: 'GET',
			url: 'https://photoslibrary.googleapis.com/v1/albums',
			headers: {
				'Authorization': authorization
			}
		} ).done( function( _data ){
			//console.log( _data );
		} );
	} );
}
