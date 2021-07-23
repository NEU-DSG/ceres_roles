

<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit();
}
	

require_once plugin_dir_path( __FILE__ ) . 'classes/CeresRoles.php';


$ceresRoles = new CeresRoles;
$ceresRoles->uninstall();

