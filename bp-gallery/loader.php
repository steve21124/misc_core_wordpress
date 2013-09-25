<?php
/*
Plugin Name: BP Gallery Plus
Plugin URI: http://www.amkd.com.au/wordpress/bp-gallery-plugin/98
Description: Based on the orginal BP Photos+tags by Jesse Lareaux. This plugin enables users on a BuddyPress site to create multiple albums. Albums can be given the usual privacy restrictions, with the addition of giving Album access to members of a group they have created.
Version: 1.2.5
Revision Date: January 8, 2013
Requires at least: 3.1
Tested up to: WP 3.5, BP 1.6.2
Author: Caevan Sachinwalla
Author URI: http://www.amkd.com.au
Network: true
*/
// JLL_MOD - changed plugin header
define('BP_PLUGIN_PATH', WP_PLUGIN_DIR.'/bp-gallery/');

/**
 * Attaches BuddyPress Album to Buddypress.
 *
 * This function is REQUIRED to prevent WordPress from white-screening if BuddyPress Album is activated on a
 * system that does not have an active copy of BuddyPress.
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bpgpls_init() {
	
	require( dirname( __FILE__ ) . '/includes/bpa.core.php' );
	
	do_action('bpgpls_init');
	
}
add_action( 'bp_include', 'bpgpls_init' );

/**
 * bp_gallplus_install()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_install(){
	global $bp,$wpdb;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(CheckNewAlbumsTableExists() )
		{
			// If the New Albums table exists we can assume the installation has the renamed tables
			if(CheckNewAlbumsTableExists())
			{
				AddNewAlbumTableFields();				
			}
			return;
		}

		if(CheckOldAlbumsTableExists())
		{
			$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if ($mysqli->connect_error) 
			{
    		bp_logdebug('bp_gallplus_install: Connect Error (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
    		return;
			}
			// This is an old installation of BP Gallery Plus and needs to have the tables renamed
			$reanmeSql = "RENAME TABLE `".$wpdb->base_prefix."bp_album` TO `".$wpdb->base_prefix."bp_gallplus_album`";
			if ($mysqli->query($reanmeSql) === TRUE) 
			{
    		bp_logdebug("bp_gallplus_install: bp_album table successfully renamed");
			}
			$reanmeSql = "RENAME TABLE `".$wpdb->base_prefix."bp_albums` TO `".$wpdb->base_prefix."bp_gallplus_albums`";
			if ($mysqli->query($reanmeSql) === TRUE) 
			{
    		bp_logdebug("bp_gallplus_install: bp_albums table successfully renamed");
			$reanmeSql = "RENAME TABLE `".$wpdb->base_prefix."bp_album_tags` TO `".$wpdb->base_prefix."bp_gallplus_tags`";
			}
			if ($mysqli->query($reanmeSql) === TRUE) 
			{
    		bp_logdebug("bp_gallplus_install: bp_album_tags table successfully renamed");
    	}
    	BPGPlusTransferOptions();
			return;
		}
		// Since BP Gallery Plus has not been installed on the installation before we can create new albums
    $sql[] = "CREATE TABLE {$wpdb->base_prefix}bp_gallplus_album (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            owner_type varchar(10) NOT NULL,
	            owner_id bigint(20) NOT NULL,
	            date_uploaded datetime NOT NULL,
	            title varchar(250) NOT NULL,
	            description longtext NOT NULL,
	            privacy tinyint(2) NOT NULL default '0',
	            pic_org_url varchar(250) NOT NULL,
	            pic_org_path varchar(250) NOT NULL,
	            pic_mid_url varchar(250) NOT NULL,
	            pic_mid_path varchar(250) NOT NULL,
	            pic_thumb_url varchar(250) NOT NULL,
	            pic_thumb_path varchar(250) NOT NULL,
							album_id bigint(20) NOT NULL,
  						like_count bigint(20) default '0',
  						feature_image tinyint(1) default NULL,
	  					group_id bigint(20) NOT NULL default '0',
	  					media_type int(11) default '0',
            	KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY album_id (album_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";
	
	$sqlalbums[] = "CREATE TABLE {$wpdb->base_prefix}bp_gallplus_albums (
  						id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  						owner_type varchar(10) character set utf8 NOT NULL,
  						owner_id bigint(20) NOT NULL,
  						date_created datetime NOT NULL,
 							date_updated datetime NOT NULL,
 							title varchar(250) character set utf8 NOT NULL,
  						description longtext character set utf8 NOT NULL,
  						privacy tinyint(2) NOT NULL default '0',
  						album_org_url varchar(250) character set utf8 NOT NULL,
  						feature_image_path varchar(250) character set utf8 NOT NULL,
  						feature_image bigint(20) NOT NULL,
  						like_count bigint(20) NOT NULL default '0',
  						group_id bigint(20) NOT NULL,
  						spare2 varchar(250) character set utf8 NOT NULL,
  						spare3 varchar(250) character set utf8 NOT NULL,
  						spare4 varchar(250) character set utf8 NOT NULL,
	            KEY owner_type (owner_type),
	            KEY owner_id (owner_id),
	            KEY privacy (privacy)
	            ) {$charset_collate};";
// JLL_MOD - add a table for face-tagging

    $sqltag[] = "CREATE TABLE {$wpdb->base_prefix}bp_gallplus_tags (
	            id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	            photo_id bigint(20) NOT NULL,
	            tagged_id bigint(20),
	            tagged_name varchar(250) NOT NULL,
	            height bigint(20) NOT NULL,
	            width bigint(20) NOT NULL,
	            top_pos bigint(20) NOT NULL,
	            left_pos bigint(20) NOT NULL,
	            KEY photo_id (photo_id),
	            KEY tagged_id (tagged_id)
	            ) {$charset_collate};";

// JLL_MOD - add a table for face-tagging
	dbDelta($sql);
	dbDelta($sqlalbums);
	dbDelta($sqltag);

	update_site_option( 'bp-galleries-plus-db-version', BP_GALLPLUS_DB_VERSION  );

        if (!get_site_option( 'bp_gallplus_slug' ))
            update_site_option( 'bp_gallplus_slug', 'album');
	
        if ( !get_site_option( 'bp_gallplus_max_upload_size' ))
            update_site_option( 'bp_gallplus_max_upload_size', 1 ); // 1mb

        if (!get_site_option( 'bp_gallplus_max_images' ))
            update_site_option( 'bp_gallplus_max_images', false);

        if (!get_site_option( 'bp_gallplus_max_priv0_images' ))
            update_site_option( 'bp_gallplus_max_priv0_images', false);

        if (!get_site_option( 'bp_gallplus_max_priv2_images' ))
            update_site_option( 'bp_gallplus_max_priv2_images', false);
        
        if (!get_site_option( 'bp_gallplus_max_priv3_images' ))
            update_site_option( 'bp_gallplus_max_priv3_images', false);
            
        if (!get_site_option( 'bp_gallplus_max_priv4_images' ))
            update_site_option( 'bp_gallplus_max_priv4_images', false);
        
        if (!get_site_option( 'bp_gallplus_max_priv6_images' ))
            update_site_option( 'bp_gallplus_max_priv6_images', false);

        if(!get_site_option( 'bp_gallplus_keep_original' ))
            update_site_option( 'bp_gallplus_keep_original', true);
        
        if(!get_site_option( 'bp_gallplus_require_description' ))
            update_site_option( 'bp_gallplus_require_description', false);

        if(!get_site_option( 'bp_gallplus_enable_comments' ))
            update_site_option( 'bp_gallplus_enable_comments', true);

        if(!get_site_option( 'bp_gallplus_enable_wire' ))
            update_site_option( 'bp_gallplus_enable_wire', true);

        if(!get_site_option( 'bp_gallplus_middle_size' ))
            update_site_option( 'bp_gallplus_middle_size', 600);

        if(!get_site_option( 'bp_gallplus_thumb_size' ))
            update_site_option( 'bp_gallplus_thumb_size', 150);
        
        if(!get_site_option( 'bp_gallplus_per_page' ))
            update_site_option( 'bp_gallplus_per_page', 20 );

        if(!get_site_option( 'bp_gallplus_url_remap' ))
	   		 		update_site_option( 'bp_gallplus_url_remap', false);
	   		 
				if(!get_site_option( 'bp_gallplus_viewer' ))
					update_site_option( 'bp_gallplus_viewer', 0 );
				
	      if(!get_site_option( 'bp_gallplus_mid_size' ))
					update_site_option( 'bp_gallplus_mid_size', true );

        if(true) {
		$path = bp_get_root_domain() . '/wp-content/uploads/album';
		update_site_option( 'bp_gallplus_base_url', $path );
	}

}
register_activation_hook( __FILE__, 'bp_gallplus_install' );

/**
 * bp_gallplus_check_installed()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_check_installed() {
	global $wpdb, $bp;

	if ( !current_user_can('install_plugins') )
		return;

	if (!defined('BP_VERSION') || version_compare(BP_VERSION, '1.2','<')){
		add_action('admin_notices', 'bp_gallplus_compatibility_notices' );
		return;
	}

	if ( get_site_option( 'bp-galleries-plus-db-version' ) < BP_GALLPLUS_DB_VERSION )
	{
		bp_gallplus_install();
	}
}
add_action( 'admin_menu', 'bp_gallplus_check_installed' );

/**
 * bp_gallplus_compatibility_notices() 
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_compatibility_notices() {

	if (!defined('BP_VERSION')){    
		$message .= ' BP Gallery Plus needs BuddyPress 1.2 or later to work. Please install Buddypress';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
		
	}elseif(version_compare(BP_VERSION, '1.2','<') ){
		$message .= 'BP Gallery Plus needs BuddyPress 1.2 or later to work. Your current version is '.BP_VERSION.' please upgrade.';
		
		echo '<div class="error fade"><p>'.$message.'</p></div>';
	}
}

/**
 * bp_gallplus_activate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_activate() {
	bp_gallplus_check_installed();
		AddDonationProfileField();
	do_action( 'bp_gallplus_activate' );
}
register_activation_hook( __FILE__, 'bp_gallplus_activate' );

/**
 * bp_gallplus_deactivate()
 *
 *  @version 0.1.8.11
 *  @since 0.1.8.0
 */
