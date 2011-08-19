<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//error_reporting(0); //Turn off all errors

require_once("assets/no_change.php");

/************************************************************
* config.php
* Defines ASSETS, SITE, ADMIN, URL, SSL, PLUGINS, THEMES, THEME
* Contains database connection info.
* Contains secret key for encryption used throughout site.
* Contains URL and Secure URL validation.
* Handles Global Error Reporting
* Can be included in ajax files to make DB object available.
* Preferable to use ajax.php?file=
************************************************************/
require_once("assets/config.php");

/************************************************************
* auth.php
* Handles login session to admin.
* Checks to see if user has logged in before via cookie.
* If cookie is set, a check against database is performed.
* If cookie is not valid, cookie will be unset and admin.php
* will not be included.
************************************************************/
if( isset($_COOKIE['bg_authenticated_user']) || isset($_GET['bg_login']) || isset($_POST['bg_login']) ){
	require(ADMIN."/auth.php");
}

/************************************************************
* build.php
* Handles the main object for the site
* Creates plugin objects
************************************************************/
require_once(ASSETS."/build.php");
require_once(ASSETS."/build_plugins.php");

/************************************************************
* functions.php allows a site to perform any actions
* they might do in a plugin without building a plugin
************************************************************/
if(file_exists(THEME_DIR.'/functions.php')){ require_once(THEME_DIR.'/functions.php'); }

/************************************************************
* guid is set in .htaccess
* assets/url.php parses guid for pages 
************************************************************/
if(isset($_GET['guid'])){ require_once(ASSETS."/url.php"); }
else if(isset($_COOKIE['bg_authenticated_user'])){ require_once(ADMIN."/admin.php"); }
else{
	//No guid set, so get homepage
	$result = $bdb->get_result("SELECT guid FROM ".PREFIX."_content WHERE id = '".$bg->settings->site_home."' ");
	$_GET['guid'] = $result->guid;
	require_once(ASSETS."/url.php");
}

//Plugins have the chance to do any last minute actions before the script ends
function bg_shutdown(){ global $bg; $bg->call_hook('shutdown'); }
register_shutdown_function('bg_shutdown');
?>
