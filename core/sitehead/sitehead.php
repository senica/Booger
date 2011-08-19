<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*
* In your template, before the </head> tag, you SHOULD call the shortcode [sitehead].
* This will allow plugins that need to add javascript or css to your page.
* This was put into place instead of calling <?php $bg->call_hook('site-head'); ?> because of the
* processing order of PHP.
*/
$bg->add_shortcode('sitehead', 'core_sitehead_init');

function core_sitehead_init(){
	global $bg;
	$bg->call_hook('site-head');	
}
?>