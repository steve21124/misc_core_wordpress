<?php 	get_header() ;
?>

	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>


			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>

  				<?php if ( bp_gallplus_has_albums() ) : ?>
						<div class="image-pagination">
							<?php bp_gallplus_album_pagination(); ?>	
						</div>			
					
						<div class="image-gallery">	
							<?php $count = 0; ?>											
							<?php while ( bp_gallplus_has_albums() ) : bp_gallplus_the_album(); ?>
								<div class="image-thumb-box">
	                <a href="<?php bp_gallplus_album_url() ?>" class="image-thumb"><img src='<?php echo bp_gallplus_get_album_feature_url() ?>' /></a>
	                <a href="<?php bp_gallplus_album_url() ?>"  class="image-title"><?php bp_gallplus_album_title() ?></a>	
	                <?php if (bp_is_my_profile() || is_super_admin()) : ?>
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; actions &raquo;
												</option>
												<option value="window.location = '<?php bp_gallplus_album_edit_url_stub()?>'">
													Edit Album
												</option>
												<option value="BPGPLSDeleteAlbum(<?php bp_gallplus_album_id() ?>,'<?php bp_gallplus_album_title()?>')">
													Delete Album
												</option>
											</select>
											<p>
											<?php bp_gallplus_album_time() ?>
											<?php bp_gallplus_total_album_image_count() ?> images </br>
											<?php bp_gallplus_album_priv_info() ?></br>
										</p>
										</div>
									<?php else : ?>
											<div class="block-core-ItemLinks">
												<p>
													By <?php bp_gallplus_album_get_owner_profile_link() ?>
											
												<?php bp_gallplus_album_created_time() ?></BR>
												<?php bp_gallplus_total_album_image_count() ?> images </br>
												<!-- div class="bpgpls-album-meta" -->                       
												<?php 	bp_gallplus_like_button( bp_gallplus_get_album_id(), 'album' ); ?>
													<!-- div class="clear"></div>
							        	</div-->
												</p>
											</div>
									<?php endif; ?>		
								</div>
					
							<?php endwhile; ?>
						</div>					
					
					<?php else : ?>
					
						<div id="message" class="info">
							<p><?php echo bp_word_or_name( __('No galleries here, show something to the community!', 'bp-galleries-plus' ), __( "Either %s hasn't uploaded any image yet or they have restricted access", 'bp-galleries-plus' )  ,false,false) ?></p>
						</div>
				
					<?php endif; ?>

                  

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>

  <!-- div id="loadmask"style="text-align: center;">
  	<img src="<?php echo plugins_url( 'images/loadspinner.gif' , dirname(__FILE__) );?>" border="0" style="text-decoration: none" align="middle" alt="Loading" />
	</div -->
