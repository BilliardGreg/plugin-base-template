<?php

/*
Plugin Name: Plugin Base Sample
Plugin URI: http://wpcms.ninja
Description: Plugin template for having a good starting point to build your custom plugin or application.
Author: Greg Whitehead
Version: 1.1
Author URI: http://www.gregwhitehead.us/

*/


$plugin_directory 	= "plugin-base"; 	//For use in definitions
$plugin_prefix		= "CPBP";			//For use in definitions names

define( $plugin_prefix.'_URL',plugins_url($plugin_directory) . "/");
define( $plugin_prefix.'_PATH', plugin_dir_path( __FILE__) ); 

include("class/class.plugin-base.php");

if (class_exists('plugin_base')) {
	$plugin_base = new plugin_base();	
}
