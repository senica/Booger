<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*
* In your template, before the </body> tag, you SHOULD call the shortcode [sitefoot].
* This will allow plugins that need to add javascript to your page or any other last minute cleanup items.
* This was put into place instead of calling <?php $bg->call_hook('site-foot'); ?> because of the
* processing order of PHP.
*/
$bg->add_shortcode('sitefoot', 'core_sitefoot_init');

function core_sitefoot_init(){
	global $bg;
	$bg->call_hook('site-foot');	
}
?>