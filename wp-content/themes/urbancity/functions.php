<?php

//if (!empty($_SERVER['SCRIPT_FILENAME']) && 'functions.php' == basename($_SERVER['SCRIPT_FILENAME']))
//die ('<h1>Access Denied</h1>');

/*---------------------------------------------------------------------------------------*/
/* Start ChurchThemes Functions - Please DO NOT edit this section! :)                    */
/*---------------------------------------------------------------------------------------*/

// Set path to function libraries
$admin_path = TEMPLATEPATH . '/lib/admin/';
$functions_path = TEMPLATEPATH . '/lib/functions/';

// Load each admin function
require_once ($admin_path . 'theme-options.php');		// Custom theme options
require_once ($admin_path . 'upgrade-theme.php');		// Upgrade theme page
require_once ($admin_path . 'social-footer.php');		// Social footer options
require_once ($admin_path . 'metabox.php');				// Custom meta box
require_once ($admin_path . 'sidebars.php');			// Custom sidebars
require_once ($admin_path . 'post-settings.php');		// Post settings
require_once ($admin_path . 'slide-settings.php');		// Slide settings
require_once ($admin_path . 'reorder/reorder.php');		// Reorder plugin

// Load each function
require_once ($functions_path . 'functions.php');		// Specific theme functions
require_once ($functions_path . 'search-excerpt.php');	// Customize excerpt on searches
require_once ($functions_path . 'post-types.php');		// Custom post types

if (!function_exists( 'entry_views_update' ))
require_once ($functions_path . 'entry-views.php');		// Entry views extension

/*---------------------------------------------------------------------------------------*/
/* End ChurchThemes Functions - Feel free to add your own custom functions below!        */
/*---------------------------------------------------------------------------------------*/

remove_filter ('the_content', 'wpautop');



?>