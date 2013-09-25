<?php

/**
 * bp-galleries-plus CORE
 * Handles the overall operations of the plugin
 *
 * @version 0.1.8.11
 * @since 0.1.8
 * @package bp-galleries-plus
 * @subpackage Main
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/
 *
 * ========================================================================================================
 */

define ( 'BP_GALLPLUS_IS_INSTALLED', 1 );
define ( 'BP_GALLPLUS_DB_VERSION', '0.2' );
define ( 'BP_GALLPLUS_VERSION', '0.1.8.11' );
define ( 'BP_GALLPLUS_PLUGIN_URL', WP_PLUGIN_URL.'/bp-gallery/');
load_textdomain( 'bp-gallery', dirname( __FILE__ ) . '/languages/bp-album-' . get_locale() . '.mo' );

require ( dirname( __FILE__ ) . '/bpa.classes.php' );
require ( dirname( __FILE__ ) . '/bpa.screens.php' );
require ( dirname( __FILE__ ) . '/bpa.cssjs.php' );
require ( dirname( __FILE__ ) . '/bpa.template.tags.php' );
require ( dirname( __FILE__ ) . '/bpa.filters.php' );
require ( dirname( __FILE__ ) . '/bpa.group.php' );

require_once( ABSPATH . '/wp-admin/includes/image.php' );
require_once( ABSPATH . '/wp-admin/includes/file.php' );


