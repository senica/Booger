<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$dbid = $_POST['id'];
$results = $bdb->get_results("SELECT c.title FROM ".PREFIX."_relations as t LEFT JOIN ".PREFIX."_content as c ON t.relation_id=c.id  WHERE t.resource_id='".mysql_real_escape_string($dbid)."' AND c.type='tag'");
foreach($results as $r){
	echo '<div style="font-size:small; display:inline-block; padding:3px;"><a href="#" class="clear"></a><span class="tag">'.$r->title.'</span></div>';	
}		
?>