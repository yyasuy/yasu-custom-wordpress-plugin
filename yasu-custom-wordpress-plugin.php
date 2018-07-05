<?php
/*
Plugin Name: Yasu's personal plugin
Plugin URI: https://github.com/yyasuy/yasu-custom-wordpress-plugin
Description: This plugin is developed to ease my daily edits.
Version: 1.0
Author: Yasu Yamanaka
Author URI: https://www.instagram.com/whiskingram/
License: GPL2
*/

require_once( 'lib.php' );

add_action( 'admin_menu', 'yasu_admin_menu' );
add_action( 'media_buttons', 'yasu_media_buttons' );


function yasu_admin_menu(){
	add_menu_page( 'Personal convenient tools', 'Yasu Tools', 'manage_options', __FILE__, 'yasu_tools_page', 'dashicons-welcome-learn-more', 90 );
}

function yasu_tools_page(){
	$_text = _get_request_param( 'text' );
	$_text = yasu_zenkaku_number_to_hankaku_number( $_text );
	$_text = yasu_delete_kindle_highlight_position( $_text );
	printf( '<div>' );
	printf( '<h1>Enter source text you want to convert.</h1>' );
	printf( '</div>' );
	printf( '<form method="post">' );
	printf( '<textarea name="text" rows="30" cols="100">%s</textarea>', $_text );
	submit_button( 'Submit' );
	printf( '</form>' );
}

function yasu_zenkaku_number_to_hankaku_number( $_text ){
	$conv_array = array(
		'〇' => '0',
		'一' => '1',
		'二' => '2',
		'三' => '3',
		'四' => '4',
		'五' => '5',
		'六' => '6',
		'七' => '7',
		'八' => '8',
		'九' => '9',
	);
	foreach( $conv_array as $old => $new ){
		$_text = str_replace( $old, $new, $_text );
	}
	return $_text;
}

function yasu_delete_kindle_highlight_position( $_text ){
	$_text = preg_replace( '/^.+ハイライト.+位置.+$/m', '', $_text );
	return $_text;
}

function yasu_media_buttons(){
	$script_file_url = plugins_url( 'save.js', __FILE__ );
	printf( '<script src=%s></script>', $script_file_url );
	printf( '<button id="yasu_save">Save</button>' );
}
?>
