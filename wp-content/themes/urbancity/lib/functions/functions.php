<?php

// Add support for various awesome things
if(function_exists('add_theme_support')){
	add_theme_support('menus');
	add_theme_support('automatic-feed-links');
	add_theme_support('post-thumbnails', array('post', 'slide'));
}

if(function_exists('add_image_size')){
	add_image_size('slide', 924, 345, true);
	add_image_size('single', 608, 9999, false);
	add_image_size('archive', 250, 9999, false);
	add_image_size('admin', 120, 9999, false);
	add_image_size('thumb', 75, 75, true);
}

// Register newer jQuery for old WP installs
if (!is_admin()) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js', '', '1.6.1');
}

// Define theme location for bundled plugins
if(!defined('WP_THEME_URL')) {
	define( 'WP_THEME_URL', get_bloginfo('stylesheet_directory'));
}
if(!defined('WP_THEME_DIR')) {
	define( 'WP_THEME_DIR', get_bloginfo('stylesheet_directory'));
}

// Register theme menus
function register_menus() {
	register_nav_menus(
		array(
			'primary' => __('Primary Nav Menu'),
			'footer' => __('Footer Nav Menu')
		)
	);
}
add_action( 'init', 'register_menus' );

// Primary nav menu with fallback
function ct_primary_nav_menu() {
    if(function_exists('wp_nav_menu'))
    	wp_nav_menu(array(
			'theme_location' => 'primary',
			'container' => 'div',
			'container_class' => 'navbar',
			'menu_class' => '',
			'menu_id' => false,
			'depth' => '3',
			'fallback_cb' => 'ct_primary_nav_fallback'
		));
    else
        ct_primary_nav_fallback();
}
function ct_primary_nav_fallback() {
    wp_page_menu('menu_class=navbar');
}

// Footer nav menu
function ct_footer_nav_menu() {
    if(function_exists('wp_nav_menu'))
    	wp_nav_menu(array(
			'theme_location' => 'footer',
			'container' => false,
			'menu_class' => 'footer_nav',
			'menu_id' => false,
			'depth' => '1'
		));
}


if(!function_exists('churchthemes_comment')):
/**
 * Template for comments and pingbacks.
 */
function churchthemes_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="single-comment">
		<div class="comment-author vcard">
			<?php echo get_avatar( $comment, 40 ); ?>
			<?php printf( __( '%s <span class="says">says:</span>', 'churchthemes' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>
		</div><!-- .comment-author .vcard -->
		<?php if ( $comment->comment_approved == '0' ) : ?>
			<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'churchthemes' ); ?></em>
			<br />
		<?php endif; ?>

		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
			<?php
				/* translators: 1: date, 2: time */
				printf( __( '%1$s at %2$s', 'churchthemes' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'churchthemes' ), ' ' );
			?>
		</div><!-- .comment-meta .commentmetadata -->

		<div class="comment-body"><?php comment_text(); ?></div>

		<div class="reply">
			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
		</div><!-- .reply -->
	</div><!-- #comment-##  -->

	<?php
			break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'churchthemes' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'churchthemes' ), ' ' ); ?></p>
	<?php
			break;
	endswitch;
}
endif;


if(!function_exists('ct_posted_by')):
/**
 * Prints HTML with meta information for the current post date/time and author.
 */
function ct_posted_by() {
	printf( __( '<span class="%1$s">Posted by %2$s', 'churchthemes' ),
		'meta-prep-author',
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'churchthemes' ), get_the_author() ),
			get_the_author()
		)
	);
}
endif;


// Assign unique classes to first and last menu items
function first_last_class($output) {
	$output = preg_replace('/class="menu-item/', 'class="first menu-item', $output, 1);
	$output = substr_replace($output, 'class="last menu-item', strripos($output, 'class="menu-item'), strlen('class="menu-item'));
	return $output;
}
add_filter('wp_nav_menu', 'first_last_class');


// Flush rewrite rules
function flushRules() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}
add_filter('register_activation_hook', 'flushRules');