/**
 * bp_gallplus_setup_globals()
 *
 * Sets up bp-galleries-plus's global variables.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_setup_globals() {
    
	global $bp, $wpdb;
	
	if ( !defined( 'BP_GALLPLUS_UPLOAD_PATH' ) )
		define ( 'BP_GALLPLUS_UPLOAD_PATH', bp_gallplus_upload_path() );
	
	$bp->album = new stdClass();

	$bp->album->id = 'album';
	$bp->album->table_name = $wpdb->base_prefix . 'bp_gallplus_album';
	$bp->album->albums_table_name = $wpdb->base_prefix . 'bp_gallplus_albums';
	// JLL_MOD - add notifications
	//$bp->album->format_notification_function = 'bp_gallplus_format_notifications';
	$bp->album->format_notification_function = 'photos_format_notifications';
	$bp->album->slug = get_site_option( 'bp_gallplus_slug' );
	$bp->album->images_slug = 'images';
	$bp->album->single_slug = 'image';
	$bp->album->upload_slug = 'upload';
	$bp->album->delete_slug = 'delete';
	$bp->album->edit_slug = 'edit';
	$bp->album->album_slug = 'album';
	$bp->album->albums_slug = 'albums';
	$bp->album->albums_add_slug = 'add';
	$bp->album->single_edit_slug = 'single';
	$bp->album->group_slug = 'groups';
	$bp->album->group_gallery_slug = 'gallery';
	
	

        $bp->album->bp_gallplus_max_images = get_site_option( 'bp_gallplus_max_images' );
        $bp->album->bp_gallplus_max_upload_size = get_site_option( 'bp_gallplus_max_upload_size' );	
        $bp->album->bp_gallplus_max_priv0_images = get_site_option( 'bp_gallplus_max_priv0_images' );
        $bp->album->bp_gallplus_max_priv2_images = get_site_option( 'bp_gallplus_max_priv2_images' );
        $bp->album->bp_gallplus_max_priv3_images = get_site_option( 'bp_gallplus_max_priv3_images' );
        $bp->album->bp_gallplus_max_priv5_images = get_site_option( 'bp_gallplus_max_priv3_images' );
        $bp->album->bp_gallplus_max_priv4_images = get_site_option( 'bp_gallplus_max_priv4_images' );
        $bp->album->bp_gallplus_max_priv6_images = get_site_option( 'bp_gallplus_max_priv6_images' );
        $bp->album->bp_gallplus_keep_original = get_site_option( 'bp_gallplus_keep_original' );
        $bp->album->bp_gallplus_require_description = get_site_option( 'bp_gallplus_require_description' );
        $bp->album->bp_gallplus_enable_comments = get_site_option( 'bp_gallplus_enable_comments' );
        $bp->album->bp_gallplus_disable_public_access = get_site_option( 'bp_gallplus_disable_public_access' );
        $bp->album->bp_gallplus_enable_wire = get_site_option( 'bp_gallplus_enable_wire' );
        $bp->album->bp_gallplus_middle_size = get_site_option( 'bp_gallplus_middle_size' );
        $bp->album->bp_gallplus_thumb_size = get_site_option( 'bp_gallplus_thumb_size' );
        $bp->album->bp_gallplus_per_page = get_site_option( 'bp_gallplus_per_page' );
	$bp->album->bp_gallplus_url_remap = get_site_option( 'bp_gallplus_url_remap' );
	$bp->album->bp_gallplus_base_url = get_site_option( 'bp_gallplus_base_url' );
	$bp->album->bp_gallplus_keep_files = get_site_option('bp_gallplus_keep_files');

	$bp->active_components[$bp->album->slug] = $bp->album->id;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->upload_slug != $bp->current_action  ){
		bp_gallplus_query_images();
	}	
}
add_action( 'wp', 'bp_gallplus_setup_globals', 2 );

add_action( 'bp_setup_globals', 'bp_gallplus_setup_globals', 2 );
add_action( 'admin_menu', 'bp_gallplus_setup_globals', 2 );

/**
 * Adds the BuddyPress Album admin menu to the wordpress "Site" admin menu
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_add_admin_menu() {

	if( is_multisite()  ){
		return;
	}
	
	else 
	    {
		if ( !is_super_admin() ){
		    return false;
	    }

	require ( dirname( __FILE__ ) . '/admin/bpa.admin.local.php' );

	add_menu_page(__( 'BP Gallery Plus', 'bp-galleries-plus' ), __( 'BP Gallery Plus', 'bp-galleries-plus' ), 'administrator', 'bp-galleries-plus-settings', 'bp_gallplus_admin' );
		
	}
}
add_action( 'admin_menu', 'bp_gallplus_add_admin_menu' );

/**
 * Adds the bp-galleries-plus admin menu to the wordpress "Network" admin menu.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_add_network_menu() {
    
	if ( !is_super_admin() ){
		    return false;
	    }

	require ( dirname( __FILE__ ) . '/admin/bpa.admin.network.php' );

	add_menu_page(__( 'BP Gallery Plus', 'bp-galleries-plus' ), __( 'BP Gallery Plus', 'bp-galleries-plus' ), 'administrator', 'bp-galleries-plus-settings', 'bp_gallplus_admin' );

}
add_action( 'network_admin_menu', 'bp_gallplus_add_network_menu' );

/**
 * bp_gallplus_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_setup_nav() {
    
	global $bp,$images_template;

	$numAlbums = bp_gallplus_nav_album_count();
	if($numAlbums > 0)
		$nav_item_name = apply_filters( 'bp_gallplus_nav_item_name', __( 'Galleries <span>'.$numAlbums.'</span>', 'bp-galleries-plus' ) );
	else
		$nav_item_name = apply_filters( 'bp_gallplus_nav_item_name', __( 'Galleries', 'bp-galleries-plus' ) );

	bp_core_new_nav_item( array(
		'name' => $nav_item_name,
		'slug' => $bp->album->slug,
		'position' => 80,
		'screen_function' => 'bp_gallplus_screen_images',
		'default_subnav_slug' => $bp->album->image_slug,
		'show_for_displayed_user' => true
//		'slug' => $bp->album->album_slug,
//		'screen_function' => 'bp_gallplus_show_albums',
//		'default_subnav_slug' => $bp->album->album_slug
	) );


	$album_link = ($bp->displayed_user->id ? $bp->displayed_user->domain : $bp->loggedin_user->domain) . $bp->album->slug . '/';
	$album_link_title = ($bp->displayed_user->id) ? bp_word_or_name( __( "Images", 'bp-galleries-plus' ), __( "%s's Images", 'bp-galleries-plus' ) ,false,false) : __( "Images", 'bp-galleries-plus' );
	bp_core_new_subnav_item( array(
		'name' => $album_link_title,
		'slug' => $bp->album->images_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_screen_images',
		'user_has_access' => is_user_logged_in(), // Only logged in user can access this
		'position' => 10
	) );

	if($bp->current_component == $bp->album->slug  && $bp->current_action == $bp->album->single_slug ){
		add_filter( 'bp_get_displayed_user_nav_' . $bp->album->single_slug, 'bp_gallplus_single_subnav_filter' ,10,2);
		bp_core_new_subnav_item( array(
			'name' => isset($images_template->images[0]->id) ? bp_gallplus_get_image_title_truncate(20) :  __( 'Image', 'bp-galleries-plus' ),
			'slug' => $bp->album->single_slug,
			'parent_slug' => $bp->album->slug,
			'parent_url' => $album_link,
//			'screen_function' => 'bp_gallplus_screen_single',
			'screen_function' => 'bp_gallplus_screen_album',
		'user_has_access' => is_user_logged_in(), // Only logged in user can access this
			'position' => 20
		) );
	}

	$album_link = ($bp->displayed_user->id ? $bp->displayed_user->domain : $bp->loggedin_user->domain) . $bp->album->slug . '/';
	$album_link_title = ($bp->displayed_user->id) ? bp_word_or_name( __( "Galleries", 'bp-galleries-plus' ), __( "%s's Galleries", 'bp-galleries-plus' ) ,false,false) : __( "Galleries", 'bp-galleries-plus' );
	bp_core_new_subnav_item( array(
		'name' => $album_link_title,
		'slug' => $bp->album->albums_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_show_albums',
		'position' => 30,
//		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
		'user_has_access' => is_user_logged_in() // Only logged in user can access this
	) );

	bp_core_new_subnav_item( array(
		'name' => __( 'Upload Images', 'bp-galleries-plus' ),
		'slug' => $bp->album->upload_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_screen_upload',
		'position' => 40,
		'user_has_access' => (bp_is_my_profile()|| is_super_admin()) // Only the logged in user can access this on his/her profile
	) );
	bp_core_new_subnav_item( array(
		'name' => __( 'View Image', 'bp-galleries-plus' ),
		'slug' => $bp->album->single_edit_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_screen_single',
		'position' => 50,
		'user_has_access' => (bp_is_my_profile()|| is_super_admin()) // Only the logged in user can access this on his/her profile
	) ); 
	
	bp_core_new_subnav_item( array(
		'name' => __( 'Add Gallery', 'bp-galleries-plus' ),
		'slug' => $bp->album->albums_add_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_screen_add_album',
		'position' => 60,
		'user_has_access' => (bp_is_my_profile()|| is_super_admin()) // Only the logged in user can access this on his/her profile
	) );
	bp_core_new_subnav_item( array(
		'name' => __( 'View Gallery', 'bp-galleries-plus' ),
		'slug' => $bp->album->album_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_show_albums',
		'position' => 70,
		'user_has_access' => is_user_logged_in() // Only the logged in user can access this on his/her profile
	) ); 

}
add_action( 'bp_setup_nav', 'bp_gallplus_setup_nav' );

/**
 * bp_gallplus_single_subnav_filter()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_single_subnav_filter($link,$user_nav_item){
	global $bp,$images_template;
	
	if(isset($images_template->images[0]->id))
		$link = str_replace  ( '/'. $bp->album->single_slug .'/' , '/'. $bp->album->single_slug .'/'.$images_template->images[0]->id .'/',$link );
		
	return $link;
}

/**
 * bp_gallplus_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_gallplus_screen_one() when you want to load
 * a template file.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_load_template_filter( $found_template, $templates ) {
    
	global $bp;

	if ( $bp->current_component != $bp->album->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		elseif ( file_exists( TEMPLATEPATH . '/' . $template ) )
			$filtered_templates[] = TEMPLATEPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/templates/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_gallplus_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_gallplus_load_template_filter', 10, 2 );

/**
 * bp_gallplus_load_subtemplate()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_load_subtemplate( $template_name ) {
    
	if ( file_exists(STYLESHEETPATH . '/' . $template_name . '.php')) {
		$located = STYLESHEETPATH . '/' . $template_name . '.php';
	} 
	else if ( file_exists(TEMPLATEPATH . '/' . $template_name . '.php') ) {
		$located = TEMPLATEPATH . '/' . $template_name . '.php';
	} 
	else{
		$located = dirname( __FILE__ ) . '/templates/' . $template_name . '.php';
	}
	include ($located);
}

/**
 * bp_gallplus_upload_path()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_upload_path(){
    
	if ( is_multisite() )
		$path = ABSPATH . get_blog_option( BP_ROOT_BLOG, 'upload_path' );
	else {
		$upload_path = get_option( 'upload_path' );
		$upload_path = trim($upload_path);
		if ( empty($upload_path) || '/wp-content/uploads' == $upload_path) {
			$path = WP_CONTENT_DIR . '/uploads';
		} 
		else {
			$path = $upload_path;
			if ( 0 !== strpos($path, ABSPATH) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$path = path_join( ABSPATH, $path );
			}
		}
	}
	
	$path .= '/album';

	return apply_filters( 'bp_gallplus_upload_path', $path );
}

/**
 * bp_gallplus_privacy_level_permitted()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_privacy_level_permitted(){
	global $bp;
	
	if(!is_user_logged_in())
		return 0;
	elseif(is_super_admin())
		return 10;
	elseif ( ($bp->displayed_user->id && $bp->displayed_user->id == $bp->loggedin_user->id) )
		return 6;
	elseif ( ($bp->displayed_user->id && function_exists('friends_check_friendship') && friends_check_friendship($bp->displayed_user->id,$bp->loggedin_user->id) ) )
		return 4;
	else
		return 3;
}

/**
 * bp_gallplus_limits_info()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_limits_info(){
    
	global $bp,$albums_template;
	
	$owner_id = isset($albums_template) ? $albums_template->album->owner_id : $bp->loggedin_user->id;
	
	$results = bp_gallplus_get_image_count(array('owner_id'=> $owner_id, 'privacy'=>'all', 'priv_override'=>true,'groupby'=>'privacy'));
	
	$return = array();
	$tot_count = 0;
	$tot_remaining = false;
	
	foreach(array(0,2,3,4,5,6,10) as $i){
		$return[$i]['count'] = 0;
		foreach ($results as $r){
			if($r->privacy == $i){
				$return[$i]['count'] = $r->count;
				break;
			}
		}
	
		if( isset($images_template) && $i==$images_template->image->privacy )
			$return[$i]['current'] = true;
		else
			$return[$i]['current'] = false;
		
		if ($i==10){
			$return[$i]['enabled'] = is_super_admin();
			$return[$i]['remaining'] = $return[$i]['enabled'];
		} 
		else {
                        switch ($i) {
                            case "0": $pic_limit = $bp->album->bp_gallplus_max_priv0_images; break;
                            case "1": $pic_limit = $bp->album->bp_gallplus_max_priv1_images; break;
                            case "2": $pic_limit = $bp->album->bp_gallplus_max_priv2_images; break;
                            case "3": $pic_limit = $bp->album->bp_gallplus_max_priv3_images; break;
                            case "4": $pic_limit = $bp->album->bp_gallplus_max_priv4_images; break;
                            case "5": $pic_limit = $bp->album->bp_gallplus_max_priv5_images; break;
                            case "6": $pic_limit = $bp->album->bp_gallplus_max_priv6_images; break;
                            case "7": $pic_limit = $bp->album->bp_gallplus_max_priv7_images; break;
                            case "8": $pic_limit = $bp->album->bp_gallplus_max_priv8_images; break;
                            case "9": $pic_limit = $bp->album->bp_gallplus_max_priv9_images; break;
                            default: $pic_limit = null;
                        }
			
			$return[$i]['enabled'] = $pic_limit !== 0 ? true : false;
				
			$return[$i]['remaining'] = $pic_limit === false ? true : ($pic_limit > $return[$i]['count'] ? $pic_limit - $return[$i]['count'] : 0 );
		}
		
		$tot_count += $return[$i]['count'];
		$tot_remaining = $tot_remaining || $return[$i]['remaining'];
	}
	$return['all']['count'] = $tot_count;
	$return['all']['remaining'] = $bp->album->bp_gallplus_max_images === false ? true : ($bp->album->bp_gallplus_max_images > $tot_count ? $bp->album->bp_gallplus_max_images - $tot_count : 0 );
	$return['all']['remaining'] = $tot_remaining ? $return['all']['remaining'] : 0;
	$return['all']['enabled'] = true;
	
	return $return;
}

/**
 * bp_gallplus_get_images()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_images($args = ''){
	
	return bp_gallplus_Image::query_images($args);
}

/**
 * bp_gallplus_get_albums()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_albums($args = ''){
	return bp_gallplus_Album::query_images($args);
}


/**
 * bp_gallplus_get_image_count()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_image_count($args = ''){
	return bp_gallplus_Image::query_images($args,true);
}

/**
 * bp_gallplus_get_album_count()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_album_count($args = ''){
	return BP_Gallplus_Album::query_images($args,true);
}

/**
 * bp_gallplus_get_next_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_next_image($args = ''){
	$result = bp_gallplus_Image::query_images($args,false,'next');
	return ($result)?$result[0]:false;
}

/**
 * bp_gallplus_get_next_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_next_album($args = ''){
	$result = bp_gallplus_Album::query_images($args,false,'next');
	return ($result)?$result[0]:false;
}

/**
 * bp_gallplus_get_prev_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_prev_image($args = ''){
	$result = bp_gallplus_Image::query_images($args,false,'prev');
	return ($result)?$result[0]:false;
}
/**
 * bp_gallplus_get_prev_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_get_prev_album($args = ''){
	$result = bp_gallplus_Album::query_images($args,false,'prev');
	return ($result)?$result[0]:false;
}

/**
 * bp_gallplus_add_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_add_image($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path,$album_id,$group_id){
	
    global $bp;
	
	$pic = new BP_Gallplus_Image();
	
	$pic->owner_type = $owner_type;

	$title = esc_attr( strip_tags($title) );
	$description = esc_attr( strip_tags($description) );

	$title = apply_filters( 'bp_gallplus_title_before_save', $title );
	$description = apply_filters( 'bp_gallplus_description_before_save', $description);
		
	$pic->owner_id = $owner_id;
	$pic->title = $title;
	$pic->description = $description;
	$pic->privacy = $priv_lvl;
	$pic->date_uploaded = $date_uploaded;
	$pic->pic_org_url = $pic_org_url;
	$pic->pic_org_path = $pic_org_path;
	$pic->pic_mid_url = $pic_mid_url;
	$pic->pic_mid_path = $pic_mid_path;
	$pic->pic_thumb_url = $pic_thumb_url;
	$pic->pic_thumb_path = $pic_thumb_path;
	$pic->album_id = $album_id;
	$pic->group_id = $group_id;	
	$pic->media_type = 0;
    return $pic->save() ? $pic->id : false;
}
/**
 * bp_gallplus_add_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_add_album($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$group_id,$spare2,$spare3,$spare4,$feature_image="",$like_count=0){
	
    global $bp;
	

		$loc_album_id = BP_Gallplus_Album::find_album($owner_id,$title);
	if($loc_album_id)
		$loc_album = new BP_Gallplus_Album($loc_album_id);
	else
		$loc_album = new BP_Gallplus_Album();
	
	
	$loc_album->owner_type = $owner_type;

	$title  = esc_attr( strip_tags($title) );
	$description = esc_attr( strip_tags($description) );

	$title = apply_filters( 'bp_gallplus_title_before_save', $title );
	$description = apply_filters( 'bp_gallplus_description_before_save', $description);
		
	$loc_album->owner_id = $owner_id;
	$loc_album->title = $title;
	$loc_album->description = $description;
	$loc_album->privacy = $priv_lvl;
	$loc_album->date_created = $date_uploaded;
	$loc_album->pic_org_url = $pic_org_url;
	$loc_album->feature_image_path = $pic_org_path;
	$loc_album->feature_image  = $feature_image;
	$loc_album->like_count	= $like_count;
	$loc_album->group_id = $group_id;
	$loc_album->spare2 = $spare2;
	$loc_album->spare3 = $spare3;
	$loc_album->spare4 = $spare4;
	
   return $loc_album->save() ? $loc_album->id : false;
  
}
/**
 * bp_gallplus_edit_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_edit_album($album_id,$title,$description,$priv_lvl,$date_updated,$enable_comments,$group_id){
	
    global $bp;
	
	$loc_album = new BP_Gallplus_Album($album_id);
	if(!empty($loc_album->id)){
	
		$title  = esc_attr( strip_tags($title) );
		$description = esc_attr( strip_tags($description) );

		$title = apply_filters( 'bp_gallplus_title_before_save', $title );
		$description = apply_filters( 'bp_gallplus_description_before_save', $description);
		$loc_album->date_updated = $date_updated;
		$loc_album->title = $title;
		$loc_album->description = $description;
		$priv_change = false;
		if($loc_album->privacy != $priv_lvl)
		{
			$priv_change = true;
		}
		$loc_album->privacy = $priv_lvl;
		$loc_album->group_id = $group_id;
	
	 	$loc_album->save();
    if(bp_is_active('activity')){
	    	if ($enable_comments) 
	    		bp_gallplus_record_album_activity($loc_album,true);
	    	else{
	    		bp_gallplus_delete_activity($loc_album->id);
	    	}
	  }
	  if($priv_change)
	  {
			$imageIDs = $loc_album->query_album_image_ids();
			if($imageIDs)
			{
				foreach($imageIDs as $imageID)
				{
					bp_gallplus_update_image_priv($imageID->id,$priv_lvl,$group_id);
				}
			}
		}
	  return true;
	}
  else
  	return false;
  	
	}
/**
 * bp_gallplus_update_privacy()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_update_privacy($album_id,$priv_lvl,$group_id){
	
    global $bp;
	
	$loc_album = new BP_Gallplus_Album($album_id);

	if(!empty($loc_album->id)){
		if(($loc_album->owner_type == 'admin') || ($loc_album->owner_type == 'group'))
		{
			return true;
		}
		$priv_change = false;
		$loc_album->privacy = $priv_lvl;
		$loc_album->group_id = $group_id;
	
	 	$loc_album->save();
		$imageIDs = $loc_album->query_album_image_ids();
		if($imageIDs)
		{
				foreach($imageIDs as $imageID)
				{
					bp_gallplus_update_image_priv($imageID->id,$priv_lvl,$group_id);
				}
		}
	  return true;
 	
	}
	else
	{
	  return false;
	}		
}

/**
 * bp_gallplus_edit_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_edit_image($id,$title,$description,$priv_lvl,$enable_comments){
    
	global $bp;
	
	$pic = new BP_Gallplus_Image($id);

	if(!empty($pic->id)){

	    	$title = esc_attr( strip_tags($title) );
		$description = esc_attr( strip_tags($description) );

		$title = apply_filters( 'bp_gallplus_title_before_save', $title );
		$description = apply_filters( 'bp_gallplus_description_before_save', $description);

		if ( $pic->title != $title || $pic->description != $description || $pic->privacy != $priv_lvl){
		    $pic->title = $title;
		    $pic->description = $description;
		    $pic->privacy = $priv_lvl;
		    
		    $save_res = $pic->save();
		}
		else{
		    $save_res = true;	
		}
	    
	    if(bp_is_active('activity')){
	    	if ($enable_comments) 
	    		bp_gallplus_record_activity($pic);
	    	else{
	    		bp_gallplus_delete_activity($pic->id);
	    	}
	    }
	    
	    return $save_res;
    
	}
	else
		return false;
}
/**
 * bp_gallplus_edit_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_update_image_priv($id,$priv_lvl,$group_id){
    
	global $bp;
	
	$pic = new BP_Gallplus_Image($id);

	if(!empty($pic->id)){

    $pic->privacy = $priv_lvl;
    $pic->group_id = $group_id;
    $save_res = $pic->save();
   	    
   return $save_res;
    
	}
	else
		return false;
}
/**
 * bp_gallplus_delete_album_ajax()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */

