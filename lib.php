<?php
function _debug_print( $var ){
	if( gettype( $var ) != 'array' ){
		$str = $var;
	}else{
		$str = var_export( $var, true );
	}
	error_log( $str . "\n", 3, '/tmp/test.log' );
}
function _get_server_param( $_name ){
	return isset( $_SERVER[ $_name ] ) ?  $_SERVER[ $_name] : '';
}
function _get_request_param( $_name ){
	$value  = isset( $_REQUEST[ $_name ] ) ?  $_REQUEST[ $_name] : '';
	if( get_magic_quotes_gpc() ){
        	$value  = stripslashes( $value );
	}
	return $value;
}
?>
