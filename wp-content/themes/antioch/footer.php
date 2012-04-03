<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content
 * after.  Calls sidebar-footer.php for bottom widgets.
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */
if(is_page()):
	if(have_posts()): while(have_posts()): the_post();
		$page_options_meta = get_post_meta($post->ID,'_page_options',true);
		isset($page_options_meta['ct_social_footer']) ? $page_social = $page_options_meta['ct_social_footer'] : $page_social = null;
	endwhile; endif;
endif;

$theme_options = get_option('ct_theme_options');
$ct_analytics_code = $theme_options['analytics_code'];
$ct_footer_copyright = $theme_options['footer_copyright_text'];

$social_footer_settings = get_option('ct_social_footer_settings');
if(isset($social_footer_settings['social_footer'])):
	$social = $social_footer_settings['social_footer'];
else:
	$social = null;
endif;
$facebook = $social_footer_settings['facebook'];
$twitter = $social_footer_settings['twitter'];
$flickr = $social_footer_settings['flickr'];
$youtube = $social_footer_settings['youtube'];
$vimeo = $social_footer_settings['vimeo'];

?>
<?php if(is_page() && ($social == 'on' || empty($social)) && ($facebook || $twitter || $flickr || $youtube || $vimeo) && ($page_social == 'show' || empty($page_social))): ?>
		<div class="push2"></div>
<?php get_template_part('social'); ?>
<?php elseif(!is_page() && ($social == 'on' || empty($social)) && ($facebook || $twitter || $flickr || $youtube || $vimeo)): ?>
		<div class="push2"></div>
<?php get_template_part('social'); ?>
<?php else: ?>
		<div class="push"></div>
<?php endif; ?>
	</div>
	<div id="footer" class="container_12">
		<div class="grid_5 alpha">
			<p><?php if($ct_footer_copyright): echo $ct_footer_copyright.'<br />'; endif; ?>
			</p>
		</div>
		<?php ct_footer_nav_menu(); ?>
	</div>
</div>
<?php if($ct_analytics_code): echo $ct_analytics_code; endif; ?>
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */

	wp_footer();
?>
</body>
</html>