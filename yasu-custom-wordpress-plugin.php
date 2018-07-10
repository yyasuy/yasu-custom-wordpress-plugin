<?php
/*
Plugin Name: Yasu's personal wordpress plugin to make my life easy
Plugin URI: https://github.com/yyasuy/yasu-custom-wordpress-plugin
Description: This plugin is developed to ease my daily edits.
Version: 1.0
Author: Yasu Yamanaka
Author URI: https://www.instagram.com/whiskingram/
License: GPL2
*/

require_once( 'lib.php' );

// Add a page named 'Yasu Tools' in the admin menu.
add_action( 'admin_menu', 'yasu_admin_menu' );
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

// Add a button named 'Save' next to the Add Media button. Save button works the same as the Publish button.
add_action( 'media_buttons', 'yasu_media_buttons' );
function yasu_media_buttons(){
	$script_file_url = plugins_url( 'save.js', __FILE__ );
	printf( '<script src=%s></script>', $script_file_url );
	printf( '<button id="yasu_save" class="button-primary">Save</button>' );
}

// Add a Google Photos link that shows the photos taken on the published date of the post.
add_filter( 'the_content', 'yasu_the_content' );
function yasu_the_content( $_content ){
	$post_id = get_the_ID();
	$the_year = get_the_date( 'Y', $post_id );
	$the_month = get_the_date( 'n', $post_id );
	$the_day = get_the_date( 'j', $post_id );
	$args = array(
		'category_name' => 'Blog',
		'post_type' => 'post',
		'post_status' => 'publish',
		'monthnum' => $the_month,
		'day' => $the_day,
	);
	$posted_year_id_array = array();
	$query = new WP_Query( $args );
	if( $query->have_posts() ){
		while( $query->have_posts() ){
			$query->the_post();
			$post_id = get_the_ID();
			$posted_year = get_the_date( 'Y', $post_id );
			if( $posted_year == $the_year ) continue;
			array_push( $posted_year_id_array, array( 'ID' => $post_id, 'Year' => $posted_year ) );
		}
	}
	wp_reset_postdata();
	$posts_on_the_same_day = '';
	foreach( $posted_year_id_array as $year_id_pair ){
		$posts_on_the_same_day .= sprintf( '<a href="%s">%s</a> ', get_permalink( $year_id_pair[ 'ID' ] ), $year_id_pair[ 'Year' ] );
	}
	$posts_on_the_same_day = '<p>' . $posts_on_the_same_day . '</p>';

	$published_date = get_the_date( 'Y年n月j日' );

	$google_photo_link = sprintf( '<p><a href="https://photos.google.com/search/%s" target="_blank">この日の写真</a></p>', $published_date );
	$_content = $google_photo_link . $_content . $posts_on_the_same_day;
	return $_content;
}

// View the post right after updating it.
add_filter( 'redirect_post_location', 'yasu_redirect_post_location' );
function yasu_redirect_post_location( $_location ){
	$post_id = get_the_ID();
	$post_url = get_permalink( $_post_id );
	return $post_url;
}
?>