function bp_delete_album_ajax()
{
		if( $_POST[ 'albumID' ] )
		{
			$id = $_POST[ 'albumID' ];
			if (bp_gallplus_delete_album($id))
			{
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
		}

}

add_action( 'wp_ajax_BPGPLSDeleteAlbum', 'bp_delete_album_ajax' );
/**
 * bp_gallplus_delete_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_delete_album($id=false){
    
	global $bp;
	if(!$id) return false;

	
	$album = new bp_gallplus_Album($id);
	
	if(!empty($album->id)){
		$imageIDs = $album->query_album_image_ids();
		if($imageIDs)
		{
			foreach($imageIDs as $imageID)
			{
				bp_gallplus_delete_image($imageID->id);
			}
		}
		bp_gallplus_delete_activity( $album->id );
		$album->delete();
		return true;
	}
	else
	{
		return false;
	}
}
	
function bp_delete_image_ajax()
{
		if( $_POST[ 'imageID' ] )
		{
			$id = $_POST[ 'imageID' ];
			if (bp_gallplus_delete_image($id))
			{
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
		}

}

add_action( 'wp_ajax_BPGPLSDeleteImage', 'bp_delete_image_ajax' );

/**
 * bp_gallplus_delete_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_delete_image($id=false){
    
	global $bp;
	if(!$id) return false;
	
	$pic = new bp_gallplus_Image($id);
	
	if(!empty($pic->id)){
	
		if(!$bp->album->bp_gallplus_keep_files)
		{
			@unlink($pic->pic_org_path);
			@unlink($pic->pic_mid_path);
			@unlink($pic->pic_thumb_path);
		}
		
		bp_gallplus_delete_activity( $pic->id );
		
		return $pic->delete();
	
	}
	else
		return false;
}

/**
 * bp_gallplus_delete_by_user_id()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_delete_by_user_id($user_id,$remove_files = true){
    
	global $bp;
	
	if($remove_files){
		$pics = bp_gallplus_Image::query_images(array(
					'owner_type' => 'user',
					'owner_id' => $user_id,
					'per_page' => false,
					'id' => false
			));
		
		if($pics) foreach ($pics as $pic){
		
			@unlink($pic->pic_org_path);
			@unlink($pic->pic_mid_path);
			@unlink($pic->pic_thumb_path);
		
		}
	}
	   
	if (function_exists('bp_activity_delete')) {
	
	bp_activity_delete(array('component' => $bp->album->id,'user_id' => $user_id));
	
	}
	
	return BP_Gallplus_Image::delete_by_user_id($user_id);
}
/**
 * bp_gallplus_record_album_activity()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_record_album_activity($album_data, $update = false) {

	global $bp;

	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}
		
	$id = bp_activity_get_activity_id(array('component'=> $bp->album->id,'item_id' => $album_data->id));


	$primary_link = bp_core_get_user_domain($album_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$album_data->feature_image . '/';


	
	$title = $album_data->title;
	$desc = $album_data->description;

	// Using mb_strlen adds support for unicode (asian languages). Unicode uses TWO bytes per character, and is not
	// accurately counted in most string length functions
	// ========================================================================================================

	if ( function_exists( 'mb_strlen' ) ) {

	    $title = ( mb_strlen($title)<= 20 ) ? $title : mb_substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( mb_strlen($desc)<= 400 ) ? $desc : mb_substr($desc, 0 ,400-1).'&#8230;';

	} 
	else {

	    $title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
	}
	if($update)
	{
		$action = sprintf( __( '%s updated gallery: %s', 'bp-galleries-plus' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );
	}
	else
	{
		$action = sprintf( __( '%s uploaded a new gallery: %s', 'bp-galleries-plus' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );
	}

	// Image path workaround for virtual servers that do not return correct base URL
	// ===========================================================================================================

	if($bp->album->bp_gallplus_url_remap == true){

	    $filename = substr( $album_data->feature_image_path, strrpos($album_data->feature_image_path, '/') + 1 );
	    $owner_id = $album_data->owner_id;
	    $image_path = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;
	}
	else {

	    $image_path = bp_get_root_domain().$album_data->feature_image_path;
	}

	// ===========================================================================================================

//	$content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';
	if ($album_data->privacy < 3)
	{
		$content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';
	}
	else
	{
		switch($album_data->privacy < 3)
		{
			case 5: 
			case 3: $content = '<p> For Group Members </p>';
							break;
		 	case 4: $content = '<p> For Friends Only </p>';
							break;
		 	case 6: $content = '<p> Private </p>';
							break;
			default : $content = '<p> Private </p>';
							break;
		}	
	}
	
	$type = 'bp_gallplus_image';
	$item_id = $album_data->id;
	$hide_sitewide = flase; //$album_data->privacy != 0;

	$returnValue =  bp_activity_add( array( 'id' => $id, 'user_id' => $album_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'recorded_time' => $album_data->date_updated , 'hide_sitewide' => $hide_sitewide ) );	

	return $returnValue;
}
function bp_gallplus_record_group_album_activity( $group_id, $image_id ) {
	global $bp;

		if ( !$group_id ) return;
	$group = new BP_Groups_Group( $group_id, true );
	$image = new BP_Gallplus_Image($image_id);
	$title = $group->name;
	//$activity_action = sprintf( __( '%s published the post %s in the group %s:', 'buddypress'), bp_core_get_userlink( $post->post_author ), '<a href="' . get_permalink( $post->ID ) .'">' . attribute_escape( $post->post_title ) . '</a>', '<a href="' . bp_get_group_permalink( $group ) . '">' . attribute_escape( $group->name ) . '</a>' );
	$activity_action = sprintf( __( '%s Posted an image to : %s', 'bp-galleries-plus' ), bp_core_get_userlink($image->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );
	//$activity_content = bp_create_excerpt( $post->post_content );
	if($bp->album->bp_gallplus_url_remap == true){

	    $filename = substr( $image->pic_thumb_url, strrpos($image->pic_thumb_url, '/') + 1 );
	    $owner_id = $image->owner_id;
	    $image_path = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;
	}
	else {

	    $image_path = bp_get_root_domain().$image->pic_thumb_url;
	}
	$primary_link = bp_core_get_user_domain($image->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$image->id . '/';
	$activity_content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';

	/* Record this in activity streams */
	$r = groups_record_activity( array(
		'action' =>  $activity_action,
		'content' => $activity_content, 
		'primary_link' => $primary_link,
		'type' => 'new_groupblog_post',
		'item_id' => $group_id,
		'secondary_item_id' => $image->owner_id,
		'hide_sitewide' => 0
	) );

}
/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */
 
 /**
 * bp_gallplus_record_activity()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_record_activity($pic_data) {

	global $bp;

	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}
		
	$id = bp_activity_get_activity_id(array('component'=> $bp->album->id,'item_id' => $pic_data->id));

	$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';
	
	$title = $pic_data->title;
	$desc = $pic_data->description;

	// Using mb_strlen adds support for unicode (asian languages). Unicode uses TWO bytes per character, and is not
	// accurately counted in most string length functions
	// ========================================================================================================

	if ( function_exists( 'mb_strlen' ) ) {

	    $title = ( mb_strlen($title)<= 20 ) ? $title : mb_substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( mb_strlen($desc)<= 400 ) ? $desc : mb_substr($desc, 0 ,400-1).'&#8230;';

	} 
	else {

	    $title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
	    $desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
	}
	
	$action = sprintf( __( '%s uploaded a new photo: %s', 'bp-galleries-plus' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );

	// Image path workaround for virtual servers that do not return correct base URL
	// ===========================================================================================================

	if($bp->album->bp_gallplus_url_remap == true){

	    $filename = substr( $pic_data->pic_thumb_url, strrpos($pic_data->pic_thumb_url, '/') + 1 );
	    $owner_id = $pic_data->owner_id;
	    $image_path = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;
	}
	else {

	    $image_path = bp_get_root_domain().$pic_data->pic_thumb_url;
	}

	// ===========================================================================================================

	$content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';
	
	$type = 'bp_gallplus_image';
	$item_id = $pic_data->id;
	$hide_sitewide = $pic_data->privacy != 0;

	return bp_activity_add( array( 'id' => $id, 'user_id' => $pic_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );	
}

 /**
 * bp_gallplus_delete_activity()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_delete_activity( $user_id ) {
    
	global $bp;
	
	if ( !function_exists( 'bp_activity_delete' ) ) {
		return false;
	}
		
	bp_activity_delete(array('component' => $bp->album->id,'secondary_item_id' => $user_id));
	return bp_activity_delete(array('component' => $bp->album->id,'item_id' => $user_id));
}

/**
 * bp_gallplus_remove_data()
 *
 * It's always wise to clean up after a user has been deleted. This stops the database from filling up with
 * redundant information.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_delete_user_data( $user_id ) {
	
	bp_gallplus_delete_by_user_id( $user_id );
	
	do_action( 'bp_gallplus_delete_user_data', $user_id );
}

add_action( 'wpmu_delete_user', 'bp_gallplus_delete_user_data', 1 );
add_action( 'delete_user', 'bp_gallplus_delete_user_data', 1 );



/**
 * Dumps an entire object or array to a html based page in human-readable format.
 *
 * @author http://ca2.php.net/manual/en/function.var-dump.php#92594
 * @param pointer &$var | Variable to be dumped
 * @param string $info | Text to add to dumped variable html block, when dumping multiple variables.
 */

