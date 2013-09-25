<?php get_header() ?>

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

				<?php if ( bp_gallplus_has_images() ) : ?>
					
					<div class="image-pagination">
						<?php bp_gallplus_album_content_pagination(); ?>	
					</div>			
					<table "width=100%">
						<tr>
							<td width="70%">
								<h1><?php bp_gallplus_image_album_title()?></h1>
							</td>
							<td width="30%">
								<table "width=100%">
									<tr>
										<td>
											<?php 	bp_gallplus_like_button( bp_gallplus_get_album_id(), 'album' ); ?> Like this gallery
										</td>
									</tr>
									<tr>
										<td>
											<?php 	bp_gallplus_donate_button( bp_gallplus_get_album_id(), 'album' ); ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
								
					<p><?php bp_gallplus_image_album_desc()?></p>
					
					<div class="image-gallery">												
						<?php while ( bp_gallplus_has_images() ) : bp_gallplus_the_image(); ?>

							<div class="image-thumb-box">
            		<a href="<?php get_option('bp_gallplus_mid_size') ? bp_gallplus_image_mid_url() : bp_gallplus_image_url() ?>" <?php bp_gallplus_image_viewer_attribute() ?>><img src='<?php bp_gallplus_image_thumb_url() ?>' /></a>
	                <!-- a href="<?php bp_gallplus_image_url() ?>"  class="image-title"><?php bp_gallplus_image_title_truncate() ?></a -->	
	                <?php if (bp_is_my_profile() || is_super_admin()) : ?>
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; image actions &raquo;
												</option>
												<option value="window.location = '<?php bp_gallplus_image_edit_url()?>'">
													Edit Image
												</option>
												<option value="BPGPLSFeatureImage(<?php bp_gallplus_album_id() ?>,'<?php bp_gallplus_album_title()?>',<?php bp_gallplus_image_id() ?>)">
													Feature Image
												</option>
												<option value="BPGPLSDeleteImage(<?php bp_gallplus_image_id() ?>)">
													Delete Image
												</option>
											</select>
										</div>

									<?php endif; ?>		
							</div>
					
						<?php endwhile; ?>
					</div>					
				<?php else : ?>					
					<div id="message" class="info">
						<p><?php echo bp_word_or_name( __('No pics here, show something to the community!', 'bp-galleries-plus' ), __( "Either %s hasn't uploaded any image yet or they have restricted access", 'bp-galleries-plus' )  ,false,false) ?></p>
					</div>				
				<?php endif; ?>

			</div><!-- #item-body -->
				<?php bp_gallplus_load_subtemplate( apply_filters( 'bp_gallplus_template_screen_comments', 'album/albumcomments' ) ); ?>

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>
