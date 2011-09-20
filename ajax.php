<?php
/********************************************************
* ajax.php
* Usage: /ajax.php?file=path_to_your_file
* This file acts as a gateway to all the files in your
* system.  As such, it is a crucial point of security
* for your site.
* build.php handles the authentication of a user when
* assigning the $bg->user permissions.
* This file checks permissions on the core and plugin
* directories
* You will still need to specify require(ASSETS.'/no_direct.php')
********************************************************/
$file = $_GET['file'];
require_once('assets/config.php');
require_once('assets/build.php');
if(file_exists(THEME_DIR.'/functions.php')){ require_once(THEME_DIR.'/functions.php'); } //Added this so themes can add admin hooks.  May not be a good idea if the functions.php file gets too big
if($_GET['build-plugins'] == 'true'){ require_once('assets/build_plugins.php'); } //Some ajax calls may require that all the plugins be built, current example is the actions plugin

$dir = explode('/', dirname($file));
$dir = $dir[count($dir)-1];

//Authorization exceptions to files. Public should always be allowed to access these files regardless of ACL
$exceptions = array("admin/auth.php", "admin/login.php", "admin/logout.php", "admin/verify_cookies.php");

if(!in_array($file, $exceptions) && $bg->user->alias != 'admin'){ //Allow the admin alias to bypass everything
	$user = unserialize($bg->settings->user_plugins);
	$core = unserialize($bg->settings->core_plugins);
	$facl = unserialize($bg->settings->files_acl);
	if(!isset($_GET['file'])){ require_once(ASSETS.'/405.php'); die(); }
	if(!file_exists($file)){ require_once(ASSETS.'/404.php'); die(); }
	//Does the file have access permissions assigned to it? If so, does the user have permission to access it?
	if(isset($facl->{$file}) && !array_key_exists($facl->{$file}->permissions, $bg->user->permissions) ){ require_once(ASSETS.'/403.php'); die(); }
	//Are we requesting a file that is within the core and plugins directories and is the plugin active?
	if( (!isset($user->{$dir}) || $user->{$dir}->active == '0') && (!isset($core->{$dir}) || $core->{$dir}->active == '0') ){ require_once(ASSETS.'/401.php'); die(); }
	//Does the user have permission to access this plugin directory?
	if( !array_key_exists($user->{$dir}->permissions, $bg->user->permissions) && !array_key_exists($core->{$dir}->permissions, $bg->user->permissions) ){ require_once(ASSETS.'/403.php'); die();  }
	//Make sure this isn't a bogus ajax call trying to get to the root of the site
	if( $dir == '\\' || $dir == '/' || $dir == '.' || $dir == '..' ){ require_once(ASSETS.'/405.php'); die(); }
}
require_once($file); //If we are here, we should be good to include the file.
?>