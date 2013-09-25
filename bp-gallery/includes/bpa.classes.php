<?php

/**
 * bp-galleries-plus DATABASE CLASS
 * Handles database functionality for the plugin
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 * @package bp-galleries-plus
 * @subpackage Database
 * @license GPL v2.0
 * @link http://code.google.com/p/buddypress-media/wiki/DOCS_BPM_db_top
 *
 * ========================================================================================================
 */

class BP_Gallplus_Image {
    
	var $id;
	var $owner_type;
	var $owner_id;
	var $date_uploaded;
	var $title;
	var $description;
	var $privacy;
	var $pic_org_url;
	var $pic_org_path;
	var $pic_mid_url;
	var $pic_mid_path;
	var $pic_thumb_url;
	var $pic_thumb_path;
	var $album_id;
	var	$like_count;
	var $feature_image;
	var $group_id;
	var $media_type;

	/**
	 * bp_gallplus_image()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function BP_Gallplus_Image( $id = null ) {
		$this->__construct( $id );
	}
	
	function __construct( $id = null ) {
		global $wpdb, $bp;	
		
		if ( $id ) {	
			$this->populate( $id );
		}
	}
	
	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function populate($id) {
		global $wpdb,$bp;
		
		$sql = $wpdb->prepare( "SELECT * FROM {$bp->album->table_name} WHERE id = %d", $id );
		$image = $wpdb->get_row( $sql );
		
		if ( $image ) {
			$this->owner_type = $image->owner_type;
			$this->owner_id = $image->owner_id;
			$this->id = $image->id;
	        $this->date_uploaded = $image->date_uploaded;
	        $this->title = $image->title;
	        $this->description = $image->description;
	        $this->privacy = $image->privacy;
	        $this->pic_org_path = $image->pic_org_path;
	        $this->pic_org_url = $image->pic_org_url;
	        $this->pic_mid_path = $image->pic_mid_path;
	        $this->pic_mid_url = $image->pic_mid_url;
	        $this->pic_thumb_path = $image->pic_thumb_path;
	        $this->pic_thumb_url = $image->pic_thumb_url;
	        $this->album_id = $image->album_id;
	        $this->like_count = $image->like_count;
	        $this->feature_image = $image->feature_image;
	        $this->group_id = $image->group_id;
	        $this->media_type = $image->media_type;
		}
	}
	
	/**
	 * save()
	 *
	 * This method will save an object to the database. It will dynamically switch between
	 * INSERT and UPDATE depending on whether or not the object already exists in the database.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function save() {
	    
		global $wpdb, $bp;
		
		$this->title = apply_filters( 'bp_gallplus_title_before_save', $this->title );
		$this->description = apply_filters( 'bp_gallplus_description_before_save', $this->description, $this->id );
		
		do_action( 'bp_gallplus_data_before_save', $this );

		if ( !$this->owner_id)
		{		    
			return false;
		}
		$this->title = esc_attr( strip_tags($this->title) );
		$this->description = wp_filter_kses($this->description);

        if ( $this->id ) {
			$sql = $wpdb->prepare(
				"UPDATE {$bp->album->table_name} SET
					owner_type = %s,
					owner_id = %d,
					date_uploaded = %s,
					title = %s,
					description = %s,
					privacy = %d,
					pic_org_url = %s,
					pic_org_path =%s,
					pic_mid_url = %s,
					pic_mid_path =%s,
					pic_thumb_url = %s,
					pic_thumb_path =%s,
					album_id =%s,
					like_count = %d,
					feature_image = %d,
					group_id = %d,
					media_type = %d
				WHERE id = %d",
					$this->owner_type,
					$this->owner_id,
					$this->date_uploaded,
					$this->title,
					$this->description,
					$this->privacy,
					$this->pic_org_url,
					$this->pic_org_path,
					$this->pic_mid_url,
					$this->pic_mid_path,
					$this->pic_thumb_url,
					$this->pic_thumb_path,
					$this->album_id,
					$this->like_count,
					$this->feature_image,
					$this->group_id,
					$this->media_type,
					$this->id
				);
		} 
		else {
			$sql = $wpdb->prepare(
					"INSERT INTO {$bp->album->table_name} (
						owner_type,
						owner_id,
						date_uploaded,
						title,
						description,
						privacy,
						pic_org_url,
						pic_org_path,
						pic_mid_url,
						pic_mid_path,
						pic_thumb_url,
						pic_thumb_path,
						album_id,
						like_count,
						feature_image,
						group_id,
						media_type
					) VALUES (
						%s, %d, %s, %s, %s, %d, %s, %s, %s, %s, %s, %s, %d, %d, %d, %d, %d)",
						$this->owner_type,
						$this->owner_id,
						$this->date_uploaded,
						$this->title,
						$this->description,
						$this->privacy,
						$this->pic_org_url,
						$this->pic_org_path,
						$this->pic_mid_url,
						$this->pic_mid_path,
						$this->pic_thumb_url,
						$this->pic_thumb_path,
						$this->album_id,
						$this->like_count,
						$this->feature_image,
						$this->group_id,
						$this->media_type
					);
		}
		$result = $wpdb->query( $sql );
	    
		if ( !$result )
			return false;
		
		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}	
		
		do_action( 'bp_gallplus_data_after_save', $this ); 
		
		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the corresponding row for an object from the database.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */	
	function delete() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->album->table_name} WHERE id = %d", $this->id ) );
	}
	
		function query_group_image($group_id,$privacy = 5){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id, title FROM {$bp->album->table_name} WHERE group_id = %d AND privacy = %d",$group_id, $privacy) ;
//bp_logdebug('query_group_image : '.$sql);
			$result = $wpdb->get_results( $sql );
			return $result;
	}
	
	public static function query_images($args = '',$count=false,$adjacent=false) {
	    
		global $bp, $wpdb;

		$defaults = bp_gallplus_default_query_args();
		
		$r = apply_filters('bp_gallplus_query_args',wp_parse_args( $args, $defaults ));

		extract( $r , EXTR_SKIP);
		
		$where = "1 = 1";
		
		if ($owner_id){
			$where .= $wpdb->prepare(' AND owner_id = %d',$owner_id);	
		}
		if ($id && $adjacent != 'next' && $adjacent != 'prev' && !$count){
			$where .= $wpdb->prepare(' AND id = %d',$id);
		}
		if ($album_id){
			$where .= $wpdb->prepare(' AND album_id = %d',$album_id);	
		}
	
		switch ( $privacy ) {
			case 'public':
			case 0 === $privacy:
			case '0':
				$where .= " AND privacy = 0";
				break;
				
			case 'members':
			case 2:
				if (bp_gallplus_privacy_level_permitted()>=2 || $priv_override)
					$where .= " AND privacy = 2";
				else
					return $count ? 0 : array();
				break;
			case 'groups':
			case 3:
				if (bp_gallplus_privacy_level_permitted()>=3 || $priv_override)
					$where .= " AND privacy = 3";
				else
					return $count ? 0 : array();
				break;				
			case 'friends':
			case 4:
				if (bp_gallplus_privacy_level_permitted()>=4 || $priv_override)
					$where .= " AND privacy = 4";
				else
					return $count ? 0 : array();
				break;
			case 'group_gallery':
			case 5:	
					$where .= " AND privacy = 5";
					break;
			case 'private':
			case 6:
				if (bp_gallplus_privacy_level_permitted()>=6 || $priv_override)
					$where .= " AND privacy = 6";
				else
					return $count ? 0 : array();
				break;
				
			case 'admin':
			case 10:
				if (bp_gallplus_privacy_level_permitted()>=10 || $priv_override)
					$where .= " AND privacy = 10";
				else
					return $count ? 0 : array();
				break;
				
			case 'all':
				if ( $priv_override )
					break;
				
			case 'permitted':
			default:
				$where .= " AND privacy <= ".bp_gallplus_privacy_level_permitted();
				break;
		}
		if(!$count){	
		$order = "";	
		$limits = "";
			if($adjacent == 'next'){
				$where .= $wpdb->prepare(' AND id > %d',$id);
				$order = "ORDER BY id ASC";
				$limits = "LIMIT 0, 1";
			}
			elseif($adjacent == 'prev'){
				$where .= $wpdb->prepare(' AND id < %d',$id);
				$order = "ORDER BY id DESC";
				$limits = "LIMIT 0, 1";
			}
			elseif(!$id){

				if ($orderkey != 'id' && $orderkey != 'user_id' && $orderkey != 'status' && $orderkey != 'random'&& $orderkey != 'date_created'&& $orderkey != 'date_updated') {
				    $orderkey = 'id';
				}

				if ($ordersort != 'ASC' && $ordersort != 'DESC') {
				    $ordersort = 'DESC';
				}

				if($orderkey == 'random'){
				    $order = "ORDER BY RAND() $ordersort";
				}
				else {
				    $order = "ORDER BY $orderkey $ordersort";
				}

				if ($per_page){
					if ( empty($offset) ) {
						$limits = $wpdb->prepare('LIMIT %d, %d', ($page-1)*$per_page , $per_page);
					} 
					else { // We're ignoring $page and using 'offset'
						$limits = $wpdb->prepare('LIMIT %d, %d', $offset , $per_page);
					}
				}
			}
			
			$sql = "SELECT * FROM ".$bp->album->table_name." WHERE ".$where." ".$order." ".$limits;
//			bp_logdebug("query_images : ".$sql);
			$result = $wpdb->get_results( $sql );
			// We need to any images that belong to a group the member is not a member of
			if(!is_super_admin())
			{
				$resultCount = count($result);
				for($i=0; $i<$resultCount; $i++)
				{
					if(($result[$i]->privacy == 3) || ($result[$i]->privacy == 5))
					{
						$group = new BP_Groups_Group( $result[$i]->group_id );
 						if (!bp_group_is_member($group)) {
 						
	 						unset($result[$i]);	
						}
					}
				}
				$result = array_values($result);			
			}
			
		} 
		else {
			$select='';
			$group='';
			if ($groupby=='privacy'){
				$select='privacy,';
				$group='GROUP BY privacy';
			}
			
			$sql =  "SELECT DISTINCT ".$select." COUNT(id) AS count FROM ".$bp->album->table_name." WHERE ".$where." ".$group;
//			bp_logdebug("query_images  sql : $sql ");
			if ($group)
				$result = $wpdb->get_results( $sql );
			else
				$result = $wpdb->get_var( $sql );
		}
//			bp_logdebug("query_images : result ".print_r($result,true));

		return $result;	
	}

	public static function delete_by_owner($owner_id,$owner_type ) {

		global $bp, $wpdb;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->album->table_name} WHERE owner_type = %d AND owner_id = %d ", $owner_type, $owner_id ) );
	}

	public static function delete_by_user_id($user_id) {
	    
		return BP_Gallplus_Image::delete_by_owner($user_id,'user');
	}
	
} /** End of BP_Gallplus_Image */

