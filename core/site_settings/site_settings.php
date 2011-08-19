<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_site_settings_admin_head');
$bg->add_hook('admin-foot', 'core_site_settings_admin_js');
$bg->add_hook('admin-sidebar-top', 'core_site_settings_sidebar');
$bg->add_hook('admin-storage', 'core_site_settings_dialog');

function core_site_settings_sidebar(){
	echo '<a href="#" class="settings" title="Site Settings"></a>';	
}

function core_site_settings_admin_head(){
	global $bg;
	$bg->add_css(URL.'/core/site_settings/site_settings.css');
}

function core_site_settings_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/site_settings/site_settings.js');
}

function core_site_settings_dialog(){
	echo '<div id="core-site-settings-wrapper" title="Site Settings">';
		echo '<form id="core-site-settings-form"></form>';
	echo '</div>';
}
?>