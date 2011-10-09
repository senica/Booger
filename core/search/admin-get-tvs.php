<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//Ajax page to get template variables of a page
$id = mysql_real_escape_string($_POST['id']);
$r = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE id='".$id."'");
$content = unserialize($r->content);
$return = array();
if(is_array($content)){
	foreach($content as $k=>$v){
		array_push($return, $k);	
	}
	echo json_encode($return);
}else{
	echo '';	
}
?>