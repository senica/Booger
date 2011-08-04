<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_debug_admin_head');
$bg->add_hook('admin-foot', 'core_debug_admin_js');
$bg->add_hook('admin-sidebar-top', 'core_debug_sidebar');
$bg->add_hook('admin-storage', 'core_debug_dialog');

function core_debug_sidebar(){
	echo '<a href="#" class="debug" title="Debug Info"></a>';	
}

function core_debug_admin_head(){
	global $bg;
	$bg->add_css(URL.'/core/debug/debug.css');
}

function core_debug_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/debug/debug.js');
}

function core_debug_dialog(){
	echo '<div id="core-debug-wrapper" title="Debug Info">';
	echo '</div>';
}
?>