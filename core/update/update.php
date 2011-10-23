<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_js($bg->plugin_url(false).'/update.js', 'admin-foot');
$bg->add_css($bg->plugin_url(false).'/update.css', 'admin-head');

$bg->add_hook('admin-sidebar-top', 'core_update_admin_sidebar_top');
$bg->add_hook('admin-storage', 'core_update_admin_storage');

function core_update_admin_sidebar_top(){
	echo '<a href="#" class="update" title="Check for Updates"></a>';	
}

function core_update_admin_storage(){
	echo '<div id="core-update-admin-dialog" title="Update Manager"></div>';	
}
?>