<?php
	$absolute_path = __FILE__;
	$path_to_file = explode('wp-content', $absolute_path);
	$path_to_wp = $path_to_file[0];
	
	require_once($path_to_wp.'/wp-load.php');
	
	$theme_options = get_option('ct_theme_options');
	
	$ct_main_color = $theme_options['main_color'];
	if(empty($ct_main_color) || $ct_main_color == '#'): $ct_main_color = '#629fa6'; endif;
	
	$ct_logo = $theme_options['logo'];
	
	$ct_logo_width = $theme_options['logo_width'];
	if(empty($ct_logo_width)): $ct_logo_width = '277'; endif;
	
	$ct_logo_height = $theme_options['logo_height'];
	if(empty($ct_logo_height)): $ct_logo_height = '25'; endif;
	
	$ct_logo_top_margin = $theme_options['logo_top_margin'];
	if(empty($ct_logo_top_margin)): $ct_logo_top_margin = '32'; endif;
	
	header('Content-type: text/css');
	header('Cache-control: must-revalidate');
?>
#header .logo {
	margin-top:<?php echo $ct_logo_top_margin; ?>px;
}

#header .logo a {
<?php if($ct_logo): ?>
	background:url(<?php echo $ct_logo; ?>) no-repeat;
<?php endif; ?>
	width:<?php echo $ct_logo_width; ?>px;
	height:<?php echo $ct_logo_height; ?>px;
}

a,
a:visited {
	color:<?php echo $ct_main_color; ?>;
}

h1 a:hover,
h2 a:hover,
h3 a:hover,
h4 a:hover,
h5 a:hover,
h6 a:hover,
h1 a:visited:hover,
h2 a:visited:hover,
h3 a:visited:hover,
h4 a:visited:hover,
h5 a:visited:hover,
h6 a:visited:hover {
	color:<?php echo $ct_main_color; ?>;
}

::selection,
::-moz-selection {
	background:<?php echo $ct_main_color; ?>;
}

blockquote {
	border-left:3px solid <?php echo $ct_main_color; ?>;
}

.navbar ul li a:hover {
	color:<?php echo $ct_main_color; ?>;
}

.mask .slide_content h3.subtitle {
	color:<?php echo $ct_main_color; ?>;
}

.pag_box ul a:hover, .pag_box ul .selected a {
	background:<?php echo $ct_main_color; ?>;
}

.pagination li:hover,
.pagination li.active {
	background:<?php echo $ct_main_color; ?>;
}

.search-excerpt {
	color:<?php echo $ct_main_color; ?>;
}


/* Events Manager Styles */

table.em-calendar td.eventful a,
table.em-calendar td.eventful-today a {
	color:<?php echo $ct_main_color; ?> !important;
}
.ui-state-hover {
	color:<?php echo $ct_main_color; ?> !important;
}
.ui-datepicker-today .ui-state-highlight {
	background:<?php echo $ct_main_color; ?> !important;
}


/* Reftagger Plugin Styles */

.lbsTooltipFooter a:hover {
	color:<?php echo $ct_main_color; ?>;
}