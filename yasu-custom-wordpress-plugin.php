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
	$_text = yasu_delete_unwanted_spaces( $_text );
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
	$undo_conv_array = array(
		'統1' => '統一',
		'1般' => '一般',
		'1大' => '一大',
		'単1' => '単一',
		'同1' => '同一',
		'随1' => '随一',
		'1生' => '一生',
		'1方' => '一方',
		'1定' => '一定',
		'唯1' => '唯一',
		'1見' => '一見',
		'1部' => '一部',
		'1神' => '一神',
		'1握' => '一握',
		'1連' => '一連',
		'1慣' => '一貫',
		'1変' => '一変',
		'1員' => '一員',
		'統1' => '統一',
		'3田' => '三田',
		'3田会' => '三田会',
		'泰3' => '泰三',
		'1口' => '一口',
		'9州' => '九州',
		'1角' => '一角',
	);
	foreach( $undo_conv_array as $old => $new ){
		$_text = str_replace( $old, $new, $_text );
	}
	return $_text;
}

function yasu_delete_kindle_highlight_position( $_text ){
	$_text = preg_replace( '/^.+ハイライト.+位置.+$/m', '', $_text );
	return $_text;
}

function yasu_delete_unwanted_spaces( $_text ){
	$_text = preg_replace( '/([^\w]) +([^\w])/m', '$1$2', $_text ); // Remove spaces between 2byte characters.
	$_text = preg_replace( '/^ +/', '', $_text ); // Remove leading spaces of a line.
	return $_text;
}

// Add a button named 'Save' next to the Add Media button. Save button works the same as the Publish button.
add_action( 'media_buttons', 'yasu_media_buttons' );
function yasu_media_buttons(){
	$script_file_url = plugins_url( 'save.js', __FILE__ );
	printf( '<script src=%s></script>', $script_file_url );
	printf( '<button id="yasu_save" class="button-primary">Save</button>' );
}

add_action( 'wp_head', 'yasu_wp_head' );
function yasu_wp_head(){
	// < Google Photos 
	$script_file_url = plugins_url( 'google_photos.js', __FILE__ );
	printf( '<script src=%s></script>', $script_file_url );
	printf( '<script src="https://apis.google.com/js/client.js?onload=y_gapi_onload"></script>' );
	// Google Photos >

	// < Auth
	$auth_file_url = plugins_url( 'auth.php', __FILE__ );
?>
	<script>
	var auth = false;
	var cookies = document.cookie.split( ';' );
	cookies.forEach( function( _cookie ){
		var name_value = _cookie.split( '=' );
		var name = name_value[ 0 ].trim();
		var value = name_value[ 1 ].trim();
		if( name == 'yasu_auth' && value == 'OK' ) auth = true;
	} );
	if( auth == false ){
		var auth_url = "<?php printf( $auth_file_url ); ?>"
		location.href = auth_url;
	}
	</script>
<?php
	// Auth >
}

// Add a Google Photos link that shows the photos taken on the published date of the post.
add_filter( 'the_content', 'yasu_the_content' );
function yasu_the_content( $_content ){
	$post_id = get_the_ID();
	$cats = get_the_category( $post_id );
	$is_blog = false;
	foreach( $cats as $cat ){
		$cat_name = $cat->name;
		if( $cat_name == 'Blog' ) $is_blog = true;	
	}
	if( $is_blog == false ) return $_content;

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
			array_push( $posted_year_id_array, array( 'ID' => $post_id, 'Year' => $posted_year ) );
		}
	}
	wp_reset_postdata();
	$posts_on_the_same_day = '';
	foreach( $posted_year_id_array as $year_id_pair ){
		if( $year_id_pair[ 'Year' ] != $the_year ){
			$posts_on_the_same_day .= sprintf( '<a href="%s">%s</a> ', get_permalink( $year_id_pair[ 'ID' ] ), $year_id_pair[ 'Year' ] );
		}else{
			$posts_on_the_same_day .= sprintf( '%s ', $year_id_pair[ 'Year' ] );
		}
	}
	$posts_on_the_same_day = '<p>' . $posts_on_the_same_day . '</p>';

	$search_keyword_for_published_date = get_the_date( 'l F d Y' ); // e.g. Sunday July 22 2018

	$google_photo_link = sprintf( '<p><a href="https://photos.google.com/search/%s" target="_blank">この日の写真</a></p>', $search_keyword_for_published_date );

	$google_photo_thumbs = sprintf( '<div class="thumbs" id="%s" data-year="%s" data-month="%s" data-day="%s"></div>',
	                                 get_the_date( 'Ymd' ), get_the_date( 'Y' ), get_the_date( 'm' ), get_the_date( 'd' ) );

	$_content = $google_photo_link . $google_photo_thumbs . $_content . $posts_on_the_same_day;

	return $_content;
}

// View the post right after updating it.
add_filter( 'redirect_post_location', 'yasu_redirect_post_location' );
function yasu_redirect_post_location( $_location ){
	$post_id = get_the_ID();
	$post_url = get_permalink( $_post_id );
	return $post_url;
}

// Add my custom shortcode. The shortcode [child_page] will show the list of child pages under the current page.
add_shortcode( 'child_pages', 'yasu_child_pages' );
function yasu_child_pages(){
	$page_id = get_the_ID();
	$args = array(
		'post_parent' => $page_id,
	);
	$child_pages = get_children( $args, 'OBJECT' );
	$content = sprintf( '<ul>' );
	foreach( $child_pages as $child_page ){
		$content .= sprintf( '<li><a href="%s">%s</a></li>', get_permalink( $child_page->ID ), $child_page->post_title );
	}
	$content .= sprintf( '</ul>' );
	return $content;
}
?>
