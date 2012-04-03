<?php
/*
Plugin Name: Give Online
Description: Give Online Widget
Version: 0.1.0
Author: Eli Perelman
Author URI: http://eliperelman.com
*/
?>
error_reporting(E_ALL);

add_action("widgets_init", array('GiveOnline', 'register'));

class GiveOnline {
  function control(){
    echo 'I am a control panel';
  }
  function widget($args){
    echo $args['before_widget'];
    echo $args['before_title'] . 'Your widget title' . $args['after_title'];
    echo 'I am your widget';
    echo $args['after_widget'];
  }
  function register(){
    register_sidebar_widget('Give Online', array('GiveOnline', 'widget'));
    register_widget_control('GiveOnline', array('GiveOnline', 'control'));
  }
}