<?php require(ASSETS.'/no_direct.php'); ?>
<?php
function build_plugins($dirpath){
	global $bg, $plugins;
	$files = @scandir($dirpath);
	foreach($files as $file){
		//Check for unset plugins.  Allow plugins to set themeselves up with firstrun.php
		if( !isset($plugins->{$file}) && is_dir($dirpath.'/'.$file) && file_exists($dirpath.'/'.$file.'/firstrun.php') ){
			require_once($dirpath.'/'.$file.'/firstrun.php');
		}
		
		//If is dir and and plugin has been looked at in the admin and plugin is active (set in admin) and user has permissions to access (set in admin) and plugin entry file exist
		if( is_dir($dirpath.'/'.$file) && ($bg->user->alias == 'admin' || (isset($plugins->{$file}) && $plugins->{$file}->active == '1' && array_key_exists($plugins->{$file}->permissions, $bg->user->permissions))) && $file != '.' && $file != '..' && file_exists($dirpath.'/'.$file.'/'.$file.'.php') ){
			require_once($dirpath.'/'.$file.'/'.$file.'.php');	
		}
	}
}
$plugins = unserialize($bg->settings->core_plugins); //Get core plugin settings that were set in admin from db
build_plugins(CORE);
$plugins = unserialize($bg->settings->user_plugins); //Get user plugin settings that were set in admin from db
build_plugins(PLUGINS);
?>