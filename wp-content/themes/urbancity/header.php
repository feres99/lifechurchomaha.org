<?php

$theme_options = get_option('ct_theme_options');
$ct_favicon = $theme_options['favicon'];
$ct_ios_icon = $theme_options['ios_icon'];
$ct_custom_css = $theme_options['custom_css'];
$ct_logo = $theme_options['logo'];

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>" />
<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_uri(); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php if($ct_favicon): ?>
<link rel="icon" type="image/png" href="<?php echo $ct_favicon; ?>" />
<?php endif; ?>
<?php if($ct_ios_icon): ?>
<link rel="apple-touch-icon" href="<?php echo $ct_ios_icon; ?>" />
<?php endif; ?>
<?php wp_enqueue_script('jquery') ?>
<?php
	if(is_singular() && get_option('thread_comments'))
		wp_enqueue_script('comment-reply');

	wp_head();
?>
<?php if(is_front_page()): ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/scripts/jquery.slider.php"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/lib/scripts/custom.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/scheme.php" />
<?php if($ct_custom_css): ?>
<style type="text/css">
<?php echo $ct_custom_css; ?>
</style>
<?php endif; ?>
</head>
<body <?php body_class(); ?>>
<div id="wrapper">
	<div id="wrapper2">
		<div id="header" class="container_12">
			<div class="grid_12 alpha omega logo">
				<a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?> - <?php echo esc_attr(get_bloginfo('description', 'display')); ?>" rel="home">
				   <img src="<?php echo $ct_logo; ?>" alt="<?php bloginfo('name'); ?>" />
				</a>
			</div>
			<div class="clear"></div>
			<div class="nav">
				<?php ct_primary_nav_menu(); ?>
			</div>
		</div>
<?php if(is_front_page()): get_template_part('slider'); endif; ?>