function bp_gallplus_deactivate() {
	do_action( 'bp_gallplus_deactivate' );
}
register_deactivation_hook( __FILE__, 'bp_gallplus_deactivate' );

	function bp_logdebug($debugStr)
	{
				if(!is_dir(BP_PLUGIN_PATH.'debug'))
				{
					mkdir(BP_PLUGIN_PATH.'debug');
				}
				global $wp_query;
		   	$BP_DEBUG_DIR = BP_PLUGIN_PATH.'debug/bpdebug'.date('dmY').'.log'; 
		
	    	$date = date('d.m.Y H:i:s'); 
    		$log = $date." : [BP] ".$debugStr."\n"; 
    		error_log($log, 3, $BP_DEBUG_DIR); 
	
	}
function AddDonationProfileField()
{
	global $wpdb;
	$group_args = array(
  	   'name' => 'BP Gallery Plus'
    	 );
  $sqlStr = "SELECT `id` FROM `wp_bp_xprofile_groups` WHERE `name` = 'BP Gallery Plus'";
  $groups = $wpdb->get_results($sqlStr);
  if(count($groups) > 0)
  {
  	bp_logdebug('BP Gallery Plus : Donation group exists : ');
		return;
	}
    	 
	$group_id = xprofile_insert_field_group( $group_args ); // group's ID}
	xprofile_insert_field(
    array (
           field_group_id  => $group_id,
           name            => 'Donation Link',
           can_delete      => false, // Doesn't work *
           field_order  => 1,
           is_required     => false,
           description		=> 'If you want people to be able to make a financial donation to support your albums, enter your Paypal donation link',
           type            => 'textbox'
    )
	);
}
function CheckNewAlbumTableExists()
{
	global $wpdb;
	
	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_gallplus_album`'; 
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
  	bp_logdebug('BP Gallery Plus CheckNewAlbumTableExists : table exists ');  
    return true;
	}
	else
	{
    return false;
	}
}
function CheckOldAlbumTableExists()
{
	global $wpdb;
	
	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_album`';
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
    return true;
	}
	else
	{
    return false;
	}
}
function AddNewAlbumTableFields()
{
	global $wpdb;
	
	$fields = mysql_list_fields(DB_NAME, $wpdb->base_prefix.'bp_gallplus_album');
	$columns = mysql_num_fields($fields);
	for ($i = 0; $i < $columns; $i++) 
	{
		$field_array[] = mysql_field_name($fields, $i);
	}
	if (!in_array('media_type', $field_array))
	{
		$result = mysql_query('ALTER TABLE '. $wpdb->base_prefix.'bp_gallplus_album ADD media_type INT(11)');
	}
}

function CheckNewAlbumsTableExists()
{
	global $wpdb;

	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_gallplus_albums`'; 
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
    return true;
	}
	else
	{
    return false;
	}
}
function CheckOldAlbumsTableExists()
{
	global $wpdb;

	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_albums`';
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
    return true;
	}
	else
	{
    return false;
	}
}
function CheckNewAlbumTagTableExists()
{
	global $wpdb;

	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_gallplus_tags`'; 
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
    return true;
	}
	else
	{
    return false;
	}
}
function CheckOldAlbumTagTableExists()
{
	global $wpdb;

	$sqlStr = 'select 1 from `'.$wpdb->base_prefix.'bp_album_tags`';
	$val = mysql_query($sqlStr);
	if($val !== FALSE)
	{
    return true;
	}
	else
	{
    return false;
	}
}
function BPGPlusTransferOptions()
{

        $bp_gallplus_slug = get_site_option( 'bp_album_slug' );
        $bp_gallplus_max_pictures = get_site_option( 'bp_album_max_pictures' );
        $bp_gallplus_max_upload_size = get_site_option( 'bp_album_max_upload_size' );
        $bp_gallplus_max_priv0_pictures = get_site_option( 'bp_album_max_priv0_pictures' );
        $bp_gallplus_max_priv2_pictures = get_site_option( 'bp_album_max_priv2_pictures' );
        $bp_gallplus_max_priv3_pictures = get_site_option( 'bp_album_max_priv3_pictures' );
        $bp_gallplus_max_priv4_pictures = get_site_option( 'bp_album_max_priv4_pictures' );
        $bp_gallplus_max_priv6_pictures = get_site_option( 'bp_album_max_priv6_pictures' );
        $bp_gallplus_keep_original = get_site_option( 'bp_album_keep_original' );
        $bp_gallplus_require_description = get_site_option( 'bp_album_require_description' );
        $bp_gallplus_enable_comments = get_site_option( 'bp_album_enable_comments' );
        $bp_gallplus_disable_public_access = get_site_option('bp_album_disable_public_access');
        $bp_gallplus_enable_wire = get_site_option( 'bp_album_enable_wire' );
        $bp_gallplus_middle_size = get_site_option( 'bp_album_middle_size' );
        $bp_gallplus_thumb_size = get_site_option( 'bp_album_thumb_size' );
        $bp_gallplus_per_page = get_site_option( 'bp_album_per_page' );
				$bp_gallplus_url_remap = get_site_option( 'bp_album_url_remap' );
				$bp_gallplus_base_url = get_site_option( 'bp_album_base_url' );
	
			update_site_option( 'bp_gallplus_slug', $bp_gallplus_slug );
			update_site_option( 'bp_gallplus_max_images', $bp_gallplus_max_images =='' ? false : intval($bp_gallplus_max_images) );

			update_site_option('bp_gallplus_max_priv0_pictures' , $bp_gallplus_max_priv0_pictures);
			update_site_option('bp_gallplus_max_priv2_pictures' , $bp_gallplus_max_priv2_pictures);
			update_site_option('bp_gallplus_max_priv3_pictures' , $bp_gallplus_max_priv3_pictures);
			update_site_option('bp_gallplus_max_priv4_pictures' , $bp_gallplus_max_priv4_pictures);
			update_site_option('bp_gallplus_max_priv6_pictures' , $bp_gallplus_max_priv6_pictures);
			
			update_site_option( 'bp_gallplus_max_upload_size', $bp_gallplus_max_upload_size );
			update_site_option( 'bp_gallplus_keep_original', $bp_gallplus_keep_original );
			update_site_option( 'bp_gallplus_require_description', $bp_gallplus_require_description );
			update_site_option( 'bp_gallplus_enable_comments', $bp_gallplus_enable_comments );
			update_site_option( 'bp_gallplus_disable_public_access', $bp_gallplus_disable_public_access );
			update_site_option( 'bp_gallplus_enable_wire', $bp_gallplus_enable_wire );
			update_site_option( 'bp_gallplus_middle_size', $bp_gallplus_middle_size );
			update_site_option( 'bp_gallplus_thumb_size', $bp_gallplus_thumb_size );
			update_site_option( 'bp_gallplus_per_page', $bp_gallplus_per_page );
			update_site_option( 'bp_gallplus_url_remap', $bp_gallplus_url_remap );
			update_site_option( 'bp_gallplus_base_url', $bp_gallplus_base_url );


}
?>