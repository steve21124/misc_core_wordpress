<?php 

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * bp_gallplus_screen_image()
 *
 * Single image
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_gallplus_screen_single() {

	global $bp,$images_template;

	if ( $bp->current_component == $bp->album->slug && $bp->album->single_edit_slug == $bp->current_action && $images_template->image_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  ) {
	
		do_action( 'bp_gallplus_screen_edit' );

		add_action( 'bp_template_title', 'bp_gallplus_screen_edit_title' );
		add_action( 'bp_template_content', 'bp_gallplus_screen_edit_content' );
	
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		
		return;
	}
	
	do_action( 'bp_gallplus_screen_single' );

	bp_gallplus_query_images();
	bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_single', 'album/single' ) );
}
/**
 * bp_gallplus_show_albums()
 *
 * Single image
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_gallplus_show_albums() {

	global $bp,$albums_template;
	if(!isset($albums_template))
	{
		bp_gallplus_query_albums();
	}		

	if ( $bp->current_component == $bp->album->slug && (($bp->album->album_slug == $bp->current_action) || ($bp->album->albums_slug == $bp->current_action)) && $albums_template->album_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  ) {
		if((bp_is_my_profile()|| is_super_admin()))
		{
			do_action( 'bp_gallplus_screen_edit' );

			add_action( 'bp_template_content', 'bp_gallplus_screen_edit_album_content' );
//		add_action( 'bp_template_content', 'bp_gallplus_screen_edit_content' );
	
			bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
		
			return;
		}
	}
	
	do_action( 'bp_gallplus_show_albums' );
	if(isset($bp->action_variables[0]))
	{
		$locAlbumID = $bp->action_variables[0];
		$album = new BP_Gallplus_Album( $locAlbumID );
			// Now need to retrieve the privacy and group level
			if((($album->owner_type == 'admin') && is_super_admin()) || ($album->owner_type == 'group'))
			{
					$album_args = array(
								'album_id' => $locAlbumID,
								'privacy'=>'group_gallery');
			}
			else
			{
				$album_args = array(
								'album_id' => $locAlbumID);
			}
//		$album_args = array(
//								'album_id' => $locAlbumID);
		if(isset($bp->action_variables[1]))
		{
			$locPage = $bp->action_variables[1];
			$album_args['page'] = $locPage;
		}
		bp_gallplus_query_images($album_args);
		bp_gallplus_query_albums();
		bp_gallplus_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_images', 'album/albumcontent' ) );
	}
	else
	{
		bp_gallplus_query_albums();
		bp_core_load_template( apply_filters( 'bp_gallplus_template_album', 'album/albums' ) );
	}
}

/**
 * bp_gallplus_screen_edit_title()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_gallplus_screen_edit_title() {
	_e( 'Edit Photo', 'bp-galleries-plus' );
}

/**
 * bp_gallplus_screen_edit_content()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */ 
