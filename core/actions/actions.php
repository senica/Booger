<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*****************************************************
* Actions are performed form the admin.  This plugins
* simply provides a means of calling action hooks
* that plugins create.
* Examples of actions might be: generate a sales
* report, display site traffic, backup site, etc.
*****************************************************/
$bg->add_css(URL.'/core/actions/actions.css', 'admin-head');
$bg->add_js(URL.'/core/actions/actions.js', 'admin-foot');

$bg->add_hook('admin-sidebar-top', 'core_actions_sidebar');
$bg->add_hook('admin-storage', 'core_actions_dialog');

function core_actions_sidebar(){
	echo '<a href="#" class="actions" title="Perform Actions"></a>';	
}

function core_actions_dialog(){
	global $bg;
	echo '<div id="core-actions-wrapper" title="Perform Actions">';
	echo '</div>';
}
?>