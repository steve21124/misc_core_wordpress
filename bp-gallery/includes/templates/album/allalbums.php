

			<div id="item-body">
				<h1> Latest Albums </h1>
<?php 		$album_args = array(
								'all_albums' => true,
								'orderkey' => 'date_created');
						bp_gallplus_query_albums($album_args);
					?>

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
	                <?php if (is_super_admin()) : ?>
										<div class="block-core-ItemLinks">
											<select onchange="var value = this.value; this.options[0].selected = true; eval(value)">
												<option value="">
													&laquo; album actions &raquo;
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
										</p>
											<div class="bpgpls-album-meta">                       
												<a href="#" class="like" id="bpgpls-like-album-<?php bp_gallplus_album_id() ?>" title="Like this album"><img src="<?php echo get_stylesheet_directory_uri() ?>/img/thumbsupsm.png" /></a>
												<div class="clear"></div>
							        </div>
										</div>
									<?php endif; ?>		
								</div>
					
							<?php endwhile; ?>
						</div>					
					
					<?php else : ?>
					
						<div id="message" class="info">
							<p><?php echo bp_word_or_name( __('No albums here, show something to the community!', 'bp-galleries-plus' ), __( "Either %s hasn't uploaded any image yet or they have restricted access", 'bp-galleries-plus' )  ,false,false) ?></p>
						</div>
				
					<?php endif; ?>

                  

			</div><!-- #item-body -->