function bp_gallplus_screen_edit_content() {

	global $bp,$images_template;
		$locAlbumID = $bp->action_variables[0];
		$image_args = array(
								'id' => $locAlbumID);
		bp_gallplus_query_images($image_args);

	if (bp_gallplus_has_images() ) :  
		if(isset($bp->action_variables[0]))
		{
	   bp_gallplus_the_image($bp->action_variables[0]);
	  }
	  else
		{
	   bp_gallplus_the_image();
	  }
	  $album_id = $bp->action_variables[2];
	$limit_info = bp_gallplus_limits_info();
	if($bp->bp_gallplus_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-galleries-plus'),
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	?>
	<h4><?php _e( 'Edit Photo', 'bp-galleries-plus' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-galleries-plus-edit-form" id="bp-galleries-plus-edit-form" class="standard-form">

    <img id="image-edit-thumb" src='<?php bp_gallplus_image_middle_url() ?>' />
		<?php // JLL_MOD - adds face-tagging
        global $wpdb, $bp;
        bp_core_delete_notifications_for_user_by_item_id( $bp->loggedin_user->id, bp_gallplus_get_image_id(), album, 'user_tagged' );
            //populate autofill
            $table_name = $wpdb->prefix . "bp_friends";
            $currentuserid = $bp->loggedin_user->id;
            $currentusername = $bp->loggedin_user->fullname;
            $friends = $wpdb->get_results( "SELECT friend_user_id, initiator_user_id FROM " . $table_name. " WHERE is_confirmed=1 AND (friend_user_id=" . $currentuserid  . " OR initiator_user_id=" . $currentuserid . ")", ARRAY_A );
            for($i=0; $i<count($friends); $i++)
            {
                $singlefriend = $friends[$i];
                if ( $currentuserid == $singlefriend[initiator_user_id] ) {
                    $friendids[] = $singlefriend[friend_user_id];
                    $friendnames[] = bp_core_get_user_displayname( $singlefriend[friend_user_id] );
                } else {
                    $friendids[] = $singlefriend[initiator_user_id];
                    $friendnames[] = bp_core_get_user_displayname( $singlefriend[initiator_user_id] );
                }		
            }
            // populate default tags
            $thepic = bp_gallplus_get_image_id();
            $table_tags = $wpdb->prefix . "bp_gallplus_tags";
            $default_tags = $wpdb->get_results( "SELECT * FROM " . $table_tags. " WHERE photo_id=" . $thepic, ARRAY_A );
            for($i=0; $i<count($default_tags); $i++)
            {
                $singletag = $default_tags[$i];
                $d_tags_id[] = $singletag[id];
                $d_tags_tagged_id[] = $singletag[tagged_id];
                $d_tags_tagged_name[] = $singletag[tagged_name];
                $d_tags_height[] = $singletag[height];
                $d_tags_width[] = $singletag[width];
                $d_tags_top[] = $singletag[top_pos];
                $d_tags_left[] = $singletag[left_pos];
            }
        ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#image-edit-thumb").tag({
                    autoComplete: [ <?php // set autofill
                        echo '{ value: "' . $currentusername . '", label: "' . $currentusername . '", fid: "' . $currentuserid  . '", plink: "urlurlurlurl" },';
                        for ( $i = 0; $i < count( $friends ); $i++ ) {
                        echo '{ value: "' . $friendnames[$i] . '", label: "' . $friendnames[$i] . '", fid: "' . $friendids[$i] . '", plink: "urlurlurlurl" }, ';
                        } ?>
                        ],
                    defaultTags: [ <?php // set default tags
                        for ( $i = 0; $i < count( $default_tags ); $i++ ) {
                        echo '{ id: "' . $d_tags_id[$i] . '", label: "' . $d_tags_tagged_name[$i] . '", fid: "' . $d_tags_tagged_id[$i] . '", height: "' . $d_tags_height[$i] . '", width: "' . $d_tags_width[$i] . '", top: "' . $d_tags_top[$i] . '", left: "' . $d_tags_left[$i] . '", plink: "' . bp_core_get_user_domain($d_tags_tagged_id[$i]) . '" }, ';
                        } ?>	  
                        ],
                    save : function( width,height,top_pos,left,label,the_tag,fid ){
                                    var pid = "<?php bp_gallplus_image_id(); ?>";
                                    var fid = $("#fid").html(); 
                                    var oid = "<?php echo $bp->displayed_user->id; ?>";
                                    if (window.XMLHttpRequest)
                                      {// code for IE7+, Firefox, Chrome, Opera, Safari
                                      xmlhttp=new XMLHttpRequest();
                                      }
                                    else
                                      {// code for IE6, IE5
                                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                                      }
                                    xmlhttp.onreadystatechange=function()
                                      {
                                      if (xmlhttp.readyState==4 && xmlhttp.status==200)
                                        {
                                            var tagsetid = xmlhttp.responseText;
                                            the_tag.setId(tagsetid); // set tag id from db
                                            $("#fid").html("");
                                            $("#enable").html("Tag this photo");
                                            $("#enable").attr('class','tag-button');
                                            $("div.image-middle").find('a').attr('id','');
                                        }
                                      }
                                    xmlhttp.open("GET","<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/bp-gallery/photo-tagging/phototag-gethint.php?wid="+width+"&hei="+height+"&top="+top_pos+"&left="+left+"&lab="+label+"&pid="+pid+"&fid="+fid+"&oid="+oid,true);
                                    xmlhttp.send();
                                },
                    remove: function(id){
                                    //alert('Here I can do some ajax to delete tag #'+id+' in my db');
                                    if (window.XMLHttpRequest)
                                      {// code for IE7+, Firefox, Chrome, Opera, Safari
                                      xmlhttp=new XMLHttpRequest();
                                      }
                                    else
                                      {// code for IE6, IE5
                                      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                                      }
                                    xmlhttp.open("GET","<?php global $bp; echo $bp->root_domain; ?>/wp-content/plugins/bp-gallery/photo-tagging/phototag-gethint.php?d="+id+"&pid=<?php bp_gallplus_image_id(); ?>",true);
                                    xmlhttp.send();
                                },
                });
                $("#enable").click(function(){
                            $("#image-edit-thumb").parent().mousedown(function(e){
                                $("#image-edit-thumb").showDrag(e);
                                $("#image-edit-thumb").parent().unbind('mousedown');
                            });
                            $("#enable").html("Click on the photo to add a tag...");
                            $("#enable").attr('class','');
                });
            });
        </script>
        <div id="fid" style="display:none;"></div><div id="uid" style="display:none;"><?php echo $bp->loggedin_user->id; ?></div><div id="did" style="display:none;"><?php echo $bp->displayed_user->id; ?></div><div id="pid" style="display:none;"><?php bp_gallplus_image_id(); ?></div>
        
     <?php if ( $bp->loggedin_user->id ) { ?>
     <div id="enable" class="tag-button">Tag this photo</div> 
     <?php } ?>




    <p>
  <input type="hidden" name="image_id" value="<?php bp_gallplus_image_id() ?>" />
  <input type="hidden" name="album_id" value="<?php echo $album_id ?>" />

	<label><?php _e('Photo Title *', 'bp-galleries-plus' ) ?><br />
	<input type="text" name="title" id="image-title" size="100" value="<?php
		echo (empty($_POST['title'])) ? bp_gallplus_get_image_title() : wp_filter_kses($_POST['title']);
	?>"/></label>
    </p>
    <p>
	<label><?php _e('Photo Description', 'bp-galleries-plus' ) ?><br />
	<textarea name="description" id="image-description" rows="15"cols="40" ><?php
		echo (empty($_POST['description'])) ? bp_gallplus_get_image_desc() : wp_filter_kses($_POST['description']);
	?></textarea></label>
    </p>
		<?php bp_gallplus_screen_visibility(bp_gallplus_get_image_group_id(),true); ?>
    
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-galleries-plus' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-galleries-plus-edit' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( "Either this url is not valid or you can't edit this image.", 'bp-galleries-plus' ) ?></p>
	<?php endif;
}

