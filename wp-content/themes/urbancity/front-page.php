<?php
/**
 * The homepage template file.
 *
 * @package WordPress
 * @subpackage ChurchThemes
 */

get_header(); ?>
<div id="wrapper3" class="container_12">
   <div class="grid_4 alpha sidebar-left widget custom-grid">
      <?php dynamic_sidebar('Homepage Left'); ?>
   </div>
   <div class="grid_4 sidebar-center widget custom-grid">
      <?php dynamic_sidebar('Homepage Center'); ?>
   </div>
   <div class="grid_4 omega sidebar-right widget custom-grid">
      <?php dynamic_sidebar('Homepage Right'); ?>
   </div>
    </div>
<?php get_footer(); ?>