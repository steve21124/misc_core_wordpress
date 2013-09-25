<?php

/**
 * Example use in the template file:
 *
 * 	<?php if ( bp_gallplus_has_images() ) : ?>
 *
 *		<?php while ( bp_gallplus_has_images() ) : bp_gallplus_the_image(); ?>
 *
 *			<a href="<?php bp_gallplus_image_url() ?>">
 *				<img src='<?php bp_gallplus_image_thumb_url() ?>' />
 *			</a>
 *
 *		<?php endwhile; ?>
 *
 *	<?php else : ?>
 *
 *		<p class="error">No Pics!</p>
 *
 *	<?php endif; ?>
 */
class BP_Gallplus_Template {
    
	var $current_image = -1;
	var $image_count = 0;
	var $images;
	var $image;

	var $in_the_loop;

	var $pag_page;
	var $pag_per_page;
	var $pag_links;
	var $album_pag_links;
	var $group_album_pag_links;
	var $pag_links_global;
	var $album_id;
	
	function BP_Gallplus_Template( $args = '' ) {
		$this->__construct( $args);
	}
	
	function __construct( $args = '' ) {
		global $bp;

		
		$defaults = bp_gallplus_default_query_args();
		$r = apply_filters('bp_gallplus_template_args',wp_parse_args( $args, $defaults ));
//bp_logdebug('BP_Gallplus_Template : '.print_r($r,true));

		extract( $r , EXTR_SKIP);

		$this->pag_page = $page;
		$this->pag_per_page = $per_page;
		$this->owner_id = $owner_id;
		$this->privacy= $privacy;
		$this->album_id = $album_id;
		if ($this->privacy == 'group_gallery')
		{
			$r['owner_id']=false;
		}

		$total = bp_gallplus_get_image_count($r);
		$this->images = bp_gallplus_get_images($r);

		if ( !$max || $max >= $total )
			$this->total_image_count = $total;
		else
			$this->total_image_count = $max;

		if ( !$max || $max >= count($this->images))
			$this->image_count = count($this->images);
		else
			$this->image_count = $max;
		
		
		$this->pag_links_global = paginate_links( array(
			'base' => get_permalink() . '%#%',
			'format' => '?page=%#%',
			'total' => ceil( (int) $this->total_image_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		    
		));
						
		$this->album_pag_links = paginate_links( array(
			'base' => $bp->displayed_user->domain . $bp->album->slug .'/'. $bp->album->album_slug .'/'.$this->album_id.'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_image_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
		$this->group_album_pag_links = paginate_links( array(
			'base' => bp_gallplus_get_group_album_url().'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_image_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));

		$this->pag_links = paginate_links( array(
			'base' => $bp->displayed_user->domain . $bp->album->slug .'/'. $bp->album->images_slug .'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_image_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
		
		if ($this->image_count)
			$this->image = $this->images[0];
		
	}
	
	function has_images() {
		if ( $this->current_image + 1 < $this->image_count ) {
			return true;
		} elseif ( $this->current_image + 1 == $this->image_count && $this->image_count > 0) {
			do_action('bp_gallplus_loop_end');

			$this->rewind_images();
		}

		$this->in_the_loop = false;
		return false;
	}

	function next_image() {
		$this->current_image++;
		$this->image = $this->images[$this->current_image];

		return $this->image;
	}

	function rewind_images() {
		$this->current_image = -1;
		if ( $this->image_count > 0 ) {
			$this->image = $this->images[0];
		}
	}

	function the_image($id=0) {
		global $image, $bp;

		if($id != 0) 
		{
			for($i=0; $i< $this->image_count; $i++)
			{
				if($this->images[$i]->id == $id)
				{
					$this->current_image = $i;
					$this->image = $this->images[$i];
					break;
				}
			}
		}
		else
		{
	$this->in_the_loop = true;
		$this->image = $this->next_image();

		if ( 0 == $this->current_image )
			do_action('bp_gallplus_loop_start');
		}
	}
	
	function has_next_pic(){
		if (!isset($this->image->next_pic))
		{
			$pic_args = array(
			'id' => $this->image->id);

			$this->image->next_pic = bp_gallplus_get_next_image($pic_args);
		}
		if (isset($this->image->next_pic) && $this->image->next_pic !== false)
			return true;
		if (isset($this->image->next_pic) && $this->image->next_pic === false)
			return false;
		
	}
	function has_prev_pic(){
	if (!isset($this->image->prev_pic))
	{
					$pic_args = array('id' => $this->image->id);
			$this->image->prev_pic = bp_gallplus_get_prev_image($pic_args);
	}
		if (isset($this->image->prev_pic) && $this->image->prev_pic !== false)
			return true;
		if (isset($this->image->prev_pic) && $this->image->prev_pic === false)
			return false;
	}
} //** End class BP_Gallplus_Template

function bp_gallplus_query_images( $args = '' ) {
    
	global $images_template;
//bp_logdebug('bp_gallplus_query_images : '.print_r($args,true));

	$images_template = new BP_Gallplus_Template( $args );

	return $images_template->has_images();
}

function bp_gallplus_the_image($id = 0) {
    
	global $images_template;
	return $images_template->the_image($id);
}

/**
 * bp_gallplus_has_images()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_images() {
    
	global $images_template;
	return $images_template->has_images();
}

/**
 * bp_gallplus_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_title() {
	echo bp_gallplus_get_image_title();
}
	function bp_gallplus_get_image_title() {
	    
		global $images_template;
		return apply_filters( 'bp_gallplus_get_image_title', $images_template->image->title);
	}

/**
 * bp_gallplus_image_title_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_title_truncate($length = 11) {
	echo bp_gallplus_get_image_title_truncate($length);
}	
	function bp_gallplus_get_image_title_truncate($length) {

		global $images_template;

		$title = $images_template->image->title;

		$title = apply_filters( 'bp_gallplus_get_image_title_truncate', $title);

		$r = wp_specialchars_decode($title, ENT_QUOTES);


		if ( function_exists('mb_strlen') && strlen($r) > mb_strlen($r) ) {

			$length = round($length / 2);
		}

		if ( function_exists( 'mb_substr' ) ) {


			$r = mb_substr($r, 0, $length);
		}
		else {
			$r = substr($r, 0, $length);
		}

		$result = _wp_specialchars($r) . '&#8230;';

		return $result;
		
	}
/**
 * bp_gallplus_image_image_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_album_title() {
	echo bp_gallplus_get_image_album_title();
}
	function bp_gallplus_get_image_album_title() {
	    
		global $bp, $wpdb, $images_template;
		$sql = $wpdb->prepare( "SELECT title FROM {$bp->album->albums_table_name} WHERE id = %d", $images_template->image->album_id );
		$title = $wpdb->get_var( $sql );

		return apply_filters( 'bp_gallplus_get_image_title', $title);
	}
/**
 * bp_gallplus_image_image_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_album_desc() {
	echo bp_gallplus_get_image_album_desc();
}
	function bp_gallplus_get_image_album_desc() {
	    
		global $bp, $wpdb, $images_template;
		$sql = $wpdb->prepare( "SELECT description FROM {$bp->album->albums_table_name} WHERE id = %d", $images_template->image->album_id );
		$title = $wpdb->get_var( $sql );

		return apply_filters( 'bp_gallplus_get_image_title', $title);
	}

/**
 * bp_gallplus_image_desc()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_desc() {
	echo bp_gallplus_get_image_desc();
}
	function bp_gallplus_get_image_desc() {
	    
		global $images_template;
		
		return apply_filters( 'bp_gallplus_get_image_desc', $images_template->image->description );
	}
	
/**
 * bp_gallplus_image_desc_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_desc_truncate($words=55) {
	echo bp_gallplus_get_image_desc_truncate($words);
}
	function bp_gallplus_get_image_desc_truncate($words=55) {
	    
		global $images_template;
		
		$exc = bp_create_excerpt($images_template->image->description, $words, true) ;
		
		return apply_filters( 'bp_gallplus_get_image_desc_truncate', $exc, $images_template->image->description, $words );
	}

/**
 * bp_gallplus_image_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_id() {
	echo bp_gallplus_get_image_id();
}
	function bp_gallplus_get_image_id() {
	    
		global $images_template;
		
		return apply_filters( 'bp_gallplus_get_image_id', $images_template->image->id );
	}
/**
 * bp_gallplus_image_image_album_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_album_id() {
	echo bp_gallplus_get_image_album_id();
}
	function bp_gallplus_get_image_album_id() {
	    
		global $bp, $wpdb, $images_template;

		return apply_filters( 'bp_gallplus_get_image_id', $images_template->image->album_id);
	}

/**
 * bp_gallplus_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_url() {
	echo bp_gallplus_get_image_url();
}
	function bp_gallplus_get_image_url() {
	    
		global $bp,$images_template;

		$owner_domain = bp_core_get_user_domain($images_template->image->owner_id);
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$images_template->image->id  . '/');
	  return apply_filters( 'bp_gallplus_get_image_thumb_url', bp_get_root_domain().$images_template->image->pic_org_url );
	}
/**
 * bp_gallplus_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_mid_url() {
	echo bp_gallplus_get_image_mid_url();
}
	function bp_gallplus_get_image_mid_url() {
	    
		global $bp,$images_template;

		$owner_domain = bp_core_get_user_domain($images_template->image->owner_id);
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$images_template->image->id  . '/');
	  return apply_filters( 'bp_gallplus_get_image_thumb_url', bp_get_root_domain().$images_template->image->pic_mid_url );
	}


/**
 * bp_gallplus_image_edit_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_edit_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_gallplus_get_image_edit_url().'" class="image-edit">'.__('Edit image','bp-galleries-plus').'</a>';
}

/**
 * bp_gallplus_image_edit_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_edit_url() {
	echo bp_gallplus_get_image_edit_url();
}
	function bp_gallplus_get_image_edit_url() {
	    
		global $bp,$images_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_edit_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_edit_slug.'/'.$images_template->image->id.'/'.$bp->album->edit_slug),'bp-galleries-plus-edit-pic');
//			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_edit_url_stub', $bp->album->single_edit_slug.'/'.$images_template->image->id.'/'.$bp->album->edit_slug).'/'.$images_template->image->album_id,'bp-galleries-plus-edit-pic');
	}
/**
 * bp_gallplus_image_edit_url_stub()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_edit_url_stub() {
	echo bp_gallplus_get_image_edit_url_stub();
}
	function bp_gallplus_get_image_edit_url_stub() {
	    
		global $bp,$images_template;
		
		if (bp_is_my_profile() || is_super_admin())
		{
//			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_edit_url_stub', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$images_template->image->id.'/'.$bp->album->edit_slug),'bp-galleries-plus-edit-pic');
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_edit_url_stub', $bp->album->single_edit_slug.'/'.$images_template->image->id.'/'.$bp->album->edit_slug).'/'.$images_template->image->album_id,'bp-galleries-plus-edit-pic');
//				return wp_nonce_url(apply_filters( 'bp_gallplus_get_album_edit_url_stub',$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-galleries-plus-edit-album');
		}
}
/**
 * bp_gallplus_album_priv()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_priv() {
	echo bp_gallplus_get_image_priv();
}
	function bp_gallplus_get_image_priv() {
	    
		global $images_template;
		
		return apply_filters( 'bp_gallplus_get_album_priv', $images_template->image->privacy );
	}

/**
 * bp_gallplus_image_delete_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_delete_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_gallplus_get_image_delete_url().'" class="image-delete">'.__('Delete image','bp-galleries-plus').'</a>';
}

/**
 * bp_gallplus_image_delete_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_delete_url() {
	echo bp_gallplus_get_image_delete_url();
}
	function bp_gallplus_get_image_delete_url() {
	    
		global $bp,$images_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_delete_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$images_template->image->id.'/'.$bp->album->delete_slug ),'bp-galleries-plus-delete-pic');
	}

/**
 * bp_gallplus_image_original_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_original_url() {
	echo bp_gallplus_get_image_original_url();
}
	function bp_gallplus_get_image_original_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $images_template->image->pic_org_url, strrpos($images_template->image->pic_org_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_original_url', bp_get_root_domain().$images_template->image->pic_org_url );
		}
		
	}

/**
 * bp_gallplus_image_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_middle_url() {
	echo bp_gallplus_get_image_middle_url();
}
	function bp_gallplus_get_image_middle_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $images_template->image->pic_mid_url, strrpos($images_template->image->pic_mid_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_middle_url', bp_get_root_domain().$images_template->image->pic_mid_url );
		}
	}

/**
 * bp_gallplus_image_thumb_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_thumb_url() {
	echo bp_gallplus_get_image_thumb_url();
}
	function bp_gallplus_get_image_thumb_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){
		    $filename = substr( $images_template->image->pic_thumb_url, strrpos($images_template->image->pic_thumb_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_thumb_url', bp_get_root_domain().$images_template->image->pic_thumb_url );
		}
	}

/**
 * bp_gallplus_total_image_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_total_image_count() {
	echo bp_gallplus_get_total_image_count();
}
	function bp_gallplus_get_total_image_count() {
	    
		global $images_template;
		
		return apply_filters( 'bp_gallplus_get_total_image_count', $images_template->total_image_count );
	}

/**
 * bp_gallplus_image_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_pagination($always_show = false) {
	echo bp_gallplus_get_image_pagination($always_show);
}
	function bp_gallplus_get_image_pagination($always_show = false) {
	    
		global $images_template;
		
		if ($always_show || $images_template->total_image_count > $images_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination', $images_template->pag_links );
	}

/**
 * bp_gallplus_image_pagination_global()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_pagination_global($always_show = false) {
	echo bp_gallplus_get_image_pagination_global($always_show);
}
	function bp_gallplus_get_image_pagination_global($always_show = false) {
	    
		global $images_template;
		
		if ($always_show || $images_template->total_image_count > $images_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination_global', $images_template->pag_links_global );
	}
/**
 * bp_gallplus_album_content_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_content_pagination($always_show = false) {
	echo bp_gallplus_get_album_content_pagination($always_show);
}
	function bp_gallplus_get_album_content_pagination($always_show = false) {
	    
		global $images_template;
		
		if ($always_show || $images_template->total_image_count > $images_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination', $images_template->album_pag_links );
	}
	
/**
 * bp_gallplus_group_album_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_group_album_pagination($always_show = false) {
	echo bp_gallplus_get_group_album_pagination($always_show);
}
	function bp_gallplus_get_group_album_pagination($always_show = false) {
	    
		global $bp, $images_template;
		
		$group_id = $images_template->images[0]->group_id;		
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		$images_template->group_album_pag_links = paginate_links( array(
			'base' => '/'.$bp->groups->slug .'/'. $group->slug .'/gallery/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $images_template->total_image_count / (int) $images_template->pag_per_page ),
			'current' => (int) $images_template->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));		
		if ($always_show || $images_template->total_image_count > $images_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination', $images_template->group_album_pag_links );
	}

/**
 * bp_gallplus_adjacent_links()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_gallplus_adjacent_links() {
	echo bp_gallplus_get_adjacent_links();
}
	function bp_gallplus_get_adjacent_links() {
	    
		global $images_template;
		
		if ($images_template->has_prev_pic() || $images_template->has_next_pic())
			return bp_gallplus_get_prev_image_or_album_link().' '.bp_gallplus_get_next_image_or_album_link();
		else
			return '<a href="'.bp_gallplus_get_images_url().'" class="image-album-link image-no-adjacent-link"><span>'.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_gallplus_next_image_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_image_link($text = ' &raquo;', $title = true) {
	echo bp_gallplus_get_next_image_link($text, $title);
}
	function bp_gallplus_get_next_image_link($text = ' &raquo;', $title = true) {
	    
		global $images_template;
		
		if ($images_template->has_next_pic()){
			$text = ( ($title)?bp_gallplus_get_next_image_title():'' ).$text;
			return '<a href="'.bp_gallplus_get_next_image_url().'" class="image-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return null;
	}

/**
 * bp_gallplus_next_image_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_image_or_album_link($text = ' &raquo;', $title = true) {
	echo bp_gallplus_get_next_image_or_album_link($text, $title);
}
	function bp_gallplus_get_next_image_or_album_link($text = ' &raquo;', $title = true) {
	    
		global $images_template;
		
		if ($images_template->has_next_pic()){
			$text = ( ($title)?bp_gallplus_get_next_image_title():'' ).$text;
			return '<a href="'.bp_gallplus_get_next_image_url().'" class="image-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return '<a href="'.bp_gallplus_get_images_url().'" class="image-album-link image-next-link"> <span> '.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span></a>';
	}

/**
 * bp_gallplus_next_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_image_url() {
	echo bp_gallplus_get_next_image_url();
}
	function bp_gallplus_get_next_image_url() {
	    
		global $bp,$images_template;
		
		if ($images_template->has_next_pic())
			return apply_filters( 'bp_gallplus_get_next_image_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_edit_slug.'/'.$images_template->image->next_pic->id  . '/');
	}

/**
 * bp_gallplus_next_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_image_title() {
	echo bp_gallplus_get_next_image_title();
}
	function bp_gallplus_get_next_image_title() {
	    
		global $images_template;
		
		if ($images_template->has_next_pic())
			return apply_filters( 'bp_gallplus_get_image_title', $images_template->image->next_pic->title );
	}
	
/**
 * bp_gallplus_has_next_image()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_next_image() {
    
	global $bp,$images_template;
	
	return $images_template->has_next_pic();
}

/**
 * bp_gallplus_prev_image_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_image_link($text = '&laquo; ', $title = true) {
	echo bp_gallplus_get_prev_image_link($text, $title);
}
	function bp_gallplus_get_prev_image_link($text = '&laquo; ', $title = true) {
	    
		global $images_template;
		
		if ($images_template->has_prev_pic()){
			$text .= ($title)?bp_gallplus_get_prev_image_title():'';
			return '<a href="'.bp_gallplus_get_prev_image_url().'" class="image-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return null;
	}

/**
 * bp_gallplus_prev_image_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_image_or_album_link($text = '&laquo; ', $title = true) {
	echo bp_gallplus_get_prev_image_or_album_link($text, $title);
}
	function bp_gallplus_get_prev_image_or_album_link($text = '&laquo; ', $title = true) {
	    
		global $images_template;
		if ($images_template->has_prev_pic()){
			$text .= ($title)?bp_gallplus_get_prev_image_title():'';
			return '<a href="'.bp_gallplus_get_prev_image_url().'" class="image-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return '<a href="'.bp_gallplus_get_images_url().'" class="image-album-link image-prev-link"><span> '.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_gallplus_prev_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_image_url() {
	echo bp_gallplus_get_prev_image_url();
}
	function bp_gallplus_get_prev_image_url() {
	    
		global $bp,$images_template;
		
		if ($images_template->has_prev_pic())
			return apply_filters( 'bp_gallplus_get_prev_image_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_edit_slug.'/'.$images_template->image->prev_pic->id . '/');
	}

/**
 * bp_gallplus_prev_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_image_title() {
	echo bp_gallplus_get_prev_image_title();
}
	function bp_gallplus_get_prev_image_title() {
	    
		global $images_template;
		
		if ($images_template->has_prev_pic())
			return apply_filters( 'bp_gallplus_get_image_title', $images_template->image->prev_pic->title );
	}
	
/**
 * bp_gallplus_has_prev_image()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_prev_image() {
    
	global $images_template;
	
	return $images_template->has_prev_pic();
}

/**
 * bp_gallplus_images_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_images_url() {
    
	echo bp_gallplus_get_images_url();
	
}
	function bp_gallplus_get_images_url() {
	    
		global $bp;
			return apply_filters( 'bp_gallplus_get_images_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->images_slug . '/');
	}

/**
 * bp_gallplus_image_has_activity()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_gallplus_image_has_activity(){

	global $bp,$images_template;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}

	return bp_has_activities( array('object'=> $bp->album->id,'primary_id'=>$images_template->image->id , 'show_hidden' => true) );
}

/**
 * bp_gallplus_comments_enabled()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_comments_enabled() {
    
        global $bp;

        return $bp->album->bp_gallplus_enable_comments;
	}
/**
 * bp_gallplus_image_group_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_image_group_id() {
	echo bp_gallplus_get_image_group_id();
}
	function bp_gallplus_get_image_group_id() {
	    
		global $images_template;
		return apply_filters( 'bp_gallplus_get_group_id', $images_template->image->group_id );
	}
	
/**
 * bp_gallplus_image_owner_profile_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
 function bp_gallplus_image_owner_profile_link() {
       echo bp_gallplus_image_get_owner_profile_link();
 }
 
 
function bp_gallplus_image_get_owner_profile_link() {
   global $bp, $images_template;
   
   return apply_filters( 'bp_get_member_permalink', bp_core_get_userlink( $images_template->image->owner_id, false, false, true ) );
}

class BP_Gallplus_Album_Template {
    
	var $current_album = -1;
	var $album_count = 0;
	var $albums;
	var $album;

	var $in_the_loop;

	var $pag_page;
	var $pag_per_page;
	var $pag_links;
	var $pag_links_global;
	var $pag_links_stub;

	
	function BP_Gallplus_Album_Template( $args = '' ) {
		$this->__construct( $args);
	}
	
	function __construct( $args = '' ) {
		global $bp;

	
		$defaults = bp_gallplus_default_query_args();
		
		$r = apply_filters('bp_gallplus_template_args',wp_parse_args( $args, $defaults ));
		extract( $r , EXTR_SKIP);

		$this->pag_page = $page;
		$this->pag_per_page = $per_page;
		if((isset($all_albums)) && ($all_albums))
		{
			$this->owner_id = false;
		}
		else
		{
			$this->owner_id = $owner_id;
		} 
		$this->privacy= $privacy;

		$total = bp_gallplus_get_album_count($r);
		$this->albums = bp_gallplus_get_albums($r);

		if ( !$max || $max >= $total )
			$this->total_album_count = $total;
		else
			$this->total_album_count = $max;

		if ( !$max || $max >= count($this->albums))
			$this->album_count = count($this->albums);
		else
			$this->album_count = $max;
		
		
		$this->pag_links_global = paginate_links( array(
			'base' => get_permalink() . '%#%',
			'format' => '?page=%#%',
			'total' => ceil( (int) $this->total_album_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		    
		));
						
		$this->pag_links = paginate_links( array(
			'base' => $bp->displayed_user->domain . $bp->album->slug .'/'. $bp->album->album_slug .'/%_%',
			'format' => '%#%',
			'total' => ceil( (int) $this->total_album_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		));
			$this->pag_links_stub = paginate_links( array(
			'base' => '?page=%#%',
			'format' => '?page=%#%',
			'total' => ceil( (int) $this->total_album_count / (int) $this->pag_per_page ),
			'current' => (int) $this->pag_page,
			'prev_text' => '&larr;',
			'next_text' => '&rarr;',
			'mid_size' => 1
		    
		));
	
		if ($this->album_count)
			$this->album = $this->albums[0];
		
	}
	
	function has_albums() {
		if ( $this->current_album + 1 < $this->album_count ) {
			return true;
		} elseif ( $this->current_album + 1 == $this->album_count && $this->album_count > 0) {
			do_action('bp_gallplus_loop_end');

			$this->rewind_albums();
		}

		$this->in_the_loop = false;
		return false;
	}

	function next_album() {
		$this->current_album++;
		$this->album = $this->albums[$this->current_album];

		return $this->album;
	}

	function rewind_albums() {
		$this->current_album = -1;
		if ( $this->album_count > 0 ) {
			$this->album = $this->albums[0];
		}
	}

	function the_album($id=0) {
		global $image, $bp;
		if($id != 0) 
		{
			$this->current_album = $id;
			for($i=0; $i< $this->album_count; $i++)
			{
				if($this->albums[$i]->id == $id)
				{
					$this->album = $this->albums[$i];
					break;
				}
			}
		}
		else
		{
			$this->in_the_loop = true;
			$this->album = $this->next_album();
			if ( 0 == $this->current_album )
				do_action('bp_gallplus_loop_start');
		}
	}
	
	function has_next_album(){
		if (!isset($this->album->next_album))
			$this->album->next_album = bp_gallplus_get_next_album();
		if (isset($this->album->next_album) && $this->album->next_album !== false)
			return true;
		if (isset($this->album->next_album) && $this->album->next_album === false)
			return false;
		
	}
	function has_prev_album(){
		if (!isset($this->album->prev_album))
			$this->image->prev_album = bp_gallplus_get_prev_album();
		if (isset($this->album->prev_album) && $this->album->prev_album !== false)
			return true;
		if (isset($this->album->prev_album) && $this->album->prev_album === false)
			return false;
	}
} // END class BP_Gallplus_Album_Template

function bp_gallplus_query_albums( $args = '' ) {
    
	global $albums_template;

	$albums_template = new BP_Gallplus_Album_Template( $args );

	return $albums_template->has_albums();
}

function bp_gallplus_the_album($id = 0) {
    
	global $albums_template;
	return $albums_template->the_album($id);
}

/**
 * bp_gallplus_has_albums()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_albums() {
    
	global $albums_template;
	return $albums_template->has_albums();
}

/**
 * bp_gallplus_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_title() {
	echo bp_gallplus_get_album_title();
}
	function bp_gallplus_get_album_title() {
	    
		global $albums_template;
		if((isset($albums_template->album->title)) && (strlen($albums_template->album->title) > 0))
		{
			return apply_filters( 'bp_gallplus_get_image_title', $albums_template->album->title);
		}
		else
		{
			return apply_filters( 'bp_gallplus_get_image_title', 'Untitled');
		}	
	}

/**
 * bp_gallplus_album_owner_user_name()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_owner_user_name() {
	echo bp_gallplus_album_get_owner_user_name();
}
function bp_gallplus_album_get_owner_user_name()
{
	global $bp,$albums_template;
		return apply_filters( 'bp_gallplus_get_image_title', bp_core_get_username( $albums_template->album->owner_id ));
}

 function bp_gallplus_album_get_owner_profile_link() {
       echo bp_gallplus_album_owner_profile_link();
 }
function bp_gallplus_album_owner_profile_link() {
   global $bp, $albums_template;
   
   return apply_filters( 'bp_get_member_permalink', bp_core_get_userlink( $albums_template->album->owner_id, false, false, true ) );
}

/**
 * bp_gallplus_image_title_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_title_truncate($length = 11) {
	echo bp_gallplus_get_album_title_truncate($length);
}	
	function bp_gallplus_get_album_title_truncate($length) {

		global $albums_template;

		$title = $albums_template->album->title;

		$title = apply_filters( 'bp_gallplus_get_image_title_truncate', $title);

		$r = wp_specialchars_decode($title, ENT_QUOTES);


		if ( function_exists('mb_strlen') && strlen($r) > mb_strlen($r) ) {

			$length = round($length / 2);
		}

		if ( function_exists( 'mb_substr' ) ) {


			$r = mb_substr($r, 0, $length);
		}
		else {
			$r = substr($r, 0, $length);
		}

		$result = _wp_specialchars($r) . '&#8230;';

		return $result;
		
	}

/**
 * bp_gallplus_image_desc()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_desc() {
	echo bp_gallplus_get_album_desc();
}
	function bp_gallplus_get_album_desc() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_gallplus_get_album_desc', $albums_template->album->description );
	}
	
/**
 * bp_gallplus_image_desc_truncate()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_desc_truncate($words=55) {
	echo bp_gallplus_get_album_desc_truncate($words);
}
	function bp_gallplus_get_album_desc_truncate($words=55) {
	    
		global $albums_template;
		
		$exc = bp_create_excerpt($images_template->album->description, $words, true) ;
		
		return apply_filters( 'bp_gallplus_get_image_desc_truncate', $exc, $albums_template->album->description, $words );
	}

/**
 * bp_gallplus_album_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_id() {
	echo bp_gallplus_get_album_id();
}
	function bp_gallplus_get_album_id() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_gallplus_get_album_id', $albums_template->album->id );
	}

/**
 * bp_gallplus_album_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_url() {
	echo bp_gallplus_get_album_url();
}
	function bp_gallplus_get_album_url() {
	    
		global $bp,$albums_template;

		$owner_domain = bp_core_get_user_domain($albums_template->album->owner_id);
		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/'.$bp->album->album_slug.'/'.$albums_template->album->id  . '/');
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/'.$albums_template->album->id  . '/');
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/?album_id='.$albums_template->album->id);
	}
/**
 * bp_gallplus_album_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_group_album_url() {
	echo bp_gallplus_get_group_album_url();
}
	function bp_gallplus_get_group_album_url() {
	    
		global $bp,$albums_template;

		$group_id = $albums_template->album->group_id;
		$group = groups_get_group( array( 'group_id' => $group_id ) );
		return apply_filters( 'bp_gallplus_get_image_url', $bp->groups->slug . '/' . $group->slug.'/gallery/');
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/'.$albums_template->album->id  . '/');
//		return apply_filters( 'bp_gallplus_get_image_url', $owner_domain . $bp->album->slug . '/?album_id='.$albums_template->album->id);
	}

/**
 * bp_gallplus_album_edit_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_edit_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_gallplus_get_album_edit_url().'" class="album-edit">'.__('Edit album','bp-galleries-plus').'</a>';
}

/**
 * bp_gallplus_image_edit_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_edit_url() {
	echo bp_gallplus_get_album_edit_url();
}
	function bp_gallplus_get_album_edit_url() {
	    
		global $bp,$albums_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_album_edit_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->album_slug.'/'.$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-galleries-plus-edit-album');
	}
function bp_gallplus_album_edit_url_stub() {
	echo bp_gallplus_get_album_edit_url_stub();
}
	function bp_gallplus_get_album_edit_url_stub() {
	    
		global $bp,$albums_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_album_edit_url_stub',$albums_template->album->id.'/'.$bp->album->edit_slug),'bp-galleries-plus-edit-album');
	}
/**
 * bp_gallplus_album_priv()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_priv() {
	echo bp_gallplus_get_album_priv();
}
	function bp_gallplus_get_album_priv() {
	    
		global $albums_template;
		
		return apply_filters( 'bp_gallplus_get_album_priv', $albums_template->album->privacy );
	}
/**
 * bp_gallplus_album_priv_info()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_priv_info() {
	echo bp_gallplus_get_album_priv_info();
}
	function bp_gallplus_get_album_priv_info() {
	    
		global $albums_template;
	
		$loc_priv = $albums_template->album->privacy;
		switch($loc_priv)
		{
			case 0: $loc_priv_txt = 'Public'; break;
			case 2: $loc_priv_txt = 'Registered members'; break;
			case 5: $loc_priv_txt = 'Group Gallery'; break;
			case 3: $loc_priv_txt = 'Group Members';
/*					$groups_array = BP_Groups_Member::get_is_admin_of($albums_template->album->owner_id);
					$group_count = $groups_array['total'];
 					foreach( $groups_array['groups'] as $group)
					{
						if(( $albums_template->album->group_id > 0) && ($group->id ==  $albums_template->album->group_id))
						{
							$loc_priv_txt = 'Members of Group "'.$group->name.'"';
						}
					} */
					break;
			case 4: $loc_priv_txt = 'Only friends'; break;
			case 6: $loc_priv_txt = 'Private'; break;
			default : $loc_priv_txt = $loc_priv;break;
		}
		return apply_filters( 'bp_gallplus_get_album_priv', $loc_priv_txt );
	}
	
/**
 * bp_gallplus_album_group_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_group_id() {
	echo bp_gallplus_get_group_id();
}
	function bp_gallplus_get_group_id() {
	    
		global $albums_template;
		return apply_filters( 'bp_gallplus_get_group_id', $albums_template->album->group_id );
	}


/**
 * bp_gallplus_album_id()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
//function bp_gallplus_album_id() {
//	echo bp_gallplus_get_album_id();
//}
//	function bp_gallplus_get_album_id() {
	    
//		global $albums_template;
		
//		return apply_filters( 'bp_gallplus_get_album_priv', $albums_template->album->id );
//	}


/**
 * bp_gallplus_image_delete_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*function bp_gallplus_image_delete_link() {
	if (bp_is_my_profile() || is_super_admin())
		echo '<a href="'.bp_gallplus_get_image_delete_url().'" class="image-delete">'.__('Delete image','bp-galleries-plus').'</a>';
}
*/
/**
 * bp_gallplus_image_delete_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
 /*
function bp_gallplus_image_delete_url() {
	echo bp_gallplus_get_image_delete_url();
}
	function bp_gallplus_get_image_delete_url() {
	    
		global $bp,$images_template;
		
		if (bp_is_my_profile() || is_super_admin())
			return wp_nonce_url(apply_filters( 'bp_gallplus_get_image_delete_url', $bp->displayed_user->domain . $bp->album->slug .'/'.$bp->album->single_slug.'/'.$images_template->image->id.'/'.$bp->album->delete_slug ),'bp-galleries-plus-delete-pic');
	}
*/
/**
 * bp_gallplus_image_original_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*function bp_gallplus_image_original_url() {
	echo bp_gallplus_get_image_original_url();
}
	function bp_gallplus_get_image_original_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $images_template->image->pic_org_url, strrpos($images_template->image->pic_org_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_original_url', bp_get_root_domain().$images_template->image->pic_org_url );
		}
		
	}
*/
/**
 * bp_gallplus_image_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
/*
function bp_gallplus_image_middle_url() {
	echo bp_gallplus_get_image_middle_url();
}
	function bp_gallplus_get_image_middle_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $images_template->image->pic_mid_url, strrpos($images_template->image->pic_mid_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_middle_url', bp_get_root_domain().$images_template->image->pic_mid_url );
		}
	}
*/
/**
 * bp_gallplus_image_thumb_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
 /*
function bp_gallplus_image_thumb_url() {
	echo bp_gallplus_get_image_thumb_url();
}
	function bp_gallplus_get_image_thumb_url() {

		global $bp, $images_template;

		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $images_template->image->pic_thumb_url, strrpos($images_template->image->pic_thumb_url, '/') + 1 );
		    $owner_id = $images_template->image->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_image_thumb_url', bp_get_root_domain().$images_template->image->pic_thumb_url );
		}
	}
*/
/**
 * bp_gallplus_image_middle_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */

function bp_gallplus_album_feature_url() {
	echo bp_gallplus_get_album_feature_url();
}
	function bp_gallplus_get_album_feature_url() {

		global $bp, $albums_template;


		if( $albums_template->album->feature_image == 0)
		{
			// No feature image has been set;
			return plugins_url( 'includes/images/No-Image-Available.gif' , dirname(__FILE__) );
		}
		if($bp->album->bp_gallplus_url_remap == true){

		    $filename = substr( $albums_template->album->feature_image_path, strrpos($albums_template->album->feature_image_path, '/') + 1 );
		    $owner_id = $albums_template->album->owner_id;
		    $result = $bp->album->bp_gallplus_base_url . '/' . $owner_id . '/' . $filename;

		    return $result;
		}
		else {
		    return apply_filters( 'bp_gallplus_get_album_feature_url', bp_get_root_domain().$albums_template->album->feature_image_path );
		}
	}
/**
 * bp_gallplus_total_album_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_total_album_count() {
	echo bp_gallplus_get_total_album_count();
}
	function bp_gallplus_get_album_album_count() {
	    
		global $albums_template;
			
		return apply_filters( 'bp_gallplus_get_total_image_count', $albums_template->total_album_count );
	}

/**
 * bp_gallplus_album_pagination()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_pagination($always_show = false) {
	echo bp_gallplus_get_album_pagination($always_show);
}
	function bp_gallplus_get_album_pagination($always_show = false) {
	    
		global $albums_template;
		
		if ($always_show || $albums_template->total_image_count > $albums_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination', $albums_template->pag_links );
	}

/**
 * bp_gallplus_image_pagination_global()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_pagination_global($always_show = false) {
	echo bp_gallplus_get_album_pagination_global($always_show);
}
	function bp_gallplus_get_album_pagination_global($always_show = false) {
	    
		global $albums_template;
		
		if ($always_show || $albums_template->total_image_count > $albums_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination_global', $albums_template->pag_links_global );
	}
/**
 * bp_gallplus_image_pagination_stub()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_pagination_stub($always_show = false) {
	echo bp_gallplus_get_album_pagination_stub($always_show);
}
	function bp_gallplus_get_album_pagination_stub($always_show = false) {
	    
		global $albums_template;
		
		if ($always_show || $albums_template->total_image_count > $albums_template->pag_per_page)
		return apply_filters( 'bp_gallplus_get_image_pagination_global', $albums_template->pag_links_stub );
	}

/**
 * bp_gallplus_adjacent_links()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_gallplus_album_adjacent_links() {
	echo bp_gallplus_album_get_adjacent_links();
}
	function bp_gallplus_album_get_adjacent_links() {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album() || $albums_template->has_next_album())
			return bp_gallplus_get_prev_album_or_album_link().' '.bp_gallplus_get_next_album_or_album_link();
		else
			return '<a href="'.bp_gallplus_get_album_url().'" class="image-album-link image-no-adjacent-link"><span>'.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_gallplus_next_image_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_album_link($text = ' &raquo;', $title = true) {
	echo bp_gallplus_get_next_album_link($text, $title);
}
	function bp_gallplus_get_next_album_link($text = ' &raquo;', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_next_album()){
			$text = ( ($title)?bp_gallplus_get_next_album_title():'' ).$text;
			return '<a href="'.bp_gallplus_get_next_album_url().'" class="image-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return null;
	}

/**
 * bp_gallplus_next_image_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_album_or_album_link($text = ' &raquo;', $title = true) {
	echo bp_gallplus_get_next_album_or_album_link($text, $title);
}
	function bp_gallplus_get_next_album_or_album_link($text = ' &raquo;', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_next_pic()){
			$text = ( ($title)?bp_gallplus_get_next_album_title():'' ).$text;
			return '<a href="'.bp_gallplus_get_next_album_url().'" class="image-next-link"> <span>'.$text.'</span></a>';
		}
		else
			return '<a href="'.bp_gallplus_get_album_url().'" class="image-album-link image-next-link"> <span> '.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span></a>';
	}

/**
 * bp_gallplus_next_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_album_url() {
	echo bp_gallplus_get_next_album_url();
}
	function bp_gallplus_get_next_album_url() {
	    
		global $bp,$albums_template;
		
		if ($albums_template->has_next_album())
			return apply_filters( 'bp_gallplus_get_next_image_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$albums_template->album->next_album->id  . '/');
	}

/**
 * bp_gallplus_next_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_next_album_title() {
	echo bp_gallplus_get_next_album_title();
}
	function bp_gallplus_get_next_album_title() {
	    
		global $albums_template;
		
		if ($albums_template->has_next_album())
			return apply_filters( 'bp_gallplus_get_image_title', $albums_template->album->next_album->title );
	}
	
/**
 * bp_gallplus_has_next_album()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_next_album() {
    
	global $bp,$albums_template;
	
	return $albums_template->has_next_album();
}

/**
 * bp_gallplus_prev_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_album_link($text = '&laquo; ', $title = true) {
	echo bp_gallplus_get_prev_album_link($text, $title);
}
	function bp_gallplus_get_prev_album_link($text = '&laquo; ', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album()){
			$text .= ($title)?bp_gallplus_get_prev_album_title():'';
			return '<a href="'.bp_gallplus_get_prev_album_url().'" class="image-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return null;
	}

/**
 * bp_gallplus_prev_album_or_album_link()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_album_or_album_link($text = '&laquo; ', $title = true) {
	echo bp_gallplus_get_prev_album_or_album_link($text, $title);
}
	function bp_gallplus_get_prev_album_or_album_link($text = '&laquo; ', $title = true) {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album()){
			$text .= ($title)?bp_gallplus_get_prev_album_title():'';
			return '<a href="'.bp_gallplus_get_prev_album_url().'" class="image-prev-link"><span>'.$text.'</span> </a>';
		}
		else
			return '<a href="'.bp_gallplus_get_album_url().'" class="image-album-link image-prev-link"><span> '.bp_word_or_name( __( "Return to your album", 'bp-galleries-plus' ), __( "Return to %s album", 'bp-galleries-plus' ) ,false,false ).'</span> </a>';
	}

/**
 * bp_gallplus_prev_image_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_album_url() {
	echo bp_gallplus_get_prev_album_url();
}
	function bp_gallplus_get_prev_album_url() {
	    
		global $bp,$albums_template;
		
		if ($albums_template->has_prev_pic())
			return apply_filters( 'bp_gallplus_get_prev_image_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->single_slug.'/'.$albums_template->album->prev_album->id . '/');
	}

/**
 * bp_gallplus_prev_image_title()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_prev_album_title() {
	echo bp_gallplus_get_prev_album_title();
}
	function bp_gallplus_get_prev_album_title() {
	    
		global $albums_template;
		
		if ($albums_template->has_prev_album())
			return apply_filters( 'bp_gallplus_get_image_title', $albums_template->album->prev_album->title );
	}
	
/**
 * bp_gallplus_has_prev_image()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_has_prev_album() {
    
	global $albums_template;
	
	return $albums_template->has_prev_album();
}

/**
 * bp_gallplus_images_url()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
//function bp_gallplus_album_url() {
    
//	echo bp_gallplus_get_album_url();
	
//}
//	function bp_gallplus_get_album_url() {
	    
//		global $bp;
//			return apply_filters( 'bp_gallplus_get_images_url', $bp->displayed_user->domain . $bp->album->slug . '/'.$bp->album->album_slug . '/');
//	}

/**
 * bp_gallplus_image_has_activity()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */	
function bp_gallplus_album_has_activity(){

	global $bp,$albums_template;

	// Handle users that try to run the function when the activity stream is disabled
	// ------------------------------------------------------------------------------
	if ( !function_exists( 'bp_activity_add' ) || !$bp->album->bp_gallplus_enable_wire) {
		return false;
	}
	$returnValue = bp_has_activities( array('object'=> $bp->album->id,'primary_id'=>$albums_template->album->id , 'show_hidden' => true) );
	return $returnValue;
}

/**
 * bp_gallplus_album_comments_enabled()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_album_comments_enabled() {
    
        global $bp;

        return $bp->album->bp_gallplus_enable_comments;
	
}
function bp_gallplus_album_time() {
	echo bp_gallplus_get_album_time();
}
	function bp_gallplus_get_album_time() {
	    
		global $albums_template;
		
		if($albums_template->album->date_updated > $albums_template->album->date_created)
		{
			return apply_filters( 'bp_gallplus_get_image_title','Updated '.bp_core_time_since( strtotime( $albums_template->album->date_updated ) ));
		}
		else
		{
			return apply_filters( 'bp_gallplus_get_image_title','Created '.bp_core_time_since( strtotime( $albums_template->album->date_created ) ));
		}
	}
function time_elapsed_string($ptime) {
    $etime = time() - $ptime;
    
    if ($etime < 1) {
        return '0 seconds';
    }
    
    $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hour',
                60                      =>  'minute',
                1                       =>  'second'
                );
    
    foreach ($a as $secs => $str) {
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . ' ' . $str . ($r > 1 ? 's' : '');
        }
    }
}
function bp_gallplus_album_created_time() {
	echo bp_gallplus_get_album_created_time();
}
	function bp_gallplus_get_album_created_time() {
	    
		global $albums_template;
		
			return apply_filters( 'bp_gallplus_get_image_title',time_elapsed_string( strtotime( $albums_template->album->date_created )).' ago');
//			return apply_filters( 'bp_gallplus_get_image_title',bp_core_time_since( strtotime( $albums_template->album->date_created ) ));
	}
function bp_gallplus_album_updated_time() {
	echo bp_gallplus_get_album_updated_time();
}
	function bp_gallplus_get_album_updated_time() {
	    
		global $albums_template;
		
			return apply_filters( 'bp_gallplus_get_image_title',bp_core_time_since( strtotime( $albums_template->album->date_updated ) ));
	}
/**
 * bp_gallplus_total_album_image_count()
 * 
 * @version 0.1.8.11
 * @since 0.1.8.0
 */
function bp_gallplus_total_album_image_count() {
	echo bp_gallplus_get_total_album_image_count();
}
	function bp_gallplus_get_total_album_image_count() {
	    
		global $albums_template, $bp, $wpdb;
		
				$sql =  $wpdb->prepare( "SELECT COUNT(id) FROM {$bp->album->table_name} WHERE album_id= %d",$albums_template->album->id) ;
				$result = $wpdb->get_var( $sql );
				return apply_filters( 'bp_gallplus_total_album_image_count', $result );
	}
function bp_gallplus_image_viewer_attribute(){
	echo bp_gallplus_get_image_viewer_attribute();
}

function bp_gallplus_get_image_viewer_attribute()
{
	$bp_gallplus_viewer = get_option('bp_gallplus_viewer');
	switch($bp_gallplus_viewer)
	{
			case 0: if(get_site_option( 'bp_gallplus_use_watermark' ))
							{
									// A bit of obfuscation on the id so it is not obviously guessable
								 $utc_str = gmdate("M d Y", time());
  							 $utc = strtotime($utc_str);
								 $token = $utc + intval(bp_gallplus_get_image_id());
								$classStr = 'class="fancybox" data-fancybox-group="gallery" data-fancybox-large="'.BP_GALLPLUS_PLUGIN_URL.'includes/bpa.large.img.php?token='.base64_encode($token).'" data-fancybox-image-id="'.bp_gallplus_get_image_id().'"';
							}
							else
								$classStr = 'class="fancybox" data-fancybox-group="gallery" data-fancybox-large="'.bp_gallplus_get_image_url().'"';
							break;
			case 1: $classStr = 'class="thickbox"';
							break;
			case 2: $classStr = ''; // colorbox is automatic
							break;
			case 3: $classStr = 'rel="lightbox"';
							break;
			default: $classStr = 'class="fancybox" data-fancybox-group="gallery" data-fancybox-large="'.bp_gallplus_get_image_url().'"';																									
	}
	return $classStr;
}
?>