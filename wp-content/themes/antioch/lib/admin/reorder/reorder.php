<?php
/*
Plugin Name: Reorder
Plugin URI: http://benjitastic.com
Description: Enables simple drag and drop reordering of all post types. Please consider <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=benjitastic%40gmail%2ecom&lc=US&item_name=Ben%20Kennedy%20%2d%20Reorder%20Wordpress%20Plugin&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest">donating</a> a few bucks to support future development. 
Author: Ben Kennedy
Version: 3.5
Author URI: http://benjitastic.com
*/

function build_pages($post_parent = 0) {	
	global $wp_version;
	if ($wp_version >= 3.0) {
		$post_type = $_GET['post_type'];
	} else {
		$post_type = $_GET['page'];		
	}

	$posts = get_posts('post_status=""&post_type='.$post_type.'&orderby=menu_order date&numberposts=-1&depth=1&post_parent='.$post_parent);

	foreach($posts as $p) {								
		$status = ($p->post_status != 'publish') ? "<span>$p->post_status</span>" : "";
		$children = get_posts('orderby=menu_order date&post_status=""&post_type='.$post_type.'&numberposts=-1&depth=1&post_parent='.$p->ID);
		$title = ($p->post_title) ? $p->post_title.$status : '(no title)'.$status;
		
		echo '<li id="listItem_'.$p->ID.'" class="clear-element page-item '.$p->post_status.'">
			<table class="reorder-inner">
			<tr>
			<td>';
			//if($children) echo '<a href="#" class="expand-collapse expanded"></a>';
		echo '<strong>'.$title.'</strong>
			</td>
			<td width="42" class="reorder-id">
			'.$p->ID.'
			</td>
			<td width="53" class="edit">';
			edit_post_link( 'Edit', '', '', $p->ID );
			echo '</td>						
			</tr>
			</table>';											
		
		if($wp_version >= 3.0 && is_post_type_hierarchical($_GET["post_type"]) || $wp_version < 3.0) {
			if ($children) {
				echo '<ul class="page-list">';
					build_pages($p->ID);
				echo '</ul>';
			}		
		}
		echo '</li>';		
	}			
}

// Build the admin UI
function reorder_ui(){
global $wp_version;
	if($wp_version >= 3.0){
		//get the label of the current post_type
	  	foreach (get_post_types('','objects') as $post_type ) {
	  		if($post_type->name == $_GET['post_type'])
				$pt = $post_type->label;
			elseif(!$_GET['post_type'])
				$pt = 'Posts';
	  	}
	} else {
		$pt = ucwords($_GET['page']).'s';
	}
  	
	?> 
	<div class="wrap">
		<div id="icon-edit" class="icon32"><br /></div>
			<h2>Reorder <?php echo $pt ?></h2>
			<div class="tablenav">
				<p id="reorder-loading" class="alignleft">Drag and drop to reorder and then click "Apply New Order".</p>
				<div class="alignright">
					<span class="reorder-loading"></span>
					<input type="submit" id="post-query-submit" value="Apply New Order" class="button-secondary submit-reorder" />				
				</div>
			</div>
			<table class="widefat post fixed">
				<thead>
					<tr><th>Title</th><th width="38">ID</th><th width="38">Edit</th></tr>
				</thead>	
				<tr>
					<td style="padding: 0" colspan="3" id="reorder-list">
						<?php /*<p><a href="#" class="reorder-expand-all">expand all</a> <a href="#" class="reorder-collapse-all">collapse all</a></p>*/ ?>
						<ul id="order-posts-list<?php if(($wp_version >= 3.0 && is_post_type_hierarchical($_GET["post_type"])) || ($wp_version < 3.0 && $_GET['page'] == 'page')) echo '-nested'; ?>" class="page-list">
						<?php build_pages(); ?>
						</ul>						
					</td>
				</tr>
				<tfoot>
					<tr><th>Title</th><th width="38">ID</th><th width="38">Edit</th></tr>
				</tfoot>
			</table>	
			<div class="tablenav">
				<div class="alignright">
					<span class="reorder-loading"></span>
					<input type="submit" id="post-query-submit" value="Apply New Order" class="button-secondary submit-reorder" />
				</div>
			</div>						
	</div>
<?php }


