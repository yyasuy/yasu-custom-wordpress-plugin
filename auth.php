<?php
require_once( 'lib.php' );
$json = json_decode( file_get_contents( '/var/www/auth/wordpress/.secure.json' ), true );
$_passcode = _get_request_param( 'passcode' );
if( $json[ 'HASH' ] == crypt( $_passcode, $json[ 'SALT' ] ) ){
	setcookie( "yasu_auth", "OK", time() + 120, "/" );
	header( 'location: /wordpress/' );
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>

<body>
<form method="post" action="auth.php">
<input type="text" id="passcode" name="passcode">
<button type="submit">Go</button>
</form>

</body>
</html>
