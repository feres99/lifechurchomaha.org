<?php

$post_settings = get_option('ct_post_settings');
$orderby = $post_settings['orderby'];
$order = $post_settings['order'];

if(empty($orderby)): $orderby = 'date'; endif;
if(empty($order)): $order = 'DESC'; endif;

global $post;

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

if($orderby == 'views'):
	$args=array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'paged' => $paged,
		'meta_key' => 'Views',
		'orderby' => 'meta_value_num',
		'order' => $order,
		'tag' => get_query_var('tag'),
		'cat' => get_query_var('cat'),
		'year' => get_query_var('year'),
		'monthnum' => get_query_var('monthnum'),
		's' => get_query_var('s'),
	);
else:
	$args=array(
		'post_type' => 'post',
		'post_status' => 'publish',
		'paged' => $paged,
		'orderby' => $orderby,
		'order' => $order,
		'tag' => get_query_var('tag'),
		'cat' => get_query_var('cat'),
		'year' => get_query_var('year'),
		'monthnum' => get_query_var('monthnum'),
		's' => get_query_var('s'),
	);
endif;
		
$query = null;
$query = new WP_Query($args);

$i = 0;

if($query->have_posts()): while($query->have_posts()): $query->the_post();

$i++;

$img_atts = array(
	'alt'	=> trim(strip_tags($post->post_title)),
	'title'	=> trim(strip_tags($post->post_title)),
);
?>
<div <?php if($i == 1): echo post_class('first'); else: post_class(); endif; ?>>
	<div class="date"><?php the_time(get_option('date_format')); ?></div>
	<h2><a href="<?php the_permalink(); ?>" title="<?php printf(esc_attr__('Permalink to %s', 'churchthemes'), the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	<h4><?php the_author(); ?></h4>
	<div class="excerpt">
		<div class="image"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" rel="bookmark"><?php echo get_the_post_thumbnail($post->ID, 'archive', $img_atts); ?></a></div>
		<p><?php the_excerpt(); ?><p>
	</div>
	<div class="clear"></div>
</div>
<?php endwhile; else: ?>
<div class="post sermon first">
	<h2><?php _e('No results found', 'churchthemes'); ?></h2>
	<p><?php _e('Sorry, nothing was found matching that criteria. Please try your search again.', 'churchthemes'); ?></p>
</div>
<?php endif; ?>
<?php if($query->max_num_pages > 1): pagination($query->max_num_pages); endif; ?>