/**
 * bp_gallplus_screen_images()
 *
 * An album page
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_screen_images() {

	do_action( 'bp_gallplus_screen_images' );
	if(isset($bp->action_variables[0]))
	{
		$locAlbumID = $bp->action_variables[0];
		$album = new BP_Gallplus_Album( $locAlbumID );
			// Now need to retrieve the privacy and group level
			if((($album->owner_type == 'admin') && is_super_admin()) || ($album->owner_type == 'group'))
			{
					$album_args = array(
								'album_id' => $locAlbumID,
								'privacy'=>'group_gallery');
			}
			else
			{
				$album_args = array(
								'album_id' => $locAlbumID);
			}
		bp_gallplus_query_images($album_args);
//		bp_gallplus_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_images', 'album/albumcontent' ) );
	}		
	else if(isset($bp->action_variables[1]))
	{
		$locAlbumID = $bp->action_variables[1];
		$album_args = array(
								'album_id' => $locAlbumID);
		bp_gallplus_query_images($album_args);
		bp_gallplus_query_albums();
		bp_gallplus_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_images', 'album/albumcontent' ) );
	}
	else if(isset($_GET['album_id']))
	{
		$locAlbumID = $_GET['album_id'];
		$album_args = array(
								'album_id' => $locAlbumID);
		bp_gallplus_query_images($album_args);
		bp_gallplus_query_albums();
		bp_gallplus_the_album($locAlbumID);
		bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_images', 'album/albumcontent' ) );
	}
	else
	{
		bp_gallplus_query_images();
		bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_images', 'album/images' ) );
	}
}
function bp_gallplus_screen_album() {

	do_action( 'bp_gallplus_screen_album' );

	bp_gallplus_query_albums();
	bp_core_load_template( apply_filters( 'bp_gallplus_template_screen_albums', 'album/images' ) );
}

/**
 * bp_gallplus_screen_upload()
 *
 * Sets up and displays the screen output for the sub nav item "example/screen-two"
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_screen_upload() {
    
	global $bp;

	do_action( 'bp_gallplus_screen_upload' );

	add_action( 'bp_template_content', 'bp_gallplus_screen_upload_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * bp_gallplus_screen_upload_title()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_screen_upload_title() {
	_e( 'Upload new photos', 'bp-galleries-plus' );
}

/**
 * bp_gallplus_screen_upload_content()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_screen_upload_content() {

	global $bp;

	$limit_info = bp_gallplus_limits_info();

	if($bp->bp_gallplus_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members can view','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			5 => __('Group Members can view and add','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-galleries-plus'),
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members can view','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			5 => __('Group Members can view and add','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	$owner_id = $bp->loggedin_user->id;
	$loc_album = new BP_Gallplus_Album();
	$loc_album->owner_id = $owner_id; 
	$loc_album->owner_type = 'user';

	$albums = $loc_album->query_album_names();
	$groupAlbums =  bp_gallplus_member_groups($bp->loggedin_user->id);
	if( ($limit_info['all']['enabled'] == true) && ($limit_info['all']['remaining'] > 0) ):?>

	<h4><?php _e( 'Upload new photos', 'bp-galleries-plus' ) ?></h4>
	<!-- form>
		<div id="queue"></div>
		<input id="file_upload" name="file_upload" type="file" multiple="true">
		<a style="position: relative; top: 8px;" href="javascript:$('#file_upload').uploadifive('upload')">Upload Files</a>
	</form -->

	<form method="post" enctype="multipart/form-data" name="bp-galleries-plus-upload-form" id="bp-galleries-plus-upload-form" class="standard-form">

    <input type="hidden" name="upload" value="<?php echo $bp->album->bp_gallplus_max_upload_size; ?>" />
    <input type="hidden" name="action" value="image_upload" />

 	<?php 
 	if($albums)
	{
		echo "\n<p><label>";
					_e('Select Album', 'bp-galleries-plus' );
					echo "\n".'	<SELECT NAME="selected_album" id="selected_album">'."\n";
					echo "<OPTION VALUE=0></OPTION>\n";
					foreach( $albums as $album)
					{
						echo '<OPTION VALUE="'.$album->id.'">'.$album->title."</OPTION>\n";
					}
					echo "</SELECT>\n<br />";
					_e('Or', 'bp-galleries-plus' );
				echo "</label></p>\n";
	}
	 if($groupAlbums)
	{
		echo "\n<p><label>";
					_e('Select Group Album', 'bp-galleries-plus' );
					echo "\n".'	<SELECT NAME="selected_group_album" id="selected_group_album">'."\n";
					echo "<OPTION VALUE=0></OPTION>\n";
					foreach( $groupAlbums as $album)
					{
						echo '<OPTION VALUE="'.$album->id.'">'.$album->title."</OPTION>\n";
					}
					echo "</SELECT>\n<br />";
					_e('Or', 'bp-galleries-plus' );
				echo "</label></p>\n";
	}?>


   <p>
	<label><?php _e('Enter Album Name', 'bp-galleries-plus' ) ?><br />
	<input type="text" name="album_name" id="album_name"/></label>
    </p>

    <p>
	<label><?php _e('Select Image to Upload *', 'bp-galleries-plus' ) ?><br />
	<input type="file" value="" name="upload[]" multiple id="file"/></label>
    </p>
    <p id="album_visibility">
	<label><?php _e('Visibility','bp-galleries-plus') ?></label>

			<?php $checked=false;
				$groups_array = BP_Groups_Member::get_is_admin_of($owner_id);
				$group_count = $groups_array['total'];
				foreach($priv_str as $k => $str){
					if($limit_info[$k]['enabled']) {
						if($k == 5) :?>
							<label style="display:none"><input type="radio" id="priv_<?php echo $k ?>"name="privacy" value="<?php echo $k ?>"
						<?php else: ?>
							<label><input type="radio" id="priv_<?php echo $k ?>"name="privacy" value="<?php echo $k ?>" 
						<?php endif;
				if(!$checked){
					 echo 'checked="checked" ';
					 $checked = true;
				}
				if($k == 3)
				{
					if ($group_count == 0) // Only add group privacy if member has groups
					{
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-galleries-plus' );
					}											
					else
					{
						echo '/>'.$str;
						echo "\n<label>";
						_e('Select Group', 'bp-galleries-plus' );
						echo "\n".'	<SELECT NAME="selected_group" id="selected_group">'."\n";
					foreach( $groups_array['groups'] as $group)
						{
							echo '<OPTION VALUE="'.$group->id.'">'.$group->name."\n";
						}
						echo "</SELECT>\n<br />";
						echo "</label>\n";

					}
				}
				else
				{
					if ($limit_info[$k]['current'] && !$limit_info[$k]['remaining'])
						echo 'disabled="disabled" />'.$str.' '.__( '(limit reached)', 'bp-galleries-plus' );
					else
						echo '/>'.$str;
				}
			?></label>

			<?php }} ?>
    </p>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Upload image', 'bp-galleries-plus' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-galleries-plus-upload' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( 'You have reached the upload limit, delete some images if you want to upload more', 'bp-galleries-plus' ) ?></p>
	<?php endif;
}
 
 
/********************************************************************************
 * Action Functions
 *
 * Action functions are exactly the same as screen functions, however they do not
 * have a template screen associated with them. Usually they will send the user
 * back to the default screen after execution.
 */

