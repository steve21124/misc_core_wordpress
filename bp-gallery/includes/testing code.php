<?php

bp_core_new_nav_item( array(
		'name' => $nav_item_name,
		'slug' => $bp->album->slug,
		'position' => 80,
		'screen_function' => 'bp_gallplus_screen_images',
		'default_subnav_slug' => $bp->album->image_slug,
		'show_for_displayed_user' => true
		
		//////////////////////////////////////

bp_core_new_subnav_item( array(
		'name' => $album_link_title,
		'slug' => $bp->album->album_slug,
		'parent_slug' => $bp->album->slug,
		'parent_url' => $album_link,
		'screen_function' => 'bp_gallplus_show_albums',
		'position' => 30,