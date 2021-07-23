<?php 

/*
 Plugin Name: Ceres Roles
 Plugin URI: 
 Description: Adds and customizes roles to be used in Digital Scholarship Group sites
 Version: 0.1
 Author: Patrick Murray-John
*/

// @TODO I could probably only require this if it isn't already installed and active
require_once plugin_dir_path( __FILE__ ) . 'classes/CeresRoles.php';

register_activation_hook( __FILE__, array('CeresRoles', 'activate') );
register_deactivation_hook( __FILE__, array('CeresRoles', 'deactivate') );




