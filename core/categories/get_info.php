<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$result = $bdb->get_result("SELECT id,title,guid,template,parent_id,content FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($_POST['id'])."'");
if(!$result){ echo 'false'; }else{
	$content = unserialize($result->content);
	$result->description = $content["description"];
	echo json_encode($result);
}
?>