<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */

$post_settings = get_option('ct_post_settings');
$page_layout = $post_settings['single_layout'];
$facebook_likes = $post_settings['facebook_likes'];
$tweet_this = $post_settings['tweet_this'];


get_header(); ?>
		<div id="ribbon" class="page">
			<div class="container_12 content">
				<div class="grid_9 alpha">
					<h1><?php the_title(); ?></h1>
				</div>
				<div class="grid_3 omega">
					<span class="tagline"><?php echo get_the_date(); ?></span>
				</div>
			</div>
		</div>
		<div id="wrapper3" class="container_12">
<?php if($page_layout == 'left'): get_sidebar(); endif; ?>
			<div id="content" class="<?php if($page_layout == 'full'): echo 'grid_12'; else: echo 'grid_8'; endif; ?> <?php if($page_layout == 'left'): echo 'omega'; else: echo 'alpha'; endif; ?> single single-post">
<?php if(have_posts()) while(have_posts()): the_post(); ?>
				<?php ct_posted_by(); ?>
				<br /><br />
				<div class="image"><?php the_post_thumbnail('single'); ?></div>
				<?php the_content(); ?>
<?php if((empty($facebook_likes) || $facebook_likes == 'on') || (empty($tweet_this) || $tweet_this == 'on')): ?>
				<div class="social">
					<ul>
<?php if((empty($facebook_likes) || $facebook_likes == 'on')): ?>
						<li><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like href="<?php the_permalink(); ?>" layout="button_count" show_faces="false" font="lucida grande"></fb:like></li>
<?php endif; ?>
<?php if((empty($tweet_this) || $tweet_this == 'on')): ?>
						<li><a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal"><?php _e('Tweet', 'churchthemes'); ?></a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></li>
<?php endif; ?>
					</ul>
				</div>
<?php endif; ?>
				<?php
					wp_link_pages(array(
						'before' => '' . __('Pages:', 'churchthemes'),
						'after' => ''
					));
				?>
				<div class="prev"><?php previous_post_link('%link', '' . _x('&larr;', 'Previous post link', 'churchthemes') . ' %title'); ?></div>
				<div class="next"><?php next_post_link('%link', '%title ' . _x('&rarr;', 'Next post link', 'churchthemes') . ''); ?></div>
				
				<?php edit_post_link( __('Edit this post', 'churchthemes'), '<div class="edit-post">', '</div>'); ?>
				
				<?php comments_template('', true); ?>
				
<?php endwhile; // end of the loop. ?>
			</div>
<?php if($page_layout == 'right' || empty($page_layout)): get_sidebar(); endif; ?>
		</div>
<?php get_footer(); ?>