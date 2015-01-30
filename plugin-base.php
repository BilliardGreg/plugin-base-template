<?php

/*
Plugin Name: Plugin Base Sample
Plugin URI: http://www.wp-411.com
Description: Plugin template for having a good starting point to build your custom plugin or application.
Author: Greg Whitehead
Version: 1.1
Author URI: http://www.gregwhitehead.us/

*/


$plugin_directory 	= "plugin-base"; 	//For use in definitions
$plugin_prefix		= "CPBP";			//For use in definitions names

define( $plugin_prefix.'_URL',plugins_url($plugin_directory) . "/");
define( $plugin_prefix.'_PATH', plugin_dir_path( __FILE__) ); 



if (class_exists('plugin_base')) {
	register_activation_hook(__FILE__, array('plugin_base','activate'));
	register_deactivation_hook(__FILE__,array('plugin_base','deactivate'));
	
	$incon_tracking = new plugin_base();	
}
