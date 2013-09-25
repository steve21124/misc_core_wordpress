<?php

// This filter is a direct copy of "bp_activity_make_nofollow_filter_callback". It is pasted here and re-named because
// if the user disables the activity stream, buddypress disables bp_activity_make_nofollow_filter_callback, causing an
// error if we use it.
// ===================================================================================================================

/**
 * bp_gallplus_make_nofollow_filter()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_make_nofollow_filter( $text ) {
	return preg_replace_callback( '|<a (.+?)>|i', 'bp_gallplus_make_nofollow_filter_callback', $text );
}
	function bp_gallplus_make_nofollow_filter_callback( $matches ) {
		$text = $matches[1];
		$text = str_replace( array( ' rel="nofollow"', " rel='nofollow'"), '', $text );
		return "<a $text rel=\"nofollow\">";
	}
	
add_filter( 'bp_gallplus_title_before_save', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_title_before_save', 'strip_tags', 1 );

add_filter( 'bp_gallplus_description_before_save', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_description_before_save', 'strip_tags', 1 );

add_filter( 'bp_gallplus_get_image_title', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_get_image_title', 'wptexturize' );
add_filter( 'bp_gallplus_get_image_title', 'convert_smilies' );	
add_filter( 'bp_gallplus_get_image_title', 'convert_chars' );

add_filter( 'bp_gallplus_get_image_title_truncate', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_get_image_title_truncate', 'wptexturize' );
add_filter( 'bp_gallplus_get_image_title_truncate', 'convert_smilies' );
add_filter( 'bp_gallplus_get_image_title_truncate', 'convert_chars' );

add_filter( 'bp_gallplus_get_image_desc', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_get_image_desc', 'force_balance_tags' );
add_filter( 'bp_gallplus_get_image_desc', 'wptexturize' );
add_filter( 'bp_gallplus_get_image_desc', 'convert_smilies' );
add_filter( 'bp_gallplus_get_image_desc', 'convert_chars' );
add_filter( 'bp_gallplus_get_image_desc', 'make_clickable' );
add_filter( 'bp_gallplus_get_image_desc', 'bp_gallplus_make_nofollow_filter' );
add_filter( 'bp_gallplus_get_image_desc', 'wpautop' );

add_filter( 'bp_gallplus_get_image_desc_truncate', 'wp_filter_kses', 1 );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'force_balance_tags' );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'wptexturize' );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'convert_smilies' );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'convert_chars' );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'make_clickable' );
add_filter( 'bp_gallplus_get_image_desc_truncate', 'bp_gallplus_make_nofollow_filter' );

?>