function bp_gallplus_dump(&$var, $info = FALSE) {
    
    $scope = false;
    $prefix = 'unique';
    $suffix = 'value';
    
    if($scope) $vals = $scope;
    else $vals = $GLOBALS;

    $old = $var;
    $var = $new = $prefix.rand().$suffix; $vname = FALSE;
    foreach($vals as $key => $val) if($val === $new) $vname = $key;
    $var = $old;

    echo "<pre style='margin: 0px 0px 10px 0px; display: block; background: white; color: black; font-family: Verdana; border: 1px solid #cccccc; padding: 5px; font-size: 10px; line-height: 13px;'>";
    if($info != FALSE) echo "<b style='color: red;'>$info:</b><br>";
    bp_gallplus_do_dump($var, '$'.$vname);
    echo "</pre>";
}

/**
 * Recursive iterator function used by bp_gallplus_dump()
 *
 * @author http://ca2.php.net/manual/en/function.var-dump.php#92594
 * @see bp_gallplus_dump()
 */

function bp_gallplus_do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL) {
    
    $do_dump_indent = "<span style='color:#eeeeee;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference.$var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme'; $keyname = 'referenced_object_name';

    if (is_array($var) && isset($var[$keyvar])) {

        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#a2a2a2'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    }
    else {
	
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if($type == "String") $type_color = "<span style='color:green'>";
        elseif($type == "Integer") $type_color = "<span style='color:red'>";
        elseif($type == "Double"){ $type_color = "<span style='color:#0099c5'>"; $type = "Float"; }
        elseif($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif($type == "NULL") $type_color = "<span style='color:black'>";

        if(is_array($avar)) {
	    
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => ":"") . "<span style='color:#a2a2a2'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach($keys as $name)
            {
                $value = &$avar[$name];
                bp_gallplus_do_dump($value, "['$name']", $indent.$do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        }
        elseif(is_object($avar)) {
	    
            echo "$indent$var_name <span style='color:#a2a2a2'>$type</span><br>$indent(<br>";
            foreach($avar as $name=>$value) bp_gallplus_do_dump($value, "$name", $indent.$do_dump_indent, $reference);
            echo "$indent)<br>";
        }
        elseif(is_int($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_string($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color\"$avar\"</span><br>";
        elseif(is_float($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color$avar</span><br>";
        elseif(is_bool($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $type_color".($avar == 1 ? "TRUE":"FALSE")."</span><br>";
        elseif(is_null($avar)) echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#a2a2a2'>$type(".strlen($avar).")</span> $avar<br>";

        $var = $var[$keyvar];
    }
}

/**
 * Adds *all* images in the database which users have marked as *public* to the user and site activity streams. Distributes the created activity
 * stream posts over the entire history of site to make the posts look natural. Detects images that already exist in the activity stream and
 * does not create posts for them.
 *
 * This function marks the activity stream posts it creates with secondary_item_id = 999 so that they can be deleted easily if it is necessary to
 * undo the changes. Do not try to remove created using an SQL query as it will not delete comments that users have added to the created posts, use
 * bp_activity_delete() in bp-activity.php
 *
 */

/**
 * bp_gallplus_rebuild_activity()
 *
 * It's always wise to clean up after a user has been deleted. This stops the database from filling up with
 * redundant information.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_rebuild_activity() {

	global $bp, $wpdb;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}

	// Fetch all "public" images from the database
	$sql =  $wpdb->prepare( "SELECT * FROM {$bp->album->table_name} WHERE privacy = 0") ;
	$results = $wpdb->get_results( $sql );

	// Handle users that decide to run the function on sites with no uploaded content.
	//--------------------------------------------------------------------------------
	if(!$results){
	    return;
	}

	// Create an activity stream post for each image, with a special secondary_item_id so we can easily find our posts
	// ===============================================================================================================

	foreach($results as $pic_data){

		// Check if the item *already* has an activity stream post

		$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE component = '{$bp->album->id}' AND item_id = {$pic_data->id}");
		$has_post = $wpdb->get_var( $sql );

		// Create activity stream post

		if( !$has_post){

			$primary_link = bp_core_get_user_domain($pic_data->owner_id) . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$pic_data->id . '/';

			$title = $pic_data->title;
			$desc = $pic_data->description;
			$title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
			$desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';

			$action = sprintf( __( '%s uploaded a new photo: %s', 'bp-galleries-plus' ), bp_core_get_userlink($pic_data->owner_id), '<a href="'. $primary_link .'">'.$title.'</a>' );

			// Image path workaround for virtual servers that do not return correct base URL
			// ===========================================================================================================

			if($bp->album->bp_gallplus_url_remap == true){

			    $filename = substr( $pic_data->pic_thumb_url, strrpos($pic_data->pic_thumb_url, '/') + 1 );
			    $owner_id = $pic_data->owner_id;
			    $image_path = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;
			}
			else {

			    $image_path = bp_get_root_domain().$pic_data->pic_thumb_url;
			}

			// ===========================================================================================================

			$content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="'. $image_path .'" /></a>'.$desc.'</p>';

			$type = 'bp_gallplus_image';
			$item_id = $pic_data->id;
			$hide_sitewide = $pic_data->privacy != 0;

			bp_activity_add( array('user_id' => $pic_data->owner_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $bp->album->id, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => 999,'recorded_time' => $pic_data->date_uploaded , 'hide_sitewide' => $hide_sitewide ) );

		}

	}
	unset($results); unset($pic_data);

	// Find the site's oldest activity stream post, get its date, and convert it into a unix integer timestamp. Note this handles
	// sites with *zero* activity stream posts, because we added (potentially thousands of) them in the previous step.
	// ================================================================================================================================

	$sql = $wpdb->prepare( "SELECT date_recorded FROM {$bp->activity->table_name} ORDER BY date_recorded ASC LIMIT 1");
	$oldest_post_date = $wpdb->get_var( $sql );

	$full = explode(' ', $oldest_post_date);
	$date = explode('-', $full[0]);
	    $time = explode(':', $full[1]);

	    $year = $date[0];
	    $month = $date[1];
	    $day = $date[2];

	    $hour = $time[0];
	    $minute = $time[1];
	    $second = $time[2];

	    $oldest_unix_date = mktime($hour, $minute, $second, $month, $day, $year);
	$current_date = time();

	// Set each of our marked activity stream items to a random date between the first activity stream post date and the current date
	// ================================================================================================================================

	$sql = $wpdb->prepare( "SELECT id FROM {$bp->activity->table_name} WHERE component = '{$bp->album->id}' AND secondary_item_id = 999");
	$results = $wpdb->get_results( $sql );

	foreach($results as $post){

		$new_date = gmdate( "Y-m-d H:i:s", rand($oldest_unix_date, $current_date) );

		$sql = $wpdb->prepare( "UPDATE {$bp->activity->table_name} SET date_recorded = '{$new_date}' WHERE id = {$post->id}");
		$wpdb->query( $sql );	    
	}
	unset($results); unset($post);
	
}

/**
 * Removes all posts that were created by bp_gallplus_rebuild_activity() from the activity stream.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_undo_rebuild_activity() {

	global $bp, $wpdb;


	// Handle users that try to run the function when the activity stream is disabled
	if ( !function_exists( 'bp_activity_delete' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}

	return bp_activity_delete(array('component' => $bp->album->id,'secondary_item_id' => 999));

}
function bp_feature_image_ajax()
{
	$nonce = $_POST['BPGPLSFeatureImageNonce'];

	// check to see if the submitted nonce matches with the
	// generated nonce we created earlier
	if ( ! wp_verify_nonce( $nonce, 'BPGPLSFeatureImage' ) )
	{
				echo 'fail';
				return false;
	}
		if( ($_POST[ 'imageID' ] ) &&($_POST[ 'albumID' ]))
		{
			$id = $_POST[ 'imageID' ];
			$album_id = $_POST[ 'albumID' ];
			if (bp_gallplus_feature_image($album_id,$id))
			{
				echo 'success';
			}
			else
			{
				echo 'fail';
			}
		}

}
add_action( 'wp_ajax_BPGPLSFeatureImage', 'bp_feature_image_ajax' );

/**
 * bp_gallplus_feature_image()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_feature_image($album_id=false,$id=false){
    
	global $bp;
	if(!$album_id) return false;
	if(!$id) return false;
	
	$album = new BP_Gallplus_Album($album_id);
	$pic = new BP_Gallplus_Image($id);
	
	if((!empty($pic->id)) && (!empty($album->id))){
	
		$album->feature_image = $pic->id;
		$album->feature_image_path = $pic->pic_thumb_url;
	
		return $album->save();
	
	}
	else
		return false;
}
function bp_gallplus_album_add_like($album_id, $user_id ='') {
global $bp;
	if ( !$album_id )
		return false;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;
		
	/* Add to the users liked activities. */
	$user_likes = get_user_meta( $user_id, 'bp_liked_albums', true );
	$user_likes[$album_id] = 'album_liked';
	update_user_meta( $user_id, 'bp_liked_albums', $user_likes );
	$album = new BP_Gallplus_Album($album_id);
	$album->like_count++;
	$album->save();
	return $album->like_count;
}
function bp_gallplus_image_add_like($image_id, $user_id ='') {
global $bp;
	if ( !$image_id )
		return false;

	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;
		
	/* Add to the users liked activities. */
	$user_likes = get_user_meta( $user_id, 'bp_liked_images', true );
	$user_likes[$image_id] = 'image_liked';
	update_user_meta( $user_id, 'bp_liked_images', $user_likes );
	$image = new BP_Gallplus_Image($image_id);
	$image->like_count++;
	$image->save();
	return $image->like_count;
}
/**
 * bp_gallplus_like_process_ajax()
 *
 * Runs the relevant function depending on what Ajax call has been made.
 *
 */
function bp_gallplus_like_process_ajax() {
	global $bp;

	$id = preg_replace( "/\D/", "", $_POST['id'] ); 
	
	if ( $_POST['type'] == 'like_album' )
	{
		echo(bp_gallplus_album_add_like( $id ));
	}
	if ( $_POST['type'] == 'like_image' )
	{
		echo(bp_gallplus_image_add_like( $id ));
	}
	
/*	if ( $_POST['type'] == 'unlike' )
		bp_like_remove_user_like( $id, 'activity' );

	if ( $_POST['type'] == 'view-likes' )
		bp_like_get_likes( $id, 'activity' );

	if ( $_POST['type'] == 'like_blogpost' )
		bp_like_add_user_like( $id, 'blogpost' );

	if ( $_POST['type'] == 'unlike_blogpost' )
		bp_like_remove_user_like( $id, 'blogpost' );
*/
	die();
}
add_action( 'wp_ajax_BPGPLSAlbumLike', 'bp_gallplus_like_process_ajax' );
/**
 * bp_gallplus_like_is_liked()
 *
 * Checks to see whether the user has liked a given item.
 *
 */
function bp_gallplus_like_is_liked( $item_id = '', $type = '', $user_id = '' ) {
	global $bp;
	
	if ( !$type )
		return false;
	
	if ( !$item_id )
		return false;
	
	if ( !$user_id )
		$user_id = $bp->loggedin_user->id;
	
	if ( $type == 'album' )
		$user_likes = get_user_meta( $user_id, 'bp_liked_albums', true );
	
	if ( $type == 'image' )
		$user_likes = get_user_meta( $user_id, 'bp_liked_images', true );
	
	if ( !$user_likes ){
		return false;
	} elseif ( !array_key_exists( $item_id, $user_likes ) ) {
		return false;
	} else {
		return true;
	};
}

/**
 * bp_gallplus_like_button()
 *
 * Outputs the 'Like/Unlike' and 'View likes/Hide likes' buttons.
 *
 */
function bp_gallplus_like_button( $id = '', $type = '' ) {
	
	$users_who_like = 0;
	$liked_count = 0;
	
	/* Set the type if not already set, and check whether we are outputting the button on a blogpost or not. */
	if ( !$type  )
		$type = 'album';
	
	if ( $type == 'album' ) :
	
		$album = new BP_Gallplus_Album($id);
	
		if ( is_user_logged_in() ) :
			
				$liked_count = $album->like_count;
			
			if ( !bp_gallplus_like_is_liked( $id, 'album' ) ) : ?>
				<span><a href="#" class="like_album" id="like-album-<?php echo $id; ?>" title="like this album"><img src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/thumbsupsm.png" /><?php  if ( $liked_count ) echo '<span>[' . $liked_count . ']</span>'; ?></a></span>
			<?php else : ?>
				<span><img class="liked_album" src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/thumbsupsmgray.png" alt="You already like this album"/><?php  if ( $liked_count ) echo ' <span>[' . $liked_count . ']</span>'; ?></span>
			<?php endif;
			
		endif;
	
	elseif ( $type == 'image' ) :
		$image = new BP_Gallplus_Image($id);
		$liked_count = $image->like_count;
		
		if ( !bp_gallplus_like_is_liked( $id, 'image' ) ) : ?>
		
				<!-- span><img src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/thumbsupsm.png" class="like_image" id="like-image-<?php echo $id; ?>" alt="Like this image"/><?php  if ( $liked_count ) echo '<span style="vertical-align: top;">[' . $liked_count . ']</span>'; ?></span -->
				<span><a href="#" class="like_image" id="like-album-<?php echo $id; ?>" title="like this image"><img src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/thumbsupsm.png" /></a><?php  if ( $liked_count ) echo '<span style="vertical-align: bottom;">[' . $liked_count . ']</span>';else echo '<span style="vertical-align: bottom;">&nbsp;</span>' ?></span>
		
		<?php else : ?>
				<span><img class="liked_image" src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/thumbsupsmgray.png" alt="You already like this image"/><?php  if ( $liked_count ) echo '<span >['.$liked_count.']</span>'; ?></span>
		<?php endif;

	endif;
}

/**
 * bp_gallplus_donate_button()
 *
 * Adds a donation button.
 *
 */
function bp_gallplus_donate_button( $id = '', $type = '' ) {
	
	$users_who_like = 0;
	$liked_count = 0;
	
	/* Set the type if not already set, and check whether we are outputting the button on a blogpost or not. */
	if ( !$type  )
		$type = 'album';
	
	if ( $type == 'album' ) :
	
		$album = new BP_Gallplus_Album($id);
	
		$donationLink = xprofile_get_field_data('Donation Link', $album->owner_id);
		$verifiedLink = false;
		if($donationLink)
		{
			$location = strpos($donationLink,'https://www.paypal.com');
			if($location !== false)
			{
				$verifiedLink = true;
			}
		}
		if($verifiedLink ) : ?>
		
				<a href="<?php echo $donationLink ?>" class="donate" id="fdonate-<?php echo $id; ?>" title="Donate"><img src="<?php echo BP_GALLPLUS_PLUGIN_URL ?>includes/images/gifticon.png" /> Make a donation</a>
			<?php endif;
			
		endif;
}


add_action( 'wp_ajax_activity_like', 'bp_like_process_ajax' );


function bp_gallplus_privacy_ajax()
{

	$nonce = $_GET['BPGPLSAlbumPrivacyNonce'];
	$callback = $_GET['callback'];
	// check to see if the submitted nonce matches with the
	// generated nonce we created earlier
	if ( ! wp_verify_nonce( $nonce, 'BPGPLSAlbumPrivacy' ) )
	{
			$result[] = array('result'=>fail,'privacy'=>'');
	}
	else
	{
		if( $_GET[ 'albumID' ])
		{
			$album_id = $_GET[ 'albumID' ];
			$album_privacy = bp_gallplus_privacy($album_id);
			if ($album_privacy !== false)
			{
				$result[] = array('result'=>success,'privacy'=>$album_privacy);
			}
			else
			{
				$result[] = array('result'=>fail,'privacy'=>'');
			}
		}
		else
		{
				$result[] = array('result'=>fail,'privacy'=>'');
		}
	}
	echo $callback.'('.json_encode($result).');';
}
add_action( 'wp_ajax_BPGPLSAlbumPrivacy', 'bp_gallplus_privacy_ajax' );

/**
 * bp_gallplus_privacy()
 *
 * @version 0.1.8.11
 * @since 0.1.8
 */
function bp_gallplus_privacy($album_id=false){
    
	global $bp;
	if(!$album_id) return false;
	
	$album = new BP_Gallplus_Album($album_id);
	
	if(!empty($album->id)){
		$privacy[] = array('albumID'=>$album->id,'privacy'=>$album->privacy,'groupID'=>$album->group_id);	
		return $privacy;
	}
	else
		return false;
}

function bp_gallplus_member_group_name($user_id = false)
{
	global $wpdb, $bp;
	if(!$user_id)
	{
		return false;
	}
	$results = BP_Groups_Member::get_group_ids( $user_id ); 
	$first = true;
	if($results['total'] > 0)
	{
		for($i=0; $i < $results['total']; $i++)
		{
			$group_id = $results['groups'][$i];
			$group = new BP_Groups_Group( $group_id, true );
			$groups_array[] = array('name' => $group->name,'id' => $group_id);
		}
		return array( 'groups' => $groups_array);
	}
	return false;	
}

function bp_gallplus_member_groups($user_id = false)
{	
	global $wpdb, $bp;
	if(!$user_id)
	{
		return false;
	}
	$results = BP_Groups_Member::get_group_ids( $user_id ); 
	$first = true;
	if($results['total'] > 0)
	{
		for($i=0; $i < $results['total']; $i++)
		{
			$group_id = $results['groups'][$i];
			$sql = $wpdb->prepare( "SELECT id, title FROM {$bp->album->albums_table_name} WHERE group_id = %d AND privacy = %d",$group_id,5) ;
			$locNames  = $wpdb->get_results( $sql );
			if($first)
			{
				$albumNames = $locNames;
			}
			else
			{
				$albumNames = array_merge($albumNames, $locNames);
			}
		}
		if(count( $albumNames) > 0)
		{
			return $albumNames;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}

}

function bp_gallplus_group_has_album($group_id = false)
{	
	if(!$group_id)
	{
		return false;
	}

	$results = BP_Gallplus_Album::query_group_album($group_id,5);
	if(count($results))
	{
		return true;
	}
	return false;
}

function bp_gallplus_nav_album_count(){
    
	global $bp;
	
	$owner_id = $bp->displayed_user->id;
	
	$results = BP_Gallplus_Album::query_images(array('owner_id'=> $owner_id, 'privacy'=>'all', 'priv_override'=>true));
	return count($results);
}







// JLL_MOD - added functions
//////////////////////////////////////////////////////////////////////////////////////////////////////
// JLL_MOD - enqueue scripts and css for photo-tagging
function init_photo_tagging() {
	global $bp;
    if ($bp->current_component == 'album') {
		
      //  wp_deregister_script( 'jquery' );
        wp_register_script( '-jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js');
        wp_enqueue_script( '-jquery' );
		
		wp_deregister_script( 'jqueryui' );
        wp_register_script( 'jqueryui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js');
        wp_enqueue_script( 'jqueryui' );
		
        wp_register_script( 'jquerytag', $bp->root_domain . '/wp-content/plugins/bp-gallery/photo-tagging/jquery.tag.js');
        wp_enqueue_script( 'jquerytag' );
 		
    }
}
add_action('init', 'init_photo_tagging');


// JLL_MOD - Add notification format
function photos_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $wpdb, $bp;

	switch ( $action ) {
		case 'user_tagged':
			$tagid = $item_id;
			$photo_id = $secondary_item_id;
			
			$table_name = $wpdb->prefix . "bp_gallplus";
			$photos = $wpdb->get_results( "SELECT owner_id FROM " . $table_name. " WHERE id=" . $photo_id, ARRAY_A );

			$photo_owner = $photos[0];
			$photo_owner_id = $photo_owner[owner_id];
			$photo_owner_name = bp_core_get_user_displayname( $photo_owner_id );
			$photo_tag_link = bp_core_get_user_domain( $photo_owner_id ) . $bp->album->slug . '/image/' . $photo_id . '/';

			if ( (int)$total_items > 1 ) {
				return apply_filters( 'new_photo_tags_notification', '<a href="' . $photo_tag_link . '" title="New Photo Tag">' . sprintf( '%d new tags in %s \'s photo', (int)$total_items, $photo_owner_name ) . '</a>', $photo_tag_link, $total_items, $photo_owner_name );
			} else {
				return apply_filters( 'new_photo_tag_notification', '<a href="' . $photo_tag_link . '" title="Someone tagged you in ' . $photo_owner_name .'\'s photo">' . sprintf( 'You were tagged in %s\'s photo', $photo_owner_name ) . '</a>', $photo_tag_link, $photo_owner_name );
			}
		break;
		
	}

	do_action( 'photos_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}


// JLL_MOD - Add notification Emails --- NOT ACTIVE
function photo_tagged_notification( $photo_id, $photo_owner_id, $tagged_id ) {
	global $bp;

	$photo_owner_name = bp_core_get_user_displayname( $photo_owner_id );
	$ud = get_userdata( $tagged_id );
	$photo_owner_ud = get_userdata( $photo_owner_id );
	$photo_tag_link = bp_core_get_user_domain( $photo_owner_id ) . $bp->album->slug . '/image/' . $photo_id . '/';
	$settings_link = bp_core_get_user_domain( $tagged_id ) .  BP_SETTINGS_SLUG . '/notifications';
	$photo_owner_link = bp_core_get_user_domain( $photo_owner_id );

	//bp_core_add_notification( $photo_id, $tagged_id, BP_GALLPLUS_SLUG, 'user_tagged', $photo_owner_id )


	// Set up and send the message
	$to       = $ud->user_email;
	$sitename = wp_specialchars_decode( get_blog_option( BP_ROOT_BLOG, 'blogname' ), ENT_QUOTES );
	$subject  = '[' . $sitename . '] ' . sprintf( __( 'You were tagged in %s\'s photo', 'buddypress' ), $photo_owner_name );

	$message = sprintf( __(
"Someone tagged you in %s\'s photo.

To view the photo you're tagged in: %s

To view %s's profile: %s

---------------------
", 'buddypress' ), $photo_owner_name, $photo_tag_link, $photo_owner_name, $photo_owner_link );

	$message .= sprintf( __( 'To disable these notifications please log in and go to: %s', 'buddypress' ), $settings_link );

	/* Send the message */
	$to = apply_filters( 'friends_notification_new_request_to', $to );
	$subject = apply_filters( 'friends_notification_new_request_subject', $subject, $photo_owner_name );
	$message = apply_filters( 'friends_notification_new_request_message', $message, $photo_owner_name, $photo_owner_link, $photo_tag_link );

	wp_mail( $to, $subject, $message );
}

?>