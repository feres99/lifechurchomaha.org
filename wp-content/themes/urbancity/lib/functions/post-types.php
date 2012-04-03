<?php

/* SLIDE */

// Register Post Type
add_action('init', 'sl_register');

function sl_register() {
	$labels = array(
		'name' => ( 'Slides' ),
		'singular_name' => ( 'Slide' ),
		'add_new' => _x( 'Add New', 'Slides' ),
		'add_new_item' => __( 'Add New Slide' ),
		'edit_item' => __( 'Edit Slide' ),
		'new_item' => __( 'New Slide' ),
		'view_item' => __( 'View Slide' ),
		'search_items' => __( 'Search Slides' ),
		'not_found' =>  __( 'No Slides found' ),
		'not_found_in_trash' => __( 'No Slides found in Trash' ),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => false,
		'publicly_queryable' => false,
		'exclude_from_search' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 11,
		'menu_icon' => get_stylesheet_directory_uri() . '/lib/admin/images/menu_icon-slide-16.png',
		'supports' => array( 'title', 'thumbnail', 'revisions' )
	);

	register_post_type( 'slide' , $args );
	
	flush_rewrite_rules(false);
	
}
// End Register Post Type

// Create Custom Taxonomies
add_action( 'init', 'create_slide_taxonomies', 0 );

function create_slide_taxonomies() {

	// Slide Tags Taxonomy (Non-Hierarchical)
	$labels = array(
		'name' => _x( 'Slide Tags', 'taxonomy general name' ),
		'singular_name' => _x( 'Tag', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Tags' ),
		'popular_items' => __( 'Popular Tags' ),
		'all_items' => __( 'All Tags' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit Tag' ),
		'update_item' => __( 'Update Tag' ),
		'add_new_item' => __( 'Add New Tag' ),
		'new_item_name' => __( 'New Tag Name' ),
		'separate_items_with_commas' => __( 'Separate Tags with commas' ),
		'add_or_remove_items' => __( 'Add or remove Tags' ),
		'choose_from_most_used' => __( 'Choose from the most used Tags' )
	);
	register_taxonomy( 'slide_tag', 'slide', array(
		'hierarchical' => false,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'slide_tag' ),
	));
	// End Slide Tags Taxonomy

}
// End Custom Taxonomies

// Submenu
add_action('admin_menu', 'sl_submenu');

function sl_submenu() {
	
	// Add to end of admin_menu action function
	global $submenu;
	$submenu['edit.php?post_type=slide'][5][0] = __('All Slides');
	$post_type_object = get_post_type_object('slide');
	$post_type_object->labels->name = "Slides";

}
// End Submenu

// Create Slide Options Box
add_action("admin_init", "sl_admin_init");

function sl_admin_init(){
    add_meta_box("sl_meta", "Slide Options", "sl_meta_options", "slide", "normal", "core");
}

// Custom Field Keys
function sl_meta_options(){
	global $post;
	$custom = get_post_custom($post->ID);
	$sl_tagline = $custom["_ct_sl_tagline"][0];
	$sl_linkurl = $custom["_ct_sl_linkurl"][0];
	$sl_notes = $custom["_ct_sl_notes"][0];
// End Custom Field Keys

// Start HTML
?>

	<h2 class="meta_section"><?php _e('Featured Image', 'churchthemes'); ?></h2>
	
	<div class="meta_item first">
		<a title="Set Featured Image" href="media-upload.php?post_id=<?php echo $post->ID; ?>&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=285" id="set-post-thumbnail" class="thickbox button rbutton"><?php _e('Set Featured Image', 'churchthemes'); ?></a>
		<br />
		<span><?php _e('To ensure the best image quality possible, please use a JPG image that is 924 x 345 pixels', 'churchthemes'); ?></span>
	</div>
	
	<hr class="meta_divider" />
	
	<h2 class="meta_section"><?php _e('General', 'churchthemes'); ?></h2>
	
	<div class="meta_item">
		<label for="_ct_sl_tagline"><?php _e('Tagline', 'churchthemes'); ?></label>
		<input type="text" name="_ct_sl_tagline" size="70" autocomplete="on" value="<?php echo htmlspecialchars($sl_tagline); ?>">
		<span><?php _e('Tagline shown under the title on the slide (80 characters max)', 'churchthemes'); ?></span>
	</div>
	
	<div class="meta_item">
		<label for="_ct_sl_linkurl"><?php _e('Slide Link', 'churchthemes'); ?></label>
		<input type="text" name="_ct_sl_linkurl" size="70" autocomplete="on" placeholder="http://mychurch.org/some-page-with-more-info/" value="<?php echo htmlspecialchars($sl_linkurl); ?>">
		<span><?php _e('Where users are taken when the slide image is clicked', 'churchthemes'); ?></span>
	</div>
	
	<hr class="meta_divider" />
	
	<h2 class="meta_section"><?php _e('More', 'churchthemes'); ?></h2>
	
	<div class="meta_item">
		<label for="_ct_sl_notes"><?php _e('Admin Notes', 'churchthemes'); ?><br /><br /><span class="label_note"><?php _e('Not Published', 'churchthemes'); ?></span></label>
		<textarea type="text" name="_ct_sl_notes" cols="60" rows="8"><?php echo htmlspecialchars($sl_notes); ?></textarea>
	</div>
		
	<div class="meta_clear"></div>
	
<?php
// End HTML
}

// Save Custom Field Values
add_action('save_post', 'save_ct_sl_meta');

function save_ct_sl_meta(){

	global $post_id;
	
	if(isset($_POST['post_type']) && ($_POST['post_type'] == "slide")):
		
		$sl_tagline = $_POST['_ct_sl_tagline'];
		update_post_meta($post_id, '_ct_sl_tagline', $sl_tagline);
		
		$sl_linkurl = $_POST['_ct_sl_linkurl'];
		update_post_meta($post_id, '_ct_sl_linkurl', $sl_linkurl);
		
		$sl_notes = $_POST['_ct_sl_notes'];
		update_post_meta($post_id, '_ct_sl_notes', $sl_notes);
	
	endif;	
}
// End Custom Field Values
// End Slide Options Box

// Custom Columns
function sl_register_columns($columns){
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'sl_tagline' => 'Tagline',
			'sl_tags' => 'Tags',
			'sl_views' => 'Views',
			'sl_image' => 'Featured Image'
		);
		return $columns;
}
add_filter('manage_edit-slide_columns', 'sl_register_columns');

function sl_display_columns($column){
		global $post;
		$custom = get_post_custom();
		switch ($column)
		{
			case 'sl_tagline':
				$meta_views = $custom['_ct_sl_tagline'][0];
				echo $meta_views;
				break;
			case 'sl_tags':
				echo get_the_term_list($post->ID, 'slide_tag', '', ', ', '');
				break;
			case 'sl_views':
				$meta_views = $custom['Views'][0];
				echo $meta_views;
				break;
			case 'sl_image':
				echo get_the_post_thumbnail($post->ID, 'admin');
				break;
		}
}
add_action('manage_posts_custom_column', 'sl_display_columns');

// End Custom Columns

/* END SLIDE */

?>