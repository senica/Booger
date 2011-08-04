<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$cols = array("id","title","guid","template","viewable_by","allow_comments","allow_pingbacks","publish_on","status","author"); 
$list = '';
foreach($cols as $col){
	$list .= $col.',';	
}
$list = substr($list,0,strlen($list)-1);
$result = $bdb->get_result("SELECT $list FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($_POST['id'])."'");
if(!$result){ echo 'false'; }else{ echo json_encode($result); }
?>