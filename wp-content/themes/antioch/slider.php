<?php

$slide_settings = get_option('ct_slide_settings');
$orderby = $slide_settings['orderby'];
$order = $slide_settings['order'];
$tags = $slide_settings['active_slide_tags'];
$num = $slide_settings['slide_limit'];

if(empty($orderby)): $orderby = 'date'; endif;
if(empty($order)): $order = 'DESC'; endif;

global $post;

if($orderby == 'views'):
	$args=array(
		'post_type' => 'slide',
		'post_status' => 'publish',
		'posts_per_page' => $num,
		'meta_key' => 'Views',
		'orderby' => 'meta_value_num',
		'order' => $order,
		'slide_tag' => $tags,
	);
else:
	$args=array(
		'post_type' => 'slide',
		'post_status' => 'publish',
		'posts_per_page' => $num,
		'orderby' => $orderby,
		'order' => $order,
		'slide_tag' => $tags,
	);
endif;
		
$query = null;
$query = new WP_Query($args);

?>
		<div id="ribbon" class="home">
			<div class="container_12">
				<div class="grid_12 alpha">
					<div class="slide_frame<?php if($query->have_posts()): else: echo ' empty'; endif; ?>">
						<div id="slider">
							<div class="slide_holder">
								<div class="mask">
									<ul>
<?php

if($query->have_posts()): while($query->have_posts()) : $query->the_post();

$sl_tagline = get_post_meta($post->ID, '_ct_sl_tagline', true);
$sl_linkurl = get_post_meta($post->ID, '_ct_sl_linkurl', true);

$img_attr = array(
	'alt'	=> trim(strip_tags($post->post_title)),
	'title'	=> '',
);

?>
										<li>
											<div class="slide">
<?php if($sl_linkurl): ?><a href="<?php echo $sl_linkurl; ?>"><?php endif; ?>
												<?php echo get_the_post_thumbnail($post->ID, 'slide', $img_attr); ?>
<?php if($sl_linkurl): ?></a><?php endif; ?>
												<div class="slide_content_holder">
													<div class="slide_content">
<?php if($sl_linkurl): ?><a href="<?php echo $sl_linkurl; ?>"><?php endif; ?>
														<h2 class="title"><span><?php the_title(); ?></span></h2>
<?php if($sl_linkurl): ?></a><?php endif; ?>
<?php if($sl_tagline): ?>
<?php if($sl_linkurl): ?><a href="<?php echo $sl_linkurl; ?>"><?php endif; ?>
														<h3 class="subtitle"><span><?php echo $sl_tagline; ?></span></h3>
<?php if($sl_linkurl): ?></a><?php endif; ?>
<?php endif; ?>
													</div>
												</div>
											</div>
										</li>
<?php endwhile; endif; wp_reset_query(); ?>
									</ul>
								</div>
							</div>
							<div class="pag_box">
								<div class="pag_frame">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>