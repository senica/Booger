<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('site-head', 'core_jquery_func');

function core_jquery_func(){
	global $bg;
	$bg->add_js(URL.'/assets/js/jquery.min.js');
}
?>