<?php

/***
 * This file is used to add site administration menus to the WordPress network admin backend.
 */

/**
 * bp_gallplus_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_admin() {
    
	global $bp;

	// If the form has been submitted and the admin referrer checks out, save the settings
	if ( isset( $_POST['submit'] )  ) { 

		check_admin_referer('bpgpls-settings');

		if( current_user_can('install_plugins') ) {

			update_site_option( 'bp_gallplus_slug', $_POST['bp_gallplus_slug'] );
			update_site_option( 'bp_gallplus_max_images', $_POST['bp_gallplus_max_images']=='' ? false : intval($_POST['bp_gallplus_max_images']) );

			foreach(array(0,2,4,6) as $i){
				$option_name = "bp_gallplus_max_priv{$i}_images";
				$option_value = $_POST[$option_name]=='' ? false : intval($_POST[$option_name]);
				update_site_option($option_name , $option_value);
			}
			
			update_site_option( 'bp_gallplus_max_upload_size', $_POST['bp_gallplus_max_upload_size'] );
			update_site_option( 'bp_gallplus_keep_original', $_POST['bp_gallplus_keep_original'] );
			update_site_option( 'bp_gallplus_require_description', $_POST['bp_gallplus_require_description'] );
			update_site_option( 'bp_gallplus_enable_comments', $_POST['bp_gallplus_enable_comments'] );
			update_site_option( 'bp_gallplus_disable_public_access', $_POST['bp_gallplus_disable_public_access'] );
			update_site_option( 'bp_gallplus_enable_wire', $_POST['bp_gallplus_enable_wire'] );
			update_site_option( 'bp_gallplus_middle_size', $_POST['bp_gallplus_middle_size'] );
			update_site_option( 'bp_gallplus_thumb_size', $_POST['bp_gallplus_thumb_size'] );
			update_site_option( 'bp_gallplus_per_page', $_POST['bp_gallplus_per_page'] );
			update_site_option( 'bp_gallplus_url_remap', $_POST['bp_gallplus_url_remap'] );
			update_site_option( 'bp_gallplus_base_url', $_POST['bp_gallplus_base_url'] );
			update_site_option( 'bp_gallplus_viewer', $_POST['bp_gallplus_viewer'] );
			update_site_option( 'bp_gallplus_mid_size', $_POST['bp_gallplus_mid_size'] );
			update_site_option( 'bp_gallplus_use_watermark', $_POST['bp_gallplus_use_watermark'] );
			update_site_option( 'bp_gallplus_watermark_text', $_POST['bp_gallplus_watermark_text'] );
			update_site_option( 'bp_gallplus_keep_files', $_POST['bp_gallplus_keep_files'] );

			$updated = true;

			if($_POST['bp_gallplus_rebuild_activity'] && !$_POST['bp_gallplus_undo_rebuild_activity']){
			    bp_gallplus_rebuild_activity();
			}

			if( !$_POST['bp_gallplus_rebuild_activity'] && $_POST['bp_gallplus_undo_rebuild_activity']){
			    bp_gallplus_undo_rebuild_activity();
			}
		}
		else {
			die("You do not have the required permissions to view this page");
		}

	}

        $bp_gallplus_slug = get_site_option( 'bp_gallplus_slug' );
        $bp_gallplus_max_images = get_site_option( 'bp_gallplus_max_images' );
        $bp_gallplus_max_upload_size = get_site_option( 'bp_gallplus_max_upload_size' );
        $bp_gallplus_max_priv0_images = get_site_option( 'bp_gallplus_max_priv0_images' );
        $bp_gallplus_max_priv2_images = get_site_option( 'bp_gallplus_max_priv2_images' );
        $bp_gallplus_max_priv4_images = get_site_option( 'bp_gallplus_max_priv4_images' );
        $bp_gallplus_max_priv6_images = get_site_option( 'bp_gallplus_max_priv6_images' );
        $bp_gallplus_keep_original = get_site_option( 'bp_gallplus_keep_original' );
        $bp_gallplus_require_description = get_site_option( 'bp_gallplus_require_description' );
        $bp_gallplus_enable_comments = get_site_option( 'bp_gallplus_enable_comments' );
        $bp_gallplus_disable_public_access = get_site_option('bp_gallplus_disable_public_access');
        $bp_gallplus_enable_wire = get_site_option( 'bp_gallplus_enable_wire' );
        $bp_gallplus_middle_size = get_site_option( 'bp_gallplus_middle_size' );
        $bp_gallplus_thumb_size = get_site_option( 'bp_gallplus_thumb_size' );
        $bp_gallplus_per_page = get_site_option( 'bp_gallplus_per_page' );
				$bp_gallplus_url_remap = get_site_option( 'bp_gallplus_url_remap' );
				$bp_gallplus_base_url = get_site_option( 'bp_gallplus_base_url' );
				$bp_gallplus_viewer = get_option('bp_gallplus_viewer');
				if(!isset($bp_gallplus_viewer))
				{
					$bp_gallplus_viewer = 0;
				}
				$bp_gallplus_mid_size = get_option('bp_gallplus_mid_size');
				$bp_gallplus_rebuild_activity = false;
				$bp_gallplus_undo_rebuild_activity = false;
				$bp_gallplus_use_watermark = get_site_option( 'bp_gallplus_use_watermark');
				$bp_gallplus_watermark_text = get_site_option( 'bp_gallplus_watermark_text');
				$bp_gallplus_keep_files = get_site_option( 'bp_gallplus_keep_files');



	?>

	<div class="wrap">
	    
		<h2><?php _e('BP Galleries Plus - ', 'bp-galleries-plus' ) ?> 1.1<?php _e(' - [Network Mode]', 'bp-galleries-plus' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __('Settings Updated.', 'bp-galleries-plus' ) . "</p></div>" ?><?php endif; ?>

		<p>
		    <br>
		</p>
		<?php bpg_info_box(); ?>

		<?php // The address in this line of code determines where the form will be sent to // ?>
		<form action="<?php echo site_url() . '/wp-admin/network/admin.php?page=bp-galleries-plus-settings' ?>" name="example-settings-form" id="example-settings-form" method="post">

                    <h3><?php _e('Slug Name', 'bp-galleries-plus' ) ?></h3>

		    <p>
		    <?php 
			_e("Bad slug names will disable the plugin. No Spaces. No Punctuation. No Special Characters. No Accents.", 'bp-galleries-plus' );
			echo " <br> ";
			_e("{ abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_- } ONLY.", 'bp-galleries-plus' )
		    ?>
		    </p>
		    
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Name of BP Galleries Plus slug', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_slug" type="text" id="bp_gallplus_slug" value="<?php echo esc_attr($bp_gallplus_slug ); ?>" size="10" />
					</td>
				</tr>

			</table>

                    <h3><?php _e('General', 'bp-galleries-plus' ) ?></h3>

                                                         <table class="form-table">
                                <tr>
					<th scope="row"><?php _e('Force members to enter a description for each image', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_require_description" type="text" id="bp_gallplus_require_description"<?php if ($bp_gallplus_require_description == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_require_description" type="text" id="bp_gallplus_require_description"<?php if ($bp_gallplus_require_description == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Allow site members to post comments on BP Gallery Plus album images', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_enable_comments" type="text" id="bp_gallplus_enable_comments"<?php if ($bp_gallplus_enable_comments == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_enable_comments" type="text" id="bp_gallplus_enable_comments"<?php if ($bp_gallplus_enable_comments == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>
                               <tr>
					<th scope="row"><?php _e('Disable public access to all albums', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_disable_public_access" type="text" id="bp_gallplus_disable_public_access"<?php if ($bp_gallplus_disable_public_access == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_disable_public_access" type="text" id="bp_gallplus_disable_public_access"<?php if ($bp_gallplus_disable_public_access == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Post image thumbnails to members activity stream', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_enable_wire" type="text" id="bp_gallplus_enable_wire"<?php if ($bp_gallplus_enable_wire == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_enable_wire" type="text" id="bp_gallplus_enable_wire"<?php if ($bp_gallplus_enable_wire == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>

			</table>
		    
                    <h3><?php _e( 'Album Size Limits', 'bp-galleries-plus' ) ?></h3>

                    <p>
		    <?php _e( "<b>Accepted values:</b> EMPTY (no limit), NUMBER (value you set), 0 (disabled). The first option does not accept 0. The last option only accepts a number.", 'bp-galleries-plus' ) ?>
		    </p>
		    
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Max total images allowed in each album', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_images" type="text" id="example-setting-one" value="<?php echo esc_attr( $bp_gallplus_max_images ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible to public allowed in each album', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_priv0_images" type="text" id="bp_gallplus_max_priv0_images" value="<?php echo esc_attr( $bp_gallplus_max_priv0_images ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible only to members in each album', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_priv2_images" type="text" id="bp_gallplus_max_priv2_images" value="<?php echo esc_attr( $bp_gallplus_max_priv2_images ); ?>" size="10" />
					</td>
				</tr>
                                 <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max images visible only to friends in each album', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_priv4_images" type="text" id="bp_gallplus_max_priv4_images" value="<?php echo esc_attr( $bp_gallplus_max_priv4_images ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Max private images in each album', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_priv6_images" type="text" id="bp_gallplus_max_priv6_images" value="<?php echo esc_attr( $bp_gallplus_max_priv6_images ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Images per album page', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_per_page" type="text" id="bp_gallplus_per_page" value="<?php echo esc_attr( $bp_gallplus_per_page ); ?>" size="10" />
					</td>
				</tr>
			</table>

			<?php $styles = array(
				0 =>'fancybox (default)',
				1 =>'thickbox',
				2 =>'colorbox',
				3 =>'lightbox');?>
				
			
			<h3><?php _e('Lightbox Image Viewer', 'bp-galleries-plus' ) ?></h3>
			<p>
				<?php _e( "BP Gallery Plus comes with fancybox as the default viewer, though you can select one of the supported viewers as long as you have the correct plugin installed ", 'bp-galleries-plus' ) ?>
			</p>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label  for="target_uri"><?php _e('Select the image viewer', 'bp-galleries-plus') ?></label>
					</th>
					<td>
						<SELECT NAME="bp_gallplus_viewer" id="bp_gallplus_viewer">
						<?php 
							foreach($styles as $k => $str){
								if(($k > 0) && ($k == $bp_gallplus_viewer))
								{
									echo '<OPTION selected="selected" VALUE="'.$k.'">'.$str."\n";
								}
								else
								{
									echo '<OPTION VALUE="'.$k.'">'.$str."\n";
								}
							}?>
						</SELECT>
							<small><?php _e('The default viewer Fancybox is packaged with BP Gallery Plus and has been configure to display the mid-size image initially, and provive a button that will load the full sized image.') ?></small></label>
					</td> 
				</tr>
				<tr>
					<th scope="row"><label for="target_uri"><?php _e('Viewer Image Size') ?></label></th>
						<td>
							<label><input name="bp_gallplus_mid_size" type="checkbox" value="1" <?php if($bp_gallplus_mid_size) echo 'checked'; ?> />
							<small><?php _e('Enabling this option will, force the image viewer to load the mid size image instead of the large, which can speed up load time.') ?></small></label>
						</td>
				</tr>
				<tr>
					<th scope="row"><label for="target_uri"><?php _e('Use Text Watermark') ?></label></th>
						<td>
							<label><input name="bp_gallplus_use_watermark" type="checkbox" value="1" <?php if($bp_gallplus_use_watermark) echo 'checked'; ?> />
							<small><?php _e('Enabling this option will, force place a text watermark on large images when they are viewed. This will only work with the Fancybox viewer') ?></small></label>
						</td>
				</tr>
					<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Watermark Text', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_watermark_text" type="text" id="bp_gallplus_watermark_text" value="<?php echo esc_attr($bp_gallplus_watermark_text ); ?>" size="80" />
						<small><?php _e('Enter the text you would like to appear in the watermark.') ?></small></label>
					</td>
				</tr>
			</table>

			<h3><?php _e('Image Size Limits', 'bp-galleries-plus' ) ?></h3>

			<p>
			<?php _e( "Uploaded images will be re-sized to the values you set here. Values are for both X and Y size in pixels. We <i>strongly</i> suggest you keep the original image files so you can re-render your images during the upgrade process.", 'bp-galleries-plus' ) ?>
			</p>
			
			<table class="form-table">
			    <tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Maximum file (mb) size that can be uploaded', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_max_upload_size" type="text" id="bp_gallplus_max_upload_size" value="<?php echo esc_attr( $bp_gallplus_max_upload_size ); ?>" size="10" />
					</td> 
				</tr>
	              <tr>
					<th scope="row"><label for="target_uri"><?php _e('Album Image Size', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_middle_size" type="text" id="bp_gallplus_middle_size" value="<?php echo esc_attr( $bp_gallplus_middle_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><label for="target_uri"><?php _e('Thumbnail Image Size', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_thumb_size" type="text" id="bp_gallplus_thumb_size" value="<?php echo esc_attr( $bp_gallplus_thumb_size ); ?>" size="10" />
					</td>
				</tr>
                                <tr>
					<th scope="row"><?php _e('Keep original image files', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_keep_original" type="text" id="bp_gallplus_keep_original"<?php if ( $bp_gallplus_keep_original == true ) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_keep_original" type="text" id="bp_gallplus_keep_original"<?php if ($bp_gallplus_keep_original == false) : ?> checked="checked"<?php endif; ?> id="bp-disable-account-deletion" value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="target_uri"><?php _e('Keep files after deletion') ?></label></th>
						<td>
							<label><input name="bp_gallplus_keep_files" type="checkbox" value="1" <?php if($bp_gallplus_keep_files) echo 'checked'; ?> />
							<small><?php _e('Enabling this option will stop the image files from being deleted when a user deletes an album or image, only table entries will be removed.') ?></small></label>
						</td>
				</tr>

			</table>

			<h3><?php _e('Image URL Mapping', 'bp-galleries-plus' ) ?></h3>

			<p>
			<?php
			    _e( "If you get broken links when viewing images in bp-galleries-plus, it means your server is sending the wrong base URL to the plugin. You can use the image URL re-mapping function to fix this.",'bp-galleries-plus' );
			    echo "<a href='http://code.google.com/p/buddypress-media/wiki/UsingTheURLRemapper'> ";
			    _e("DOCUMENTATION",'bp-galleries-plus' );
			    echo "</a>";
			?>
			</p>

			<table class="form-table">
                                <tr>
					<th scope="row"><?php _e('Use image URL re-mapping', 'bp-galleries-plus' ) ?></th>
					<td>
						<input type="radio" name="bp_gallplus_url_remap" type="text" id="bp_gallplus_url_remap"<?php if ($bp_gallplus_url_remap == true ) : ?> checked="checked"<?php endif; ?>  value="1" /> <?php _e( 'Yes', 'bp-galleries-plus' ) ?> &nbsp;
						<input type="radio" name="bp_gallplus_url_remap" type="text" id="bp_gallplus_url_remap"<?php if ($bp_gallplus_url_remap == false) : ?> checked="checked"<?php endif; ?>  value="0" /> <?php _e( 'No', 'bp-galleries-plus' ) ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="target_uri"><?php _e('Base URL', 'bp-galleries-plus' ) ?></label></th>
					<td>
						<input name="bp_gallplus_base_url" type="text" id="bp_gallplus_base_url" value="<?php echo esc_attr( $bp_gallplus_base_url ); ?>" size="70" />
						/userID/filename.xxx
					</td>
				</tr>

			</table>

			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-galleries-plus' ) ?>"/>
			</p>

			<?php
			// This is very important, don't leave it out.
			wp_nonce_field( 'bpgpls-settings' );
			?>
		</form>
	</div>
<?php
}
function bpg_info_box() {
?>
	<div id="fb-info">
		<h3>Info</h3>
		<ul>
			<li><a href="http://www.amkd.com.au/wordpress/bp-gallery-plugin/98">BP Gallery Plus Home</a></li>
			<!--  li><a href="http://wordpress.org/tags/fotobook?forum_id=10">Support Forum</a></li -->
			<li><a href="http://www.fatcow.com/join/index.bml?AffID=642780">Host your Web site with FatCow!</a></li>
			<li><a href="http://www.amkd.com.au/">Need someone to build your web site or write a plugin?</a></li>
		</ul>



	</div>
<?php
}

?>