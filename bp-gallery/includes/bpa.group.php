<?php

/**
 * bp-galleries-plus group
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
 if ( !defined( 'BP_GALLPLUS_GROUP_GALLERY_SLUG' ) )
	define( 'BP_GALLPLUS_GROUP_GALLERY_SLUG', 'gallery' );

add_action( 'wp_head', 'bp_gallplus_add_css' );

class BP_Group_Gallery extends BP_Group_Extension {

	var $enable_nav_item = true;
	var $enable_create_step = true;
	var $enable_edit_item = false;

	function bp_group_gallery() {
		global $bp;

		$this->has_caps = true;

		/* Group API Extension Properties */
		$this->name = __( 'Gallery', 'buddypress' );
		$this->slug = BP_GALLPLUS_GROUP_GALLERY_SLUG;

		/* Set as early in the order as possible */
		$this->create_step_position = 42;
		$this->nav_item_position = 71;

		/* Generic check access */
		if ( $this->has_caps == false ) {
			$this->enable_create_step = false;
			$this->enable_edit_step = false;
		}

		$this->enable_nav_item = $this->enable_nav_item();
		$this->enable_create_step = $this->enable_nav_item(true);
	}

	function display() {
		global $bp;

	$albumIDs = BP_Gallplus_Album::query_group_album($bp->groups->current_group->id);
	if(count($albumIDs) > 0)
	{
	?>
		<form method="post" enctype="multipart/form-data" name="bp-galleries-plus-upload-form" id="bp-galleries-plus-upload-form" class="standard-form">

  	  <input type="hidden" name="upload" value="<?php echo $bp->album->bp_gallplus_max_upload_size; ?>" />
    	<input type="hidden" name="action" value="image_upload" />
			<input type="hidden" name="privacy" value="5"/>
			<input type="hidden" name="selected_group_album" value="<?php echo $albumIDs[0]->id ?>"/>
			<input type="hidden" name="group_name" value="<?php echo $bp->groups->current_group->slug ?>"/>
			<input type="hidden" name="selected_group" value="<?php echo $bp->groups->current_group->id ?>"/>

    	<p>
				<label><?php _e('Select Image to Upload *', 'bp-galleries-plus' ) ?><br />
				<input type="file" value="" name="upload[]" multiple id="file"/></label>
    	</p>
   		<input type="submit" name="submit" id="submit" value="<?php _e( 'Upload image', 'bp-galleries-plus' ) ?>"/>

		<?php
		// This is very important, don't leave it out. 
		wp_nonce_field( 'bp-galleries-plus-upload' );
		?>
   </form>
   <?php
//   bp_logdebug('BP_Group_Gallery display post : '.print_r($bp,true));
	
				$album_args = array(
								'album_id' => $albumIDs[0]->id,
								'privacy'=>'group_gallery');
			$locPage = $bp->action_variables[0];
			if(($locPage)  && ($locPage > 1))
			{
				$album_args['page'] = $locPage;
			}
				bp_gallplus_query_images($album_args);
					 if ( bp_gallplus_has_images() ) : ?>
		<div class="image-pagination" style="margin-top: 10px; margin-bottom: 5px;">
			<?php bp_gallplus_group_album_pagination(); ?>	
		</div>			
					
		<div class="image-gallery">												
				<?php while ( bp_gallplus_has_images() ) : bp_gallplus_the_image(); ?>
					<div class="image-thumb-box">
	             <a href="<?php get_option('bp_gallplus_mid_size') ? bp_gallplus_image_mid_url() : bp_gallplus_image_url() ?>" <?php bp_gallplus_image_viewer_attribute() ?>><img src='<?php bp_gallplus_image_thumb_url() ?>' /></a>
							<table class="group-image">
								<tr>
									<td class="group-image">
										 <?php bp_gallplus_image_owner_profile_link() ?>
								  </td>
								 </tr>
								<tr>
									<td class="group-image">
							 				<?php 	bp_gallplus_like_button( bp_gallplus_get_image_id(), 'image' ); ?>
								  </td>
								 </tr>
							
	                <?php if (is_super_admin()|| groups_is_user_admin( $bp->loggedin_user->id, $bp->groups->current_group->id) || groups_is_user_mod( $bp->loggedin_user->id,$bp->groups->current_group->id )) : ?>
								<tr>
									<td class="group-image">
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; image actions &raquo;
												</option>
													<option value="BPGPLSDeleteImage(<?php bp_gallplus_image_id() ?>)">
													Delete Image
												</option>
											</select>
										</div>
								  </td>
								 </tr>

									<?php endif; ?>		
							</table>
						</div>
					
				<?php endwhile; ?>
		</div>					
	<?php else : ?>					
		<div id="message" class="info">
				<p><?php echo bp_word_or_name( __('No pics here, show something to the community!', 'bp-galleries-plus' ), __( "Either %s hasn't uploaded any image yet or they have restricted access", 'bp-galleries-plus' )  ,false,false) ?></p>
		</div>
				
	<?php endif; 
		}                                 
 	}

	function create_screen() {
		global $bp;

		/* If we're not at this step, go bye bye */
		if ( !bp_is_group_creation_step( $this->slug ) )
			return false;
		bp_galplus_group_checkbox();
		wp_nonce_field( 'groups_create_save_' . $this->slug );
	}

	function create_screen_save($group_id  ) {
		global $bp;

		/* Always check the referer */
		check_admin_referer( 'groups_create_save_' . $this->slug );
	 $current_group =  $bp->groups->current_group;
		/* Set method and save */
		if ( isset( $_POST['galplus_group_checkbox'] ) && ( $_POST['galplus_group_checkbox']) ) {
			$date_uploaded =  gmdate( "Y-m-d H:i:s" );
			$owner_id = $bp->loggedin_user->id;

			$album_id = bp_gallplus_add_album('group',$owner_id,$current_group->name,$current_group->name,5,$date_uploaded,"","", $current_group->id,"","","","","");
		}
	}
	function enable_nav_item($creation_step = false) {
		global $bp;
		//For some reason bp->groups->current_group is not set at this point so we have to determine it from  bp->current_item
		$group_id = BP_Groups_Group::group_exists($bp->current_item);		
		if(($creation_step) || (bp_gallplus_group_has_album($group_id)))
		{
			$imageIDs = BP_Gallplus_Image::query_group_image($group_id);
				if(count($imageIDs) > 0)
				{
					$this->name = __( 'Gallery <span>'.count($imageIDs).'</span>', 'buddypress' );
				}
				else
				{
					$this->name = __( 'Gallery', 'buddypress' );
				}
				return true;
		}
		else
		{
			return false;
		}
	}

	function widget_display() {}
}
bp_register_group_extension( 'BP_Group_Gallery' );



function bp_galplus_group_checkbox() {
	global $bp, $wpdb;
	?>
	<label for="galplus_group_checkbox">Create a gallery for this group </label>
						<input name="galplus_group_checkbox" type="checkbox" value="1" checked />
							<small><?php _e('Enabling this will create a gallery where group members can upload images.') ?></small>
    <?php
    }

?>