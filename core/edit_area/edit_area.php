<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*************************************
* include edit_area plugin
*************************************/
$bg->add_hook('admin-head', 'core_edit_area_plugin');

function core_edit_area_plugin(){
	global $bg;
	$bg->add_js("core/edit_area/edit_area_full.js");
}
?>