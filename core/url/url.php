<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_shortcode('core_url', 'core_url_func');

function core_url_func($obj){
	global $bg,$bdb;
	$opt = $obj->options;
	//Page id to url [url {"pgid":1}]
	if(isset($opt->pgid)){
		$result = $bdb->get_result("SELECT guid FROM ".PREFIX."_content WHERE id='".$opt->pgid."'");
		echo URL.'/'.$result->guid;
	}
	return "noparse"; //Don't parse if noparse is set in url.  This shortcode may be used inside html tags, and the admin's handling of editing shortcodes will malform the html code.
}
?>