class BP_Gallplus_Album {
    
	var $id;
	var $owner_type;
	var $owner_id;
	var $date_created;
	var $date_updated;
	var $title;
	var $description;
	var $privacy;
	var $album_org_url;
	var $feature_image_path;
	var $feature_image;
	var $like_count;
	var $group_id;
	var $spare2;
	var $spare3;
	var $spare4;

	/**
	 * bp_gallplus_album()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function BP_Gallplus_Album( $id = null ) {
		
		$this->__construct( $id );
	}
	
	function __construct( $id = null ) {
		global $wpdb, $bp;	
		
		if ( $id ) {	
			$this->populate( $id );
		}
	}
	
	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function populate($id) {
		global $wpdb,$bp;
		
		$sql = $wpdb->prepare( "SELECT * FROM {$bp->album->albums_table_name} WHERE id = %d", $id );
		$album = $wpdb->get_row( $sql );
		if ( $album ) {
			$this->owner_type = $album->owner_type;
			$this->owner_id = $album->owner_id;
			$this->id = $album->id;
	    $this->date_created = $album->date_created;
	    $this->date_updated = $album->date_updated;
	    $this->title = $album->title;
	    $this->description = $album->description;
	    $this->privacy = $album->privacy;
	    $this->album_org_path = $album->album_org_path;
	    $this->feature_image_path = $album->feature_image_path;
			$this->feature_image = $album->feature_image;
			$this->like_count = $album->like_count;
	    $this->group_id = $album->group_id;
	    $this->spare2 = $album->spare2;
	    $this->spare3 = $album->spare3;
	    $this->spare4 = $album->spare4;
		}
	}
	
	/**
	 * save()
	 *
	 * This method will save an object to the database. It will dynamically switch between
	 * INSERT and UPDATE depending on whether or not the object already exists in the database.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
	function save() {
	    
		global $wpdb, $bp;
	
		$this->title = apply_filters( 'bp_gallplus_title_before_save', $this->title );
		$this->description = apply_filters( 'bp_gallplus_description_before_save', $this->description, $this->id );
		
//		do_action( 'bp_gallplus_data_before_save', $this );

		if ( !$this->owner_id)
		{
		    return false;
		}

		$this->title = esc_attr( strip_tags($this->title) );
		$this->description = wp_filter_kses($this->description);

        if ( $this->id ) {
			$sql = $wpdb->prepare(
				"UPDATE {$bp->album->albums_table_name} SET
					owner_type = %s,
					owner_id = %d,
					date_created = %s,
					date_updated = %s,
					title = %s,
					description = %s,
					privacy = %d,
					album_org_url = %s,
					feature_image_path =%s,
					feature_image =%d,
					like_count =%d,
					group_id = %d,
					spare2 =%s,
					spare3 = %s,
					spare4 =%s
				WHERE id = %d",
					$this->owner_type,
					$this->owner_id,
					$this->date_created,
					$this->date_updated,
					$this->title,
					$this->description,
					$this->privacy,
					$this->album_org_url,
					$this->feature_image_path,
					$this->feature_image,
					$this->like_count,
					$this->group_id,
					$this->spare2,
					$this->spare3,
					$this->spare4,
					$this->id
				);
		} 
		else {

			$sql = $wpdb->prepare(
					"INSERT INTO {$bp->album->albums_table_name} (
						owner_type,
						owner_id,
						date_created,
						date_updated,
						title,
						description,
						privacy,
						album_org_url,
						feature_image_path,
						feature_image,
						like_count,
						group_id,
						spare2,
						spare3,
						spare4
					) VALUES (
						%s, %d, %s, %s, %s, %s, %d, %s, %s, %d, %d, %d, %s, %s, %s)",
						$this->owner_type,
						$this->owner_id,
						$this->date_created,
						$this->date_created,
						$this->title,
						$this->description,
						$this->privacy,
						$this->album_org_url,
						$this->feature_image_path,
						$this->feature_image,
						$this->like_count,
						$this->group_id,
						$this->spare2,
						$this->spare3,
						$this->spare4
					);
		}
		
		$result = $wpdb->query( $sql );
	    
		if ( !$result )
		{
			return false;
		}
		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}	
		
		do_action( 'bp_gallplus_data_after_save', $this ); 
		
		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the album from the albums table.
	 * 
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */	
	function delete() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->album->albums_table_name} WHERE id = %d", $this->id ) );
	}
	
	function find_album($theOwnerId,$theTitle){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id FROM {$bp->album->albums_table_name} WHERE owner_id = %d AND title = %s",$theOwnerId, $theTitle) ;
			$result = $wpdb->get_var( $sql );
			return $result;
	}
	
	function query_album(){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id FROM {$bp->album->albums_table_name} WHERE owner_id = %d AND title = %s",$this->owner_id, $this->title) ;
			$result = $wpdb->get_var( $sql );
			return $result;
	}
		function query_album_names(){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id, title FROM {$bp->album->albums_table_name} WHERE owner_id = %d AND owner_type != 'group'",$this->owner_id) ;
			$result = $wpdb->get_results( $sql );
			return $result;
	}
		function query_album_image_ids(){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id FROM {$bp->album->table_name} WHERE album_id = %d AND owner_id = %d",$this->id,$this->owner_id) ;
			$result = $wpdb->get_results( $sql );
			return $result;
	}
		function query_group_album($group_id,$privacy = 5){
		global $wpdb, $bp;
			$sql = $wpdb->prepare( "SELECT id, title FROM {$bp->album->albums_table_name} WHERE group_id = %d AND privacy = %d and owner_type = 'group'",$group_id, $privacy) ;
			$result = $wpdb->get_results( $sql );
			return $result;
	}

	public static function query_images($args = '',$count=false,$adjacent=false) {
	    
		global $bp, $wpdb;

		$defaults = bp_gallplus_default_query_args();
		
		$r = apply_filters('bp_gallplus_query_args',wp_parse_args( $args, $defaults ));

		extract( $r , EXTR_SKIP);
		
		$where = "1 = 1";
		
		if ($owner_id){
			$where .= $wpdb->prepare(' AND owner_id = %d',$owner_id);	
		}
//		if ($album_id){
//			$where .= $wpdb->prepare(' AND album_id = %d',$album_id);	
//		}
		if ($id && $adjacent != 'next' && $adjacent != 'prev' && !$count){
			$where .= $wpdb->prepare(' AND id = %d',$id);
		}
		if($owner_type)
		{
			$where .= $wpdb->prepare(' AND owner_type = %s',$owner_type);
		}
		switch ( $privacy ) {
			case 'public':
			case 0 === $privacy:
			case '0':
				$where .= " AND privacy = 0";
				break;
				
			case 'members':
			case 2:
				if (bp_gallplus_privacy_level_permitted()>=2 || $priv_override)
					$where .= " AND privacy = 2";
				else
					return $count ? 0 : array();
				break;
			case 'groups':
			case 3:
				if (bp_gallplus_privacy_level_permitted()>=3 || $priv_override)
					$where .= " AND privacy = 3";
				else
					return $count ? 0 : array();
				break;
				
			case 'friends':
			case 4:
				if (bp_gallplus_privacy_level_permitted()>=4 || $priv_override)
					$where .= " AND privacy = 4";
				else
					return $count ? 0 : array();
				break;
			case 'group_gallery':
			case 5:	
					$where .= " AND privacy = 5";
					break;
				
			case 'private':
			case 6:
				if (bp_gallplus_privacy_level_permitted()>=6 || $priv_override)
					$where .= " AND privacy = 6";
				else
					return $count ? 0 : array();
				break;
				
			case 'admin':
			case 10:
				if (bp_gallplus_privacy_level_permitted()>=10 || $priv_override)
					$where .= " AND privacy = 10";
				else
					return $count ? 0 : array();
				break;
				
			case 'all':
				if ( $priv_override )
					break;
				
			case 'permitted':
			default:
				$where .= " AND privacy <= ".bp_gallplus_privacy_level_permitted();
				break;
		}
		if(!$count){	
		$order = "";	
		$limits = "";
			if($adjacent == 'next'){
				$where .= $wpdb->prepare(' AND id > %d',$id);
				$order = "ORDER BY id ASC";
				$limits = "LIMIT 0, 1";
			}
			elseif($adjacent == 'prev'){
				$where .= $wpdb->prepare(' AND id < %d',$id);
				$order = "ORDER BY id DESC";
				$limits = "LIMIT 0, 1";
			}
			elseif(!$id){

				if ($orderkey != 'id' && $orderkey != 'user_id' && $orderkey != 'status' && $orderkey != 'random' && $orderkey != 'date_updated' &&  $orderkey != 'date_created' && $orderkey != 'like_count') {
				    $orderkey = 'id';
				}

				if ($ordersort != 'ASC' && $ordersort != 'DESC') {
				    $ordersort = 'DESC';
				}

				if($orderkey == 'random'){
				    $order = "ORDER BY RAND() $ordersort";
				}
				else {
				    $order = "ORDER BY $orderkey $ordersort";
				}

				if ($per_page){
					if ( empty($offset) ) {
						$limits = $wpdb->prepare('LIMIT %d, %d', ($page-1)*$per_page , $per_page);
					} 
					else { // We're ignoring $page and using 'offset'
						$limits = $wpdb->prepare('LIMIT %d, %d', $offset , $per_page);
					}
				}
			}
			
			$sql = "SELECT * FROM ".$bp->album->albums_table_name." WHERE ".$where." ".$order." ".$limits;
//			bp_logdebug(" album query_images : ".$sql);
			$result = $wpdb->get_results( $sql );
			// We need to any albums that belong to a group the member is not a member of
			if(!is_super_admin())
			{
				$resultCount = count($result);
				for($i=0; $i<$resultCount; $i++)
				{
					if(($result[$i]->privacy == 3) || ($result[$i]->privacy == 5)) 
					{
						$group = new BP_Groups_Group( $result[$i]->group_id );
 						if (!bp_group_is_member($group)) {
 							unset($result[$i]);	
						}
					}
				}
				$result = array_values($result);			
			}
		} 
		else {
			$select='';
			$group='';
			if ($groupby=='privacy'){
				$select='privacy,';
				$group='GROUP BY privacy';
			}
			
		//	$sql =  $wpdb->prepare( "SELECT DISTINCT $select COUNT(id) AS count FROM {$bp->album->albums_table_name} WHERE $where $group") ;
		//	$sql =  $wpdb->prepare( "SELECT DISTINCT %s COUNT(id) AS count FROM {$bp->album->albums_table_name} WHERE %s %s",$select,$where,$group) ;
			$sql =  "SELECT DISTINCT ".$select." COUNT(id) AS count FROM ".$bp->album->albums_table_name." WHERE ".$where." ".$group;
//			$sql =  $wpdb->prepare($sqlStr) ;
			if ($group)
				$result = $wpdb->get_results( $sql );
			else
				$result = $wpdb->get_var( $sql );
		}
		
		return $result;	
	}

	public static function delete_by_owner($owner_id,$owner_type ) {

		global $bp, $wpdb;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->album->albums_table_name} WHERE owner_type = %d AND owner_id = %d ", $owner_type, $owner_id ) );
	}

	public static function delete_by_user_id($user_id) {
	    
		return BP_Gallplus_Album::delete_by_owner($user_id,'user');
	}
	
}

	/**
	 * bp_gallplus_default_query_args()
	 *
	 * @version 0.1.8.11
	 * @since 0.1.8.0
	 */
function bp_gallplus_default_query_args(){
    
	global $bp;
	$args = array();
	
	$args['owner_id'] = $bp->displayed_user->id ? $bp->displayed_user->id : false;
	$args['id'] = false;
	$args['owner_type'] = false;
	$args['page']=1;
	$args['per_page']=$bp->album->bp_gallplus_per_page;
	$args['max']=false;
	$args['privacy']='permitted';
	$args['priv_override']=false;
	$args['ordersort']='ASC';
	$args['orderkey']='id';
	$args['groupby']=false;
	
/*	if($bp->album->album_slug == $bp->current_action){

		if( isset($bp->action_variables[0]) ){
			$args['id'] = (int)$bp->action_variables[0];
		}
		else {
			$args['id'] = false;
		}
			
//		$args['per_page']=1;
	}*/
	if($bp->album->images_slug == $bp->current_action){
		$args['page'] = ( isset($bp->action_variables[0]) && (string)(int) $bp->action_variables[0] === (string) $bp->action_variables[0] ) ? (int) $bp->action_variables[0] : 1 ;
	}

	return $args;
}

?>