// Spoof the lastpostmodified date feeds
function spoof_lastpostmodified($lastpostmodified, $timezone) {
	// WP caches the feed (status 304 - see line 354 in wp-includes/classes.php)
	// We need this to not happen :)
	global $wp;
	if (!empty($wp->query_vars['feed'])){
		$lastpostmodified = date("Y-m-d H:i:s");  // Now
	}
	return $lastpostmodified;
}
add_filter('get_lastpostmodified','spoof_lastpostmodified',10,2);


// Awesome Pagination
function pagination($pages = '', $range = 3) {
	$showitems = ($range * 2)+1;
	
	global $paged;
	if(empty($paged)) $paged = 1;
	
	if($pages == '') {
		global $wp_query;
		$pages = $wp_query->max_num_pages;
		if(!$pages) {
			$pages = 1;
		}
	}
	
	if(1 != $pages) {
		echo "<ul class=\"pagination\">";
		if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a href=\"".get_pagenum_link(1)."\">&lt;&lt; First</a></li>";
		if($paged > 1 && $showitems < $pages) echo "<li class=\"previous\"><a href=\"".get_pagenum_link($paged - 1)."\">&lt; Prev</a></li>";
		
		for ($i=1; $i <= $pages; $i++) {
			if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
				echo ($paged == $i)? "<li class=\"active\"><a href=\"javascript:void(0)\">".$i."</a></li>":"<li><a href=\"".get_pagenum_link($i)."\">".$i."</a></li>";
			}
		}
		
		if ($paged < $pages && $showitems < $pages) echo "<li class=\"next\"><a href=\"".get_pagenum_link($paged + 1)."\">Next &gt;</a></li>";  
		if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<li><a href=\"".get_pagenum_link($pages)."\">Last &gt;&gt;</a></li>";
		echo "</ul>\n";
		echo "<p class=\"pagecount\">Page ".$paged." of ".$pages."</p>\n";
	}
}


// Add support for entry views
function add_entry_views() {
	add_post_type_support('post', array('entry-views'));
	add_post_type_support('page', array('entry-views'));
	add_post_type_support('slide', array('entry-views'));
}
add_action( 'init', 'add_entry_views' );


// Register custom post column for views and allow sorting
function post_edit_columns( $columns ) {
	$columns['post_views'] = __( 'Views', 'churchthemes' );
	return $columns;
}
add_filter( 'manage_edit-post_columns', 'post_edit_columns' );

function post_custom_columns( $column_name, $post_id ) {
	if ( 'post_views' != $column_name )
		return;
	$views = get_post_meta($post_id, 'Views', true);
	if ( !$views )
		$views = __( 'none', 'churchthemes' );
	echo $views;
}
add_action( 'manage_posts_custom_column', 'post_custom_columns', 10, 2 );

function post_views_column_register_sortable($columns) {
	$columns['post_views'] = 'Views';
	return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'post_views_column_register_sortable');

function post_column_orderby_views($vars) {
	if (isset( $vars['orderby']) && 'Views' == $vars['orderby']) {
		$vars = array_merge( $vars, array(
			'meta_key' => 'Views',
			'orderby' => 'meta_value_num'
		));
	}
	return $vars;
}
add_filter( 'request', 'post_column_orderby_views' );


// Load Admin Styles and Create custom Page Options
if (is_admin()) {
	wp_enqueue_style('options_css', get_bloginfo('template_directory') . '/lib/admin/css/options.css');
	wp_enqueue_style('meta_css', get_bloginfo('template_directory') . '/lib/admin/css/meta.css');
}
$page_options_meta = new WPAlchemy_MetaBox(
	array(
		'id' => '_page_options',
		'types' => array('page'),
		'autosave' => TRUE,
		'title' => 'Page Options',
		'template' => TEMPLATEPATH . '/lib/admin/page-options.php',
	)
);


// Allow shortcodes in text widgets
add_filter('widget_text', 'do_shortcode');


// Add the shortcode handler for YouTube videos
function addYouTube($atts, $content = null) {
	extract(shortcode_atts(
		array(
			"id" => '',
			"width" => '608',
			"height" => '375'
		),
		$atts
	));
	return '<iframe width="'.$width.'" height="'.$height.'" src="http://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
}
add_shortcode('youtube', 'addYouTube');

