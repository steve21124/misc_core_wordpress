<?php

/**
 * NOTE: You should always use the wp_enqueue_script() and wp_enqueue_style() functions to include
 * Javascript and CSS files.
 */

/**
 * bp_gallplus_add_js()
 *
 * This function will enqueue the components Javascript file, so that you can make
 * use of any Javascript you bundle with your component within your interface screens.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_add_js() {
    
	global $bp;

	if ( $bp->current_component == $bp->album->slug )
	{
		wp_enqueue_script( 'bp-gallery-plus-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/general.js' );
		wp_localize_script( 'bp-gallery-plus-js', 'BPGPLSAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),   
																						 'BPGPLSDeleteAlbum' => wp_create_nonce( 'BPGPLSDeleteAlbum' ),		
																						 'BPGPLSFeatureImage' => wp_create_nonce( 'BPGPLSFeatureImage' ),		
																						 'BPGPLSAlbumPrivacy' => wp_create_nonce( 'BPGPLSAlbumPrivacy' ),		
																						 'BPGPLSDeleteImage' => wp_create_nonce( 'BPGPLSDeleteImage' )) );		
		wp_enqueue_script( 'jquery-mousewheel-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js');
		wp_enqueue_script( 'jquery-fancybox-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/jquery.fancybox.js');

	}
}
 add_action( 'template_redirect', 'bp_gallplus_add_js', 1 );

/**
 * bp_gallplus_add_css()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_add_css() {
    
	global $bp;

		wp_enqueue_style( 'bp-gallery-plus-css', WP_PLUGIN_URL .'/bp-gallery/includes/css/general.css' );
		$bp_gallplus_viewer = get_option('bp_gallplus_viewer');
		if($bp_gallplus_viewer == 0)
		{
			wp_enqueue_script( 'jquery-mousewheel-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/lib/jquery.mousewheel-3.0.6.pack.js');
			wp_enqueue_script( 'jquery-fancybox-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/jquery.fancybox.js');
			wp_enqueue_style( 'jquery-fancybox-css', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/jquery.fancybox.css');
			wp_enqueue_style( 'jquery-fancybox-buttons-css', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/helpers/jquery.fancybox-buttons.css');
			wp_enqueue_script( 'jquery-fancybox-buttons-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/helpers/jquery.fancybox-buttons.js');

			/*-- Add Thumbnail helper (this is optional) --> */
			wp_enqueue_style( 'jquery-fancybox-thumbs-css', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/helpers/jquery.fancybox-thumbs.css');		
			wp_enqueue_script( 'jquery-fancybox-thumbs-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/helpers/jquery.fancybox-thumbs.js');

			/*-- Add Media helper (this is optional) --*/
			wp_enqueue_script( 'jquery-fancybox-media-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/fancybox/source/helpers/jquery.fancybox-media.js');
			/*-- BP Gallery plus fancybox settings and customizations --*/
			wp_enqueue_script( 'bpgplus-fancybox-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/bpgplus-fancybox.js');
		}
//		wp_enqueue_script( 'bp-galleries-plus-js', WP_PLUGIN_URL .'/bp-gallery-plus/includes/js/general.js' );
		wp_print_styles();	
		wp_enqueue_script( 'bp-gallery-plus-js', WP_PLUGIN_URL .'/bp-gallery/includes/js/general.js' );
		wp_localize_script( 'bp-gallery-plus-js', 'BPGPLSAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),   
																						 'BPGPLSDeleteAlbum' => wp_create_nonce( 'BPGPLSDeleteAlbum' ),		
																						 'BPGPLSFeatureImage' => wp_create_nonce( 'BPGPLSFeatureImage' ),		
																						 'BPGPLSAlbumPrivacy' => wp_create_nonce( 'BPGPLSAlbumPrivacy' ),		
																						 'BPGPLSDeleteImage' => wp_create_nonce( 'BPGPLSDeleteImage' )) );		
}
function admin_css()
{
		wp_enqueue_style( 'bp-gallery-plus-css', WP_PLUGIN_URL .'/bp-gallery/includes/css/general.css' );
}
add_action( 'admin_head', 'admin_css' );
add_action( 'wp_head', 'bp_gallplus_add_css' );
?>