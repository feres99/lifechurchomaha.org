<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */

$term =	$wp_query->get_queried_object();

$post_settings = get_option('ct_post_settings');
$ct_post_archive_title = $post_settings['archive_title'];
if(empty($ct_post_archive_title)) $ct_post_archive_title = __('Post Archives', 'churchthemes');
$ct_post_archive_layout = $post_settings['archive_layout'];

get_header();

?>
		<div id="ribbon" class="page">
			<div class="container_12 content">
				<div class="grid_6 alpha">
					<h1>
<?php if(is_day()): ?>
				<?php printf( __('Daily Archives', 'churchthemes'), get_the_date()); ?>
<?php elseif(is_month()): ?>
				<?php printf( __('Monthly Archives', 'churchthemes'), get_the_date('F Y')); ?>
<?php elseif(is_year()): ?>
				<?php printf( __('Yearly Archives', 'churchthemes'), get_the_date('Y')); ?>
<?php else: ?>
				<?php if($ct_post_archive_title): echo $ct_post_archive_title; else: _e('Post Archives', 'churchthemes'); endif; ?>
<?php endif; ?>
					</h1>
				</div>
				<div class="grid_6 omega">
					<span class="tagline">
						<?php if(is_day()): echo get_the_date(); endif; ?>
						<?php if(is_month()): echo get_the_date('F Y'); endif; ?>
						<?php if(is_year()): echo get_the_date('Y'); endif; ?>
						<?php if(is_category() || is_tag()): echo $term->name; endif; ?>
						<?php if(is_author()): echo $term->display_name; endif; ?>
					</span>
				</div>
			</div>
		</div>
		<div id="wrapper3" class="container_12">
<?php
	if((is_day() || is_month() || is_year() || is_post_type_archive('post') || is_tag() || is_category() || is_author()) && $ct_post_archive_layout == 'left'):
		get_sidebar();
?>
			<div id="content" class="grid_8 omega">
<?php
	elseif((is_day() || is_month() || is_year() || is_post_type_archive('post') || is_tag() || is_category() || is_author()) && $ct_post_archive_layout == 'full'):
?>
			<div id="content" class="grid_12 alpha">
<?php
	else:
?>
			<div id="content" class="grid_8 alpha">
<?php
	endif;
?>
<?php
if(is_day() || is_month() || is_year() || is_post_type_archive('post') || is_tag() || is_category() || is_author()):
	
	get_template_part('loop');

endif;

?>
			</div>
<?php
	if((is_day() || is_month() || is_year() || is_post_type_archive('post') || is_tag() || is_category() || is_author()) && ($ct_post_archive_layout == 'right' || empty($ct_post_archive_layout))):
		get_sidebar();
	endif;
?>
		</div>
<?php get_footer(); ?>