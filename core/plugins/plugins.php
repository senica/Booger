<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_plugins_admin_head');
$bg->add_hook('admin-foot', 'core_plugins_admin_js');
$bg->add_hook('admin-sidebar-top', 'core_plugins_sidebar');
$bg->add_hook('admin-storage', 'core_plugins_dialog');

function core_plugins_sidebar(){
	echo '<a href="#" class="plugins" title="Manage Plugins"></a>';	
}

function core_plugins_admin_head(){
	global $bg;
	$bg->add_css(URL.'/core/plugins/plugins.css');
}

function core_plugins_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/plugins/plugins.js');
}

function core_plugins_dialog(){
	echo '<div id="core-plugins-wrapper" title="Manage Plugins">';
	echo '</div>';
}
?>