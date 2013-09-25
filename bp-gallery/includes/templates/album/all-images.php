<?php

	// To use this sub-template, just add "do_action( 'bp_gallplus_all_images' ); " to any template on your site

	global $bp;
	$args = array();

	$args['owner_id'] = false;	// Do *not* change this
	$args['id'] = false;		// Do *not* change this
	$args['page']=1;		// Do *not* change this
	$args['max']=false;		// Do *not* change this
	$args['privacy']='public';	// Do *not* change this
	$args['priv_override']=false;	// Do *not* change this
	$args['groupby']=false;	// Do *not* change this

	$args['per_page']=24;
	$args['ordersort']='DESC';
	$args['orderkey']='id';	// You can also use 'random' to shuffle images, but this will slow down your site
	$images_per_row = 5;


	// STEP 1: Run the query.
	bp_gallplus_query_images($args);

	$row_count = 0;

	// STEP 2: Check items were found. If so, open the CSS block.
	if ( bp_gallplus_has_images() ) : ?>

	    <div class="bpgpls-content-wrap" id="default">

		<table class="bpgpls-content-sitewide">
		    <tr>

		    <?php
		    // STEP 3: Iterate through the items the query has found, printing out each one.
		    while ( bp_gallplus_has_images() ) : bp_gallplus_the_image(); ?>

			<td>
				<a href="<?php bp_gallplus_image_url() ?>" class="media-image"><img src='<?php bp_gallplus_image_thumb_url() ?>' /></a>
			</td>
		    <?php

		    $row_count++;

		    if($row_count == $images_per_row){
			echo '</tr><tr>';
			$row_count = 0;
		    }

		    endwhile;

		 // STEP 4: Close the CSS block.   ?>
		    </tr>
		</table>
	    </div><?php

	 endif;

	 // This db call resets the images template after generating the all images block. If it is removed,
	 // and the all-images block is used on a page that contains a user image or gallery, the content
	 // after the all-images block will not render correctly.
	 
	 bp_gallplus_query_images();

?>