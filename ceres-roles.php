<?php 

/*
 Plugin Name: Ceres Roles
 Plugin URI: 
 Description: Adds and customizes roles to be used in Digital Scholarship Group sites
 Version: 0.1
 Author: Patrick Murray-John
*/

require_once plugin_dir_path( __FILE__ ) . 'classes/CeresRoles.php';

register_activation_hook( __FILE__, array('CeresRoles', 'install') );
register_deactivation_hook( __FILE__, array('CeresRoles', 'uninstall') );




