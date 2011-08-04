<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$core = $_POST['core'];
$user = $_POST['user'];
$functions = $_POST['functions'];
$files = $_POST['files'];

$error = false;

//Add core plugin values to settings table in db
$core_plugin = array();
if(!empty($core)){
foreach($core as $c){
	$core_plugin[$c['name']]['active'] = $c['active'];
	$core_plugin[$c['name']]['permissions'] = $c['permissions'];
	$core_plugin[$c['name']] = (object) $core_plugin[$c['name']]; //change to object
}
}
$core_plugin = (object) $core_plugin; //change to object;
$q = $bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('core_plugins', '".mysql_real_escape_string(serialize($core_plugin))."') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");
if(!$q){ $error = true; }

//Add user plugin values to settings table in db
$user_plugin = array();
if(!empty($user)){
foreach($user as $u){
	$user_plugin[$u['name']]['active'] = $u['active'];
	$user_plugin[$u['name']]['permissions'] = $u['permissions'];
	$user_plugin[$u['name']] = (object) $user_plugin[$u['name']]; //change to object
}
}
$user_plugin = (object) $user_plugin; //change to object;
$q = $bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('user_plugins', '".mysql_real_escape_string(serialize($user_plugin))."') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");
if(!$q){ $error = true; }

//Add function values to settings table in db
$functions_plugin = array();
if(!empty($functions)){
foreach($functions as $u){
	$functions_plugin[$u['name']]['active'] = $u['active'];
	$functions_plugin[$u['name']]['permissions'] = $u['permissions'];
	$functions_plugin[$u['name']] = (object) $functions_plugin[$u['name']]; //change to object
}
}
$functions_plugin = (object) $functions_plugin; //change to object;
$q = $bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('functions_acl', '".mysql_real_escape_string(serialize($functions_plugin))."') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");
if(!$q){ $error = true; }

//Add file values to settings table in db
$files_plugin = array();
if(!empty($files)){
foreach($files as $u){
	$files_plugin[$u['name']]['permissions'] = $u['permissions'];
	$files_plugin[$u['name']] = (object) $files_plugin[$u['name']]; //change to object
}
}
$files_plugin = (object) $files_plugin; //change to object;
$q = $bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('files_acl', '".mysql_real_escape_string(serialize($files_plugin))."') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");
if(!$q){ $error = true; }

echo (!$error) ? 'true' : 'false';

?>