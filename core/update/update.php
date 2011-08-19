<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-sidebar-top', 'core_update_admin_sidebar_top');
$bg->add_hook('admin-head', 'core_update_admin_css');
$bg->add_hook('admin-storage', 'core_update_admin_storage');
$bg->add_hook('admin-foot', 'core_update_admin_js');

function core_update_admin_sidebar_top(){
	echo '<a href="#" class="update" title="Check for Updates"></a>';	
}

function core_update_admin_css(){
	global $bg;
	$bg->add_css(URL.'/core/update/update.css');
}

function core_update_admin_storage(){
	echo '<div id="core-update-admin-dialog" title="Update Manager"></div>';	
}

function core_update_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/update/update.js');
}
?>