/**
 * bp_gallplus_action_upload()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_action_upload() {
    
	global $bp, $wpdb;
	
	$notify_activity = false;

	if (( $bp->current_component == $bp->album->slug && $bp->album->upload_slug == $bp->current_action && isset( $_POST['submit'] )) || 
			(	$bp->current_component == $bp->album->group_slug && $bp->current_action == $bp->album->group_gallery_slug)&& isset( $_POST['submit'] ))
	{
//bp_logdebug('bp_gallplus_action_upload : start');	
		check_admin_referer('bp-galleries-plus-upload');
		
		$error_flag = false;
		$feedback_message = array();
			if( !isset($_POST['album_name']) ){
					$album_name = 'default';
			}

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
//bp_logdebug('bp_gallplus_action_upload : privacy');	
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-galleries-plus' );

		} else {

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
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
			$test = bp_gallplus_get_image_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			// For group privacy get the group_id
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
//bp_logdebug('bp_gallplus_action_upload : group');	
					$feedback_message[] = __( 'Please select a group for this album.', 'bp-galleries-plus' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

			if( $pic_limit === null){
				$error_flag = true;
//bp_logdebug('bp_gallplus_action_upload : pic_limit');	
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-galleries-plus' );	
			}			
			elseif( $pic_limit !== false && ( $pic_limit === 0  || $pic_limit <= bp_gallplus_get_image_count(array('privacy'=>$priv_lvl)) ) ) {

				$error_flag = true;
				
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public images.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for images visible to community members.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for images visible to friends.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private images.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
				}
			}
		}
		
		$uploadErrors = array(
			0 => __("There was no error, the file uploaded with success", 'bp-galleries-plus'),
			1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
			2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
			3 => __("The uploaded file was only partially uploaded", 'bp-galleries-plus'),
			4 => __("No file was uploaded", 'bp-galleries-plus'),
			6 => __("Missing a temporary folder", 'bp-galleries-plus')
		);
			$owner_type = 'user';
			$owner_id = $bp->loggedin_user->id;
			$date_uploaded =  gmdate( "Y-m-d H:i:s" );
		if(isset($_POST['selected_album']) && ($_POST['selected_album']) ){
				$album_id = $_POST['selected_album'];
			// Update the album's privacy level as well as the privacy level of the images within it	
		}
		else if(isset($_POST['selected_group_album']) && ($_POST['selected_group_album']) ){
//bp_logdebug('bp_gallplus_action_upload : should be here : '.$_POST['selected_group_album']);	
				$album_id = $_POST['selected_group_album'];
			// Update the album's privacy level as well as the privacy level of the images within it	
		}
		else
		{
			if( !isset($_POST['album_name']) ){
					$album_name = 'default';
			}
			else {
					$album_name = $_POST['album_name'];
			}
			$album_id = bp_gallplus_add_album($owner_type,$owner_id,$album_name,$album_name,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$group_id,$spare2,$spare3,$spare4);

		}
		if($album_id)
		{
			// update_privacy will not update if album owner_type is set to group or admin
			bp_gallplus_update_privacy($album_id,$priv_lvl,$group_id);
			$album = new BP_Gallplus_Album( $album_id );
			// Now need to retrieve the privacy and group level
			if(($album->owner_type == 'admin') || ($album->owner_type == 'group'))
			{
				$priv_lvl	 = $album->privacy;
				$group_id = $album->group_id;
			}
			// Set image privacy level to album privacy level
			$files=array();
			if ( isset($_FILES['upload']) )
			{
				$fdata=$_FILES['upload'];
				if(is_array($fdata['name']))
				{
					for($i=0;$i<count($fdata['name']);++$i){
	        	$files[]=array(
				    	'name'    =>$fdata['name'][$i],
    					'type'  => $fdata['type'][$i],
    					'tmp_name'=>$fdata['tmp_name'][$i],
    					'error' => $fdata['error'][$i], 
    					'size'  => $fdata['size'][$i]  
    				);
    			}
				}
				else
				{
			 		$files[]=$fdata;
				}
				if ($album->feature_image == 0)
				{
					$firstImage = true;
				}
				foreach($files as $file)
				{
					if ( $file['error'] ) {
						
						$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'bp-galleries-plus' ), $uploadErrors[$file['error']] );
						$error_flag = true;
						continue;
					}		
					elseif ( ($file['size'] / (1024 * 1024)) > $bp->album->bp_gallplus_max_upload_size ) 
					{
						$feedback_message[] = sprintf(__( 'The image you tried to upload was too big. Please upload a file less than ' . $bp->album->bp_gallplus_max_upload_size . 'MB', 'bp-galleries-plus'));
						$error_flag = true;
						continue;				
					}
					// Check the file has the correct extension type. Copied from bp_core_check_avatar_type() and modified with /i so that the
					// regex patterns are case insensitive (otherwise .JPG .GIF and .PNG would trigger an error)
					elseif ( (!empty( $file['type'] ) && !preg_match('/(jpe?g|gif|png)$/i', $file['type'] ) ) || !preg_match( '/(jpe?g|gif|png)$/i', $file['name'] ) ) {
						
						$feedback_message[] = __( 'Please upload only JPG, GIF or PNG image files.', 'bp-galleries-plus' );
						$error_flag = true;
						continue;
					}

					add_filter( 'upload_dir', 'bp_gallplus_upload_dir', 10, 0 );
  
					$pic_org = wp_handle_upload( $file,array('test_form' => false,'action'=>'image_upload') );


					if ( !empty( $pic_org['error'] ) ) {
						$feedback_message[] = sprintf( __('Your upload failed, please try again. Error was: %s', 'bp-galleries-plus' ), $pic_org['error'] );
						$error_flag = true;
						continue;
					}
		
					if( !is_multisite() )
					{

			    	// Some site owners with single-blog installs of WordPress change the path of
						// their upload directory by setting the constant 'BLOGUPLOADDIR'. Handle this
						// for compatibility with legacy sites.

						if( defined( 'BLOGUPLOADDIR' ) )
						{
							$abs_path_to_files = str_replace('/files/','/',BLOGUPLOADDIR);
						}
						else 
						{
							$abs_path_to_files = ABSPATH;
						}

					}
					else
					{
						// If the install is running in multisite mode, 'BLOGUPLOADDIR' is automatically set by
						// WordPress to something like "C:\xampp\htdocs/wp-content/blogs.dir/1/" even though the
						// actual file is in "C:\xampp\htdocs/wp-content/uploads/", so we need to use ABSPATH

						$abs_path_to_files = ABSPATH;
					}
					$pic_org_path = $pic_org['file'];
					$pic_org_url = str_replace($abs_path_to_files,'/',$pic_org_path);
			
					$pic_org_size = getimagesize( $pic_org_path );
					$pic_org_size = ($pic_org_size[0]>$pic_org_size[1])?$pic_org_size[0]:$pic_org_size[1];
			
					if($pic_org_size <= $bp->album->bp_gallplus_middle_size)
					{
						$pic_mid_path = $pic_org_path;
						$pic_mid_url = $pic_org_url;
					} 
					else
					{
						if(is_version('3.5'))
						{
					    list( $width , $height ) = getimagesize( $pic_org_path );
       				$path_parts = pathinfo( $pic_org_path);
       				$file_name = $path_parts['filename'];
							$editor = wp_get_image_editor( $pic_org_path );
							if($width > $height)
							{
								$editor->resize( $bp->album->bp_gallplus_middle_size, 0,false);
							}
							else
							{
								$editor->resize( 0, $bp->album->bp_gallplus_middle_size,false);
							}
       				$new_fileName = $file_name."_".$editor->get_suffix();
       				$pic_mid_path = $path_parts['dirname']."/".$new_fileName;
							$new_image_info = $editor->save( $pic_mid_path );
							$pic_mid_path = $new_image_info['path'];
							$pic_mid_url = str_replace($abs_path_to_files,'/',$pic_mid_path);
						}
						else
						{
							$pic_mid = wp_create_thumbnail( $pic_org_path, $bp->album->bp_gallplus_middle_size );
							$pic_mid_path = str_replace( '//', '/', $pic_mid );
							$pic_mid_url = str_replace($abs_path_to_files,'/',$pic_mid_path);
						}
						if (!$bp->album->bp_gallplus_keep_original)
						{

							unlink($pic_org_path);
							$pic_org_url=$pic_mid_url;
							$pic_org_path=$pic_mid_path;
						}
					}

					if($pic_org_size <= $bp->album->bp_gallplus_thumb_size){
						$pic_thumb_path = $pic_org_path;
						$pic_thumb_url = $pic_org_url;
					} 
					else
					{
						if(is_version('3.5'))
						{
							$editor = wp_get_image_editor( $pic_mid_path );
							if ( ! is_wp_error( $editor ) ) 
							{
								$editor->resize( $bp->album->bp_gallplus_thumb_size, $bp->album->bp_gallplus_thumb_size,true);
       					$path_parts = pathinfo( $pic_org_path);
       					$file_name = $path_parts['filename'];
       					$new_fileName = $file_name."_".$editor->get_suffix();
       					$pic_thumb_path = $path_parts['dirname']."/".$new_fileName;
								$new_image_info = $editor->save( $pic_thumb_path );
								$pic_thumb_path = $new_image_info['path'];
								$pic_thumb_url = str_replace($abs_path_to_files,'/',$pic_thumb_path);
							}
							else
							{
								bp_logdebug('Image resize error');
							}
						}
						else
						{
							$pic_thumb = image_resize( $pic_mid_path, $bp->album->bp_gallplus_thumb_size, $bp->album->bp_gallplus_thumb_size, true);
							$pic_thumb_path = str_replace( '//', '/', $pic_thumb );
							$pic_thumb_url = str_replace($abs_path_to_files,'/',$pic_thumb);
						}
					}

					$owner_type = 'user';
					$owner_id = $bp->loggedin_user->id;
					$date_uploaded =  gmdate( "Y-m-d H:i:s" );
					$title = $_FILES['file']['name'];
					$description = ' ';
					$privacy = $priv_lvl;

					$id=bp_gallplus_add_image($owner_type,$owner_id,$title,$description,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$pic_mid_url,$pic_mid_path,$pic_thumb_url,$pic_thumb_path,$album_id,$group_id);
				    if($id)
				    {
				    	//We want to save the feature image if the album does not have one
				    	if($firstImage)
				    	{
								$album->date_updated = gmdate( "Y-m-d H:i:s" );
				    		$album->feature_image = $id;
				    		$album->feature_image_path = $pic_thumb_url;
				    		$album->save();
				    		$firstImage = false;
			    			$notify_activity = true;

				    	}
					    $feedback_message[] = __('Image(s) uploaded. Now you can edit the image details.', 'bp-galleries-plus');
					  }
				    else {
					    $error_flag = true;
					    $feedback_message[] = __('There were problems saving the image details.', 'bp-galleries-plus');
						}
				}
			}
			else
			{
				$error_flag = true;
				$feedback_message[] = __('There were problems creating the album.', 'bp-galleries-plus');
			}
		}
		else 
		{
			$feedback_message[] = sprintf( __( 'Your upload failed, please try again. Error was: %s', 'bp-galleries-plus' ), $uploadErrors[4] );
			$error_flag = true;
		
		}

		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} 
		else 
		{
			// Set the date updated
			$album->date_updated = gmdate( "Y-m-d H:i:s" );
   		$album->save();
			
			if ($notify_activity)
			{
				bp_gallplus_record_album_activity($album);
			}
			else
			{
				bp_gallplus_record_album_activity($album,true);
			}
			if($album->owner_type == 'group')
			{
				bp_gallplus_record_group_album_activity($group_id,$id); 
			}
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			if(isset($_POST['group_name']))
			{
				bp_core_redirect( get_site_url() .'/'. $bp->current_component . '/'.$_POST['group_name'].'/'.$bp->album->group_gallery_slug);
			}
			else
			{
				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/?album_id='. $album->id);
			}
//			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/?album_id='. $album->id);
//			bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
			die;
		}
		
	}
	
}
add_action('bp_actions','bp_gallplus_action_upload',3);
add_action('wp','bp_gallplus_action_upload',3);

/**
 * bp_gallplus_upload_dir() 
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_upload_dir() {
    
	global $bp;

	$user_id = $bp->loggedin_user->id;
	
	$dir = BP_GALLPLUS_UPLOAD_PATH;

	$siteurl = trailingslashit( get_blog_option( 1, 'siteurl' ) );
	$url = str_replace(ABSPATH,$siteurl,$dir);
	
	$bdir = $dir;
	$burl = $url;
	
	$subdir = '/' . $user_id;
	
	$dir .= $subdir;
	$url .= $subdir;

	if ( !file_exists( $dir ) )
		@wp_mkdir_p( $dir );

	return apply_filters( 'bp_gallplus_upload_dir', array( 'path' => $dir, 'url' => $url, 'subdir' => $subdir, 'basedir' => $bdir, 'baseurl' => $burl, 'error' => false ) );

}

/**
 * bp_gallplus_action_edit()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_action_edit() {
    
	global $bp,$images_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_edit_slug == $bp->current_action && $images_template->image_count && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1] &&  isset( $_POST['submit'] )) {
	
		check_admin_referer('bp-galleries-plus-edit');
		
		$error_flag = false;
		$feedback_message = array();
		
		if(isset($_POST['image_id']))
		{
			$id = $_POST['image_id'];
		}
		else
		{
			$error_flag = true;
			$feedback_message[] = __( 'Unable to save image.', 'bp-galleries-plus' );
		}
		$album_id = 0;
		if(isset($_POST['album_id']))
		{
			$album_id = $_POST['album_id'];
		}
		
		if(empty($_POST['title'])){
//			$error_flag = true;
//			$feedback_message[] = __( 'Image Title can not be blank.', 'bp-galleries-plus' );
		}

		if( $bp->album->bp_gallplus_require_description && empty($_POST['description'])){
//			$error_flag = true;
//			$feedback_message[] = __( 'Image Description can not be blank.', 'bp-galleries-plus' );
		}
		
		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-galleries-plus' );	
		}
		else {
			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
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


			if($priv_lvl == 10 )
				$pic_limit = is_super_admin() ? false : null;
			if( $pic_limit === null){
				$error_flag = true;
				$feedback_message[] = __( 'Privacy option is not correct.', 'bp-galleries-plus' );	
			}
			elseif( $pic_limit !== false && $priv_lvl !== $images_template->images[0]->privacy && ( $pic_limit === 0|| $pic_limit <= bp_gallplus_get_image_count(array('privacy'=>$priv_lvl)) ) ){
				$error_flag = true;
				switch ($priv_lvl){
					case 0 :
						$feedback_message[] = __( 'You reached the limit for public images.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 2 :
						$feedback_message[] = __( 'You reached the limit for images visible to community members.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 4 :
						$feedback_message[] = __( 'You reached the limit for images visible to friends.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
					case 6 :
						$feedback_message[] = __( 'You reached the limit for private images.', 'bp-galleries-plus' ).' '.__( 'Please select another privacy option.', 'bp-galleries-plus' );
						break;
				}
			}
		}

//		if(bp_is_active('activity') && $bp->album->bp_gallplus_enable_comments)
//			if(!isset($_POST['enable_comments']) || ($_POST['enable_comments']!= 0 && $_POST['enable_comments']!= 1)){
//				$error_flag = true;
//				$feedback_message[] = __( 'Comments option is not correct.', 'bp-galleries-plus' );
//			}
//		else
			$_POST['enable_comments']==0;

		if( !$error_flag ){

			// WordPress adds an escape character "\" to some special values in INPUT FIELDS (test's becomes test\'s), so we have to strip
			// the escape characters, and then run the data through *proper* filters to prevent SQL injection, XSS, and various other attacks.

			if( bp_gallplus_edit_image($id,stripslashes($_POST['title']),stripslashes($_POST['description']),$priv_lvl,$_POST['enable_comments']) ){
				$feedback_message[] = __('Image details saved.', 'bp-galleries-plus');
			}else{
				$error_flag = true;
				$feedback_message[] = __('There were problems saving image details.', 'bp-galleries-plus');
			}
		}
		if ($error_flag){
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
		} 
		else {
			bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
			bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->album_slug.'/'.$album_id.'/');
			die;
		}
		
	}
	
}
add_action('bp_actions','bp_gallplus_action_edit',3);
add_action('wp','bp_gallplus_action_edit',3);

/**
 * bp_gallplus_action_delete()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_action_delete() {

	global $bp,$images_template;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->single_slug == $bp->current_action && $images_template->image_count && isset($bp->action_variables[1]) && $bp->album->delete_slug == $bp->action_variables[1] ) {
		check_admin_referer('bp-galleries-plus-delete-pic');
		
				
		if(!$images_template->image_count){
			bp_core_add_message( __( 'This url is not valid.', 'bp-galleries-plus' ), 'error' );
			return;
		}
		else{
			
			if ( !bp_is_my_profile() && !current_user_can(level_10) ) {
				bp_core_add_message( __( 'You don\'t have permission to delete this image', 'bp-galleries-plus' ), 'error' );
			}
			elseif (bp_gallplus_delete_image($images_template->images[0]->id)){
				bp_core_add_message( __( 'Image deleted.', 'bp-galleries-plus' ), 'success' );
				bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->images_slug .'/');
				die;
			}
			else{
				bp_core_add_message( __( 'There were problems deleting the image.', 'bp-galleries-plus' ), 'error' );
			}
		}
		bp_core_redirect( $bp->displayed_user->domain . $bp->album->slug . '/'. $bp->album->single_slug .'/'.$images_template->images[0]->id. '/');
		die;
	}
}
add_action('bp_actions','bp_gallplus_action_delete',3);
add_action('wp','bp_gallplus_action_delete',3);

/**
 * bp_gallplus_screen_all_images()
 * 
 * Displays sitewide featured content block
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_screen_all_images() {

        global $bp;

        bp_gallplus_query_images();
	bp_gallplus_load_subtemplate( apply_filters( 'bp_gallplus_screen_all_images', 'album/all-images' ), false );
}
add_action('bp_gallplus_all_images','bp_gallplus_screen_all_images',3);

function bp_gallplus_screen_add_album() {
    
	global $bp;

	do_action( 'bp_gallplus_screen_add_album' );

	add_action( 'bp_template_content', 'bp_gallplus_screen_add_album_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_gallplus_screen_add_album_content() {

	global $bp;

	$limit_info = bp_gallplus_limits_info();


	?>

	<h4><?php _e( 'Add New Album', 'bp-galleries-plus' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-galleries-plus-add-album-form" id="bp-galleries-plus-add-album-form" class="standard-form">

    <!-- input type="hidden" name="action" value="add_album" /-->
    <input type="hidden" name="action" value="album_upload" />

    <p>
			<label><?php _e('Enter Album Name', 'bp-galleries-plus' ) ?><br />
			<input type="text" name="album_name" id="album_name"/></label>
    </p>
   	<p>
			<label><?php _e('Enter Album Description', 'bp-galleries-plus' ) ?><br />
				<textarea cols="40" rows="10" name="album_desc" id="album_desc">
				</textarea>
			</label>
    </p>
    <p>
	    <?php	bp_gallplus_screen_visibility(); ?>
    </p>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Add Album', 'bp-galleries-plus' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-galleries-plus-add-album' );
		?>
	</form>
	<?php 
}

/**
 * bp_gallplus_action_add_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_action_add_album() {
    
	global $bp;
	
	if ( $bp->current_component == $bp->album->slug && $bp->album->albums_add_slug == $bp->current_action && isset( $_POST['submit'] )) 
	{
	
		check_admin_referer('bp-galleries-plus-add-album');
		
		$error_flag = false;
		$feedback_message = array();

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-galleries-plus' );

		} else 
		{

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
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
			$test = bp_gallplus_get_image_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
					$feedback_message[] = __( 'Please select a group for this gallery.', 'bp-galleries-plus' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

		
			$uploadErrors = array(
				0 => __("There was no error, the file uploaded with success", 'bp-galleries-plus'),
				1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
				2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
				3 => __("The uploaded file was only partially uploaded", 'bp-galleries-plus'),
				4 => __("No file was uploaded", 'bp-galleries-plus'),
				6 => __("Missing a temporary folder", 'bp-galleries-plus')
			);
		}
		if(!$error_flag)
		{  


			if( !isset($_POST['album_name']) ){
		    $error_flag = true;
		    $feedback_message[] = __('You need to specify a gallery name', 'bp-galleries-plus');
			}
			else {
				$album_name = $_POST['album_name'];
			}
			if(!$error_flag)
			{
				if( !isset($_POST['album_desc']) ){
					$album_desc = $album_name;
				}
				else {
					$album_desc = $_POST['album_desc'];
				}
				$owner_type = 'user';
				$owner_id = $bp->loggedin_user->id;
				$date_uploaded =  gmdate( "Y-m-d H:i:s" );
			
				$album_id = bp_gallplus_add_album($owner_type,$owner_id,$album_name,$album_name,$priv_lvl,$date_uploaded,$pic_org_url,$pic_org_path,$group_id,$spare2,$spare3,$spare4);
				if($album_id)
				{
	
			    $feedback_message[] = __('Album '.$album_name.' created successfully, you can now add images to it', 'bp-galleries-plus');
				}
				else
				{
					$error_flag = true;
					$feedback_message[] = __('There were problems creating the album '.$album_name.'.', 'bp-galleries-plus');
				}
			}
			if ($error_flag){
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
			} 
			else {
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/');
//				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
				die;
			}
		
		}
	}
}
add_action('bp_actions','bp_gallplus_action_add_album',3);
add_action('wp','bp_gallplus_action_add_album',3);
function bp_gallplus_screen_edit_album() {
    
	global $bp;

	do_action( 'bp_gallplus_screen_edit_album' );

	add_action( 'bp_template_content', 'bp_gallplus_screen_edit_album_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function bp_gallplus_screen_visibility($group_id = 0,$image_edit = false){
	global $bp;

	$limit_info = bp_gallplus_limits_info();

	if($bp->bp_gallplus_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			5 => __('Group Members can view and add','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-galleries-plus'),
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			5 => __('Group Members can view and add','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	$owner_id = $bp->loggedin_user->id;

   echo "<p>\n<label>";
	_e('Visibility','bp-galleries-plus');
	echo"</label>\n";

	$checked=false;
	$groups_array = bp_gallplus_member_group_name($owner_id); //BP_Groups_Member::get_is_admin_of($owner_id);
	bp_logdebug('bp_gallplus_screen_visibility : '.print_r($groups_array,true));
	$group_count = count($groups_array);
	if($image_edit)
	{
		$privacy = bp_gallplus_get_image_priv();
	}
	else
	{
		$privacy = bp_gallplus_get_album_priv();
	}
	foreach($priv_str as $k => $str){
		echo "\n".'<label><input type="radio" id="priv_'.$k.'" name="privacy" value="'.$k.'"';
		if($k == $privacy ) {
			if(!$checked){
				echo 'checked="checked" ';
				$checked = true;
			}
		}
		if($privacy == 5)
		{
			echo 'disabled="disabled" />'.$str;
		}
		else
		{
			if($k == 3)
			{
				if ($group_count == 0) // Only add group privacy if member has groups
				{
					echo 'disabled="disabled" />'.$str;
				}											
				else
				{
					echo '/>'.$str;
					echo "\n<label>";
					_e('Select Group', 'bp-galleries-plus' );
					echo "\n".'	<SELECT NAME="selected_group" id="selected_group">'."\n";
					foreach( $groups_array['groups'] as $group)
					{
						if(($group_id > 0) && ($group['id'] == $group_id))
						{
							echo '<OPTION selected="selected" VALUE="'.$group['id'].'">'.$group['name']."\n";
						}
						else
						{
							echo '<OPTION VALUE="'.$group['id'].'">'.$group['name']."\n";
						}
					}
					echo "</SELECT>\n<br />";
					echo "</label>\n";

				}
			}
			else
			{
				echo '/>'.$str;
			}
		}
		echo "\n</label></p>\n";

	}
}

function bp_gallplus_screen_edit_album_content() {

	global $bp,$albums_template;

	if (bp_gallplus_has_albums()) :
		if(isset($bp->action_variables[0]))
		{
	   bp_gallplus_the_album($bp->action_variables[0]);
	  }
	  else
		{
	   bp_gallplus_the_album();
	  }
	$limit_info = bp_gallplus_limits_info();

	if($bp->bp_gallplus_disable_public_access)
	{
		$priv_str = array(
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}
	else
	{
		$priv_str = array(
			0 => __('Public','bp-galleries-plus'),
			2 => __('Registered members','bp-galleries-plus'),
			3 => __('Group Members','bp-galleries-plus'),
			4 => __('Only friends','bp-galleries-plus'),
			6 => __('Private','bp-galleries-plus'),
			10 => __('Hidden (admin only)','bp-galleries-plus')
		);
	}

	?>

	<h4><?php _e( 'Edit Album', 'bp-galleries-plus' ) ?></h4>

	<form method="post" enctype="multipart/form-data" name="bp-galleries-plus-edit-album-form" id="bp-galleries-plus-edit-album-form" class="standard-form">

    <!-- input type="hidden" name="action" value="add_album" /-->
    <input type="hidden" name="action" value="album_edit" />
    <input type="hidden" name="album_id" value="<?php bp_gallplus_album_id() ?>" />

   <p>
		<label><?php _e('Album Name *', 'bp-galleries-plus' ) ?><br />
		<input type="text" name="title" id="album-title" size="100" value="<?php
		echo (empty($_POST['title'])) ? bp_gallplus_get_album_title() : wp_filter_kses($_POST['title']);
	?>"/></label>
    </p>
    <p>
	<label><?php _e('Album Description', 'bp-galleries-plus' ) ?><br />
	<textarea name="description" id="album-description" rows="15"cols="40" ><?php
		echo (empty($_POST['description'])) ? bp_gallplus_get_album_desc() : wp_filter_kses($_POST['description']);
	?></textarea></label>
    </p>
		<?php bp_gallplus_screen_visibility(bp_gallplus_get_group_id()); ?>
    
    <?php if(bp_is_active('activity') && $bp->album->bp_gallplus_enable_comments ): ?>
    <p>
	<label><?php _e('Image activity and comments','bp-galleries-plus') ?></label>
			<label><input type="radio" name="enable_comments" value="1" checked="checked" /><?php _e('Enable','bp-galleries-plus') ?></label>
			<label><input type="radio" name="enable_comments" value="0" /><?php _e('Disable','bp-galleries-plus') ?></label>
			<?php _e('If image already has comments this will delete them','bp-galleries-plus') ?>
    </p>
    <?php endif; ?>
    <input type="submit" name="submit" id="submit" value="<?php _e( 'Save', 'bp-galleries-plus' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-galleries-plus-edit-album' );
		?>
	</form>
	<?php else: ?>
		<p><?php _e( "Either this url is not valid or you can't edit this image.", 'bp-galleries-plus' ) ?></p>
	<?php endif;
}

/**
 * bp_gallplus_action_edit_album()
 *
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_action_edit_album() {
    
	global $bp;
	if ( $bp->current_component == $bp->album->slug && $bp->album->albums_slug == $bp->current_action && isset($bp->action_variables[1]) && $bp->album->edit_slug == $bp->action_variables[1]  && isset( $_POST['submit'] )) 
	{
	
		check_admin_referer('bp-galleries-plus-edit-album');
		
		$error_flag = false;
		$feedback_message = array();

		if( !isset($_POST['privacy']) ){
			$error_flag = true;
			$feedback_message[] = __( 'Please select a privacy option.', 'bp-galleries-plus' );

		} else 
		{

			$priv_lvl = intval($_POST['privacy']);

                        switch ($priv_lvl) {
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
			$test = bp_gallplus_get_image_count(array('privacy'=>$priv_lvl));

			if($priv_lvl == 10 ) {
				$pic_limit = is_super_admin() ? false : null;
			}
			// For group privacy get the group_id
			$group_id = 0;
			if($priv_lvl == 3){
				if( !isset($_POST['selected_group']) ){
					$error_flag = true;
					$feedback_message[] = __( 'Please select a group for this gallery.', 'bp-galleries-plus' );
				}
				else
				{
					$group_id = $_POST['selected_group'];
				}
			}

		
			$uploadErrors = array(
				0 => __("There was no error, the file uploaded with success", 'bp-galleries-plus'),
				1 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
				2 => __("Your image was bigger than the maximum allowed file size of: " . $bp->album->bp_gallplus_max_upload_size . "MB"),
				3 => __("The uploaded file was only partially uploaded", 'bp-galleries-plus'),
				4 => __("No file was uploaded", 'bp-galleries-plus'),
				6 => __("Missing a temporary folder", 'bp-galleries-plus')
			);
		}
		if(bp_is_active('activity') && $bp->album->bp_gallplus_enable_comments)
			if(!isset($_POST['enable_comments']) || ($_POST['enable_comments']!= 0 && $_POST['enable_comments']!= 1)){
				$error_flag = true;
				$feedback_message[] = __( 'Comments option is not correct.', 'bp-galleries-plus' );
			}
		else
			$_POST['enable_comments']==0;
		if(!$error_flag)
		{  


			if( !isset($_POST['title']) ){
		    $error_flag = true;
		    $feedback_message[] = __('You need to specify an gallery name', 'bp-galleries-plus');
			}
			else {
				$album_name = $_POST['title'];
			}
			if(!$error_flag)
			{
				if( !isset($_POST['description']) ){
					$album_desc = $album_name;
				}
				else {
					$album_desc = $_POST['description'];
				}
				if( !isset($_POST['album_id']) ){
			    $error_flag = true;
		    	$feedback_message[] = __('Unable to save album', 'bp-galleries-plus');
				}
				else {
					$album_id = $_POST['album_id'];
				}
				if(!$error_flag)
				{
					$date_updated =  gmdate( "Y-m-d H:i:s" );
					$edit_result = bp_gallplus_edit_album($album_id,$album_name,$album_desc,$priv_lvl,$date_updated,$_POST['enable_comments'],$group_id);
					if($edit_result)
					{
	
				    $feedback_message[] = __('Album '.$album_name.' updated successfully', 'bp-galleries-plus');
					}
					else
					{
						$error_flag = true;
						$feedback_message[] = __('There were problems updating the gallery '.$album_name.'.', 'bp-galleries-plus');
					}
				}
			}
			if ($error_flag){
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'error');
			} 
			else {
					bp_gallplus_record_album_activity($album,true);
				bp_core_add_message( implode('&nbsp;', $feedback_message ),'success' );
				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->album_slug.'/'. $album_id.'/');
//				bp_core_redirect( $bp->loggedin_user->domain . $bp->current_component . '/'.$bp->album->single_slug.'/' . $id.'/'.$bp->album->edit_slug.'/');
				die;
			}
		
		}
	}
}
add_action('bp_actions','bp_gallplus_action_edit_album',3);
add_action('wp','bp_gallplus_action_edit_album',3);

if ( ! function_exists( 'is_version' ) ) {
    function is_version( $version = '3.5' ) {
        global $wp_version;
         
        if ( version_compare( $wp_version, $version, '>=' ) ) {
            return true;
        }
        return false;
    }
}
?>