// Add CSS
function reorder_head(){	
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('template_url') .'/lib/admin/reorder/reorder.css" />' . "\n";
	
	echo '
	
	<!--[if IE]>
	<style type="text/css">
	.reorder-highlight {background: #dbdbdb!important}
	</style>
	<![endif]-->
	
	<!--[if lt IE 8]>
	<style type="text/css">	
	#reorder-list table, #dragHelper table {float: left}
	</style>
	<![endif]-->';
}

// Add Javascript
function reorder_script() {	
	//wp_enqueue_script('reorderScript', WP_PLUGIN_URL . '/reorder/interface-1.2.js');
	wp_enqueue_script('reorderScript', get_bloginfo('template_url') . '/lib/admin/reorder/interface/iutil.js');
	wp_enqueue_script('reorderScript1', get_bloginfo('template_url') . '/lib/admin/reorder/interface/idrag-modified.js');
	wp_enqueue_script('reorderScript2', get_bloginfo('template_url') . '/lib/admin/reorder/interface/idrop.js');
	wp_enqueue_script('reorderScript3', get_bloginfo('template_url') . '/lib/admin/reorder/interface/isortables.js');
	wp_enqueue_script('reorderScript4', get_bloginfo('template_url') . '/lib/admin/reorder/inestedsortable.js');
	wp_enqueue_script('reorderScript5', get_bloginfo('template_url') . '/lib/admin/reorder/reorder.js');
}

// Add to admin menus
function reorder_menu(){
	global $wp_version;
		
	//add menu to standard Posts
	$page = add_submenu_page('edit.php', 'Order Posts', 'Reorder', 'edit_posts',  'post', 'reorder_ui'); 
	add_action( "admin_print_scripts-$page", 'reorder_script' );
		
	//exclude this plugin from the following post_types
	$excludedPostTypes = array('attachment', 'revision', 'nav_menu_item');
		
	if($wp_version >= 3.0){
		//add menu to each post_type (3.0 + greater)
		foreach(get_post_types('','names') as $r) {
			if(!in_array($r, $excludedPostTypes)) {
				$page = add_submenu_page('edit.php?post_type='.$r.'', "Reorder", "Reorder", 'edit_posts',  $r, 'reorder_ui');
				add_action( "admin_print_scripts-$page", 'reorder_script' );
			}
		}	
	} else {
		//add menu to Pages
		$page = add_submenu_page('edit-pages.php', "Reorder", "Reorder", 'edit_posts',  'page', 'reorder_ui');
		add_action( "admin_print_scripts-$page", 'reorder_script' );
	}
	
}

function reorder_orderPosts($orderBy) {
    global $wpdb, $pagenow;
    if ($pagenow == 'edit.php')
        $orderBy = "{$wpdb->posts}.menu_order ASC, post_date DESC";  
    return $orderBy;
}

//add_action('admin_print_scripts', 'reorder_script');
add_action('admin_head', 'reorder_head');
add_action('admin_menu', 'reorder_menu');
add_filter('posts_orderby', 'reorder_orderPosts'); //add filter for post ordering

//add_action('parse_query','reorder_override');

function reorder_override($query) {
    global $menu_order;    
    
    //if the orderby param is not set or is set to 'menu_order'
	if (!isset($query->query_vars['orderby']) || $query->query_vars['orderby'] == 'menu_order' || $query->query_vars['orderby'] == '' || $query->query_vars['orderby'] == 'menu_order date') {
		
		//set to order by menu_order first, and date second
		//works on query_posts and get_posts
        $query->query_vars['orderby'] = 'menu_order date';
    }
}

?>