function add_youtube_button() {
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
	// Add only in Rich Editor mode
   if ( get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'add_youtube_tinymce_plugin');
		add_filter('mce_buttons', 'register_youtube_button');
	}
}

function register_youtube_button($buttons) {
	array_push($buttons, "|", "youtube_button");
	return $buttons;
}
 
// Load the TinyMCE plugin
function add_youtube_tinymce_plugin($plugin_array) {
	$plugin_array['youtube_button'] = get_bloginfo('template_url').'/lib/admin/scripts/editor_plugin.js';
	return $plugin_array;
}

function youtube_refresh_mce($ver) {
	$ver += 3;
	return $ver;
}
add_filter('tiny_mce_version', 'youtube_refresh_mce');
add_action('init', 'add_youtube_button');


// Add the shortcode handler for Vimeo videos
function addVimeo($atts, $content = null) {
	$ct_scheme = '00adef';
	if(function_exists('get_option_tree')):
		$ct_scheme = get_option_tree('ct_scheme');
		$ct_scheme = str_replace('#', '', $ct_scheme);
	endif;
	extract(shortcode_atts(
		array(
			"id" => '',
			"width" => '608',
			"height" => '342',
			"title" => 'false',
			"byline" => 'false',
			"portrait" => 'false',
			"color" => $ct_scheme
		),
		$atts
	));
	if($title == 'true'): $title = 1; else: $title = 0; endif;
	if($byline == 'true'): $byline = 1; else: $byline = 0; endif;
	if($portrait == 'true'): $portrait = 1; else: $portrait = 0; endif;
	return '<iframe src="http://player.vimeo.com/video/'.$id.'?title='.$title.'&amp;byline='.$byline.'&amp;portrait='.$portrait.'&amp;color='.$color.'" width="'.$width.'" height="'.$height.'" frameborder="0"></iframe>';
}
add_shortcode('vimeo', 'addVimeo');

function add_vimeo_button() {
	// Don't bother doing this stuff if the current user lacks permissions
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
		return;
	// Add only in Rich Editor mode
	if ( get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'add_vimeo_tinymce_plugin');
		add_filter('mce_buttons', 'register_vimeo_button');
	}
}
 
function register_vimeo_button($buttons) {
	array_push($buttons, "|", "vimeo_button");
	return $buttons;
}
 
// Load the TinyMCE plugin
function add_vimeo_tinymce_plugin($plugin_array) {
	$plugin_array['vimeo_button'] = get_bloginfo('template_url').'/lib/admin/scripts/editor_plugin.js';
	return $plugin_array;
}

function vimeo_refresh_mce($ver) {
	$ver += 3;
	return $ver;
}
add_filter('tiny_mce_version', 'vimeo_refresh_mce');
add_action('init', 'add_vimeo_button');


// CSS3 Button shortcode
function sc_css3button($atts, $content = null) {
	extract(shortcode_atts(
		array(
			'text' => 'menu_order',
			'url' => '',
			'target' => '_self',
			'title' => '',
			'rel' => '',
		), $atts));
	return "<p><a href=\"$url\" class=\"button\" target=\"$target\">$text</a></p>";
}
add_shortcode('button', 'sc_css3button');


// Add more Appearance options to the Admin Bar
function extend_admin_bar() {
	global $wp_admin_bar, $wpdb;
	if ( !is_super_admin() || !is_admin_bar_showing() )
		return;
	$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'title' => __( 'Theme Options', 'churchthemes' ), 'href' => get_bloginfo('url').'/wp-admin/themes.php?page=theme-options' ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'title' => __( 'Social Footer', 'churchthemes' ), 'href' => get_bloginfo('url').'/wp-admin/themes.php?page=social-footer' ) );
	$wp_admin_bar->add_menu( array( 'parent' => 'appearance', 'title' => __( 'Sidebars', 'churchthemes' ), 'href' => get_bloginfo('url').'/wp-admin/themes.php?page=sidebars' ) );
}
add_action('admin_bar_menu', 'extend_admin_bar', 1000);

?>