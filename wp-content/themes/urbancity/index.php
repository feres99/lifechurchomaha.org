<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query. 
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */

get_header(); ?>
		<div id="ribbon" class="page">
			<div class="container_12 content">
				<div class="grid_12 alpha">
					<h1><?php the_title(); ?></h1>
				</div>
			</div>
		</div>
		<div id="wrapper3" class="container_12">
			<div id="content" class="grid_8 alpha">
<?php if(have_posts()): while(have_posts()): the_post(); ?
				<?php the_content(); ?>
				<?php
					wp_link_pages(array(
						'before' => '' . __('Pages:', 'churchthemes'),
						'after' => ''
					));
				?>
				<?php edit_post_link( __('Edit', 'churchthemes'), '', ''); ?>
				<?php comments_template('', true); ?>
<?php endwhile; endif; ?>
			</div>
<?php get_sidebar(); ?>
		</div>
<?php get_footer(); ?>
