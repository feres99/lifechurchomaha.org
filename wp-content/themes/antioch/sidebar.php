<?php

if(is_page()):
	if(have_posts()): while(have_posts()): the_post();
		$page_options_meta = get_post_meta($post->ID,'_page_options',true);
		$page_layout = $page_options_meta['ct_page_layout'];
		$page_sidebar = $page_options_meta['ct_page_sidebar'];
	endwhile; endif;
endif;

$post_type = get_query_var('post_type');

$theme_options = get_option('ct_theme_options');
$search_results_layout = $theme_options['search_results_layout'];
$search_results_sidebar = $theme_options['search_results_sidebar'];

$post_settings = get_option('ct_post_settings');
$post_archive_layout = $post_settings['archive_layout'];
$post_archive_sidebar = $post_settings['archive_sidebar'];
$post_single_layout = $post_settings['single_layout'];
$post_single_sidebar = $post_settings['single_sidebar'];

?>
<?php
	if(
		(is_page() && ($page_layout == 'right' || empty($page_layout))) || 
		(is_search() && ($search_results_layout == 'right' || empty($search_results_layout))) || 
		((is_post_type_archive('post') || is_day() || is_month() || is_year() || is_tag() || is_category() || is_author()) && ($post_archive_layout == 'right' || empty($post_archive_layout))) || 
		(is_singular('post') && ($post_single_layout == 'right' || empty($post_single_layout)))
	):
?>
			<div id="sidebar" class="grid_4 omega">
<?php
	elseif(
		(is_page() && $page_layout == 'left') || 
		(is_search() && $search_results_layout == 'left') || 
		((is_post_type_archive('post') || is_day() || is_month() || is_year() || is_tag() || is_category() || is_author()) && $post_archive_layout == 'left') || 
		(is_singular('post') && $post_single_layout == 'left')
	):
?>
			<div id="sidebar" class="grid_4 alpha">
<?php endif; ?>
				<?php
					if(is_page()):
						if($page_sidebar):
							dynamic_sidebar($page_sidebar);
						else:
							dynamic_sidebar('Primary Sidebar');
						endif;
					endif;
				?>
				<?php
					if(is_search()):
						if($search_results_sidebar):
							dynamic_sidebar($search_results_sidebar);
						else:
							dynamic_sidebar('Primary Sidebar');
						endif;
					endif;
				?>
				<?php
					if(is_post_type_archive('post') || is_day() || is_month() || is_year() || is_tag() || is_category() || is_author()):
						if($post_archive_sidebar):
							dynamic_sidebar($post_archive_sidebar);
						else:
							dynamic_sidebar('Primary Sidebar');
						endif;
					endif;
				?>
				<?php
					if(is_singular('post')):
						if($post_single_sidebar):
							dynamic_sidebar($post_single_sidebar);
						else:
							dynamic_sidebar('Primary Sidebar');
						endif;
					endif;
				?>
			</div>