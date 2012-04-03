<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */

get_header(); ?>
		<div id="ribbon" class="page">
			<div class="container_12 content">
				<div class="grid_7 alpha">
					<h1><?php _e( 'Page Not Found', 'churchthemes' ); ?></h1>
				</div>
				<div class="grid_5 omega">
					<span class="tagline"><p><?php _e( 'Error 404', 'churchthemes' ); ?></p></span>
				</div>
			</div>
		</div>
		<div id="wrapper3" class="container_12">
			<div id="content" class="grid_12 alpha">
				<p><?php _e( 'Apologies, but the page you requested could not be found. Perhaps searching will help.', 'churchthemes' ); ?></p>
				<?php get_search_form(); ?>
				<p>&nbsp;</p>
				<p>&nbsp;</p>
			</div>
		</div>
<script type="text/javascript">
	// focus on search field after it has loaded
	document.getElementById('s') && document.getElementById('s').focus();
</script>
<?php get_footer(); ?>