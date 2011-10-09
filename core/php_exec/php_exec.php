<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//This gear allows you to add php code to your edtiable areas within a template.
//The template will replace <?php and ? > with <!--php and ?--> respectively, so they are not visible in html
//Here we reverse the process and execute the php code
$bg->content_filter('core_php_exec_init');

function core_php_exec_init($content){
	global $bg, $bdb;
	ob_start();
	$content = preg_replace("|<!--\?php(.*?)\?-->|is", '<?php'."$1".'?>', $content);
	include "data://text/plain;base64,".base64_encode($content);
	//Plain text does not work for some things  like pasted images; which is a bummer since errors show up as base64 code
	//include "data://text/plain,".$content;
	$c = ob_get_contents();
	ob_end_clean();
	return $c;
}
?>