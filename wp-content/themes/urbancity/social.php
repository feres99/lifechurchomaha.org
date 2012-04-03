<?php

$social_footer_settings = get_option('ct_social_footer_settings');
if(isset($social_footer_settings['social_footer'])):
	$social = $social_footer_settings['social_footer'];
else:
	$social = null;
endif;
$title = $social_footer_settings['title'];
$facebook = $social_footer_settings['facebook'];
$twitter = $social_footer_settings['twitter'];
$flickr = $social_footer_settings['flickr'];
$youtube = $social_footer_settings['youtube'];
$vimeo = $social_footer_settings['vimeo'];

if(empty($title)): $title = __( 'Connect With Us', 'churchthemes' ); endif;

?>
<?php if(($social == 'on' || empty($social)) && ($facebook || $twitter || $flickr || $youtube || $vimeo)): ?>
		<div class="container_12">
			<div class="grid_12 alpha social_bar">
				<div class="grid_4 alpha title">
					<h3><?php echo $title; ?></h3>
				</div>
				<div class="grid_8 omega connect">
					<ul>
						<?php if($vimeo): ?><li><a href="<?php echo $vimeo; ?>" class="vimeo"><?php _e( 'Vimeo', 'churchthemes' ); ?></a></li><?php endif; ?>
						<?php if($youtube): ?><li><a href="<?php echo $youtube; ?>" class="youtube"><?php _e( 'YouTube', 'churchthemes' ); ?></a></li><?php endif; ?>
						<?php if($flickr): ?><li><a href="<?php echo $flickr; ?>" class="flickr"><?php _e( 'Flickr', 'churchthemes' ); ?></a></li><?php endif; ?>
						<?php if($twitter): ?><li><a href="<?php echo $twitter; ?>" class="twitter"><?php _e( 'Twitter', 'churchthemes' ); ?></a></li><?php endif; ?>
						<?php if($facebook): ?><li><a href="<?php echo $facebook; ?>" class="facebook"><?php _e( 'Facebook', 'churchthemes' ); ?></a></li><?php endif; ?>
					</ul>
				</div>
			</div>
		</div>
<?php endif; ?>