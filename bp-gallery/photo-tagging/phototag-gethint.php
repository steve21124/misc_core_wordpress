<?php
include '../../../../wp-load.php';
global $wpdb, $bp;
$table_name = $wpdb->prefix . "bp_gallplus_tags";
$table_twoname = $wpdb->prefix . "bp_gallplus_album";
$tablethree_name = $wpdb->prefix . "bp_activity";
// check if save or delete
if (!empty($_GET["d"])){
	//delete
	$wpdb->query("DELETE FROM " . $table_name . " WHERE id = '" . $_GET["d"] . "'"); // delete tag
	bp_activity_delete ( array( 'item_id' =>  $_GET["d"] ) ); // delete activity
		bp_core_delete_all_notifications_by_type( $_GET["d"], album, 'user_tagged' ); // delete notification
} else {
	//save - retrieve tag id - add notification & activity post
	$tagfields["id"] = '';
	$tagfields["photo_id"] = $_GET["pid"];
	$tagfields["tagged_id"] = $_GET["fid"];
	$tagfields["tagged_name"] = $_GET["lab"];
	$tagfields["height"] = $_GET["hei"];
	$tagfields["width"] = $_GET["wid"];
	$tagfields["top_pos"] = $_GET["top"];
	$tagfields["left_pos"] = $_GET["left"];
	$photo_owner_id = $_GET["oid"];
	// put info to db
	$savethetag = $wpdb->insert( $table_name, $tagfields );
	$tagid = $wpdb->insert_id;
	// return the tag id
	echo $tagid;	
	/// add notification/activity post to tagged user
	if (!empty($tagfields["tagged_id"])){
		// activity first
	$locSql = "SELECT owner_id, title, description, pic_thumb_url FROM " . $table_twoname. " WHERE id=" . $tagfields["photo_id"];
		$photo = $wpdb->get_results( "SELECT owner_id, title, description, pic_thumb_url FROM " . $table_twoname. " WHERE id=" . $tagfields["photo_id"], ARRAY_A );
		$albumslug = get_site_option( 'bp_gallplus_slug' );
		
		foreach ( $photo as $pic ) {
				$primary_link = bp_core_get_user_domain( $photo_owner_id ) . $albumslug . '/image/' . $tagfields["photo_id"] . '/';
				$title = $pic[title];
				$desc = $pic[description];
				if ( function_exists( 'mb_strlen' ) ) {
					$title = ( mb_strlen($title)<= 20 ) ? $title : mb_substr($title, 0 ,20-1).'&#8230;';
					$desc = ( mb_strlen($desc)<= 400 ) ? $desc : mb_substr($desc, 0 ,400-1).'&#8230;';
				} 
				else {
					$title = ( strlen($title)<= 20 ) ? $title : substr($title, 0 ,20-1).'&#8230;';
					$desc = ( strlen($desc)<= 400 ) ? $desc : substr($desc, 0 ,400-1).'&#8230;';
				}
				$action = bp_core_get_userlink($tagfields["tagged_id"]) . ' was tagged in the photo: <a href="' . $primary_link . '">' . $title . '</a>';
				$content = '<p> <a href="'. $primary_link .'" class="image-activity-thumb" title="'.$title.'"><img src="' . $bp->root_domain . $pic[pic_thumb_url] .'" /></a>'.$desc.'</p>';
				$type = 'photo_tag';
				$secondary_item_id = $tagfields["photo_id"];
				bp_activity_add( array( 'user_id' => $tagfields["tagged_id"], 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => 'album', 'type' => $type, 'item_id' => $tagid, 'secondary_item_id' => $secondary_item_id ) );
		}
		// Nitification second
		if ( $tagfields["tagged_id"] != $bp->loggedin_user->id ) {
			bp_core_add_notification( $tagid, $tagfields["tagged_id"], 'album', 'user_tagged', $tagfields["photo_id"] ); // notification call
		}
	}
} ?>