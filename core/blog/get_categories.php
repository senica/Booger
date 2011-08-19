<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$dbid = $_POST['id'];
function tree($id,$level=1){
	global $bdb, $dbid;
	$results = $bdb->get_results("SELECT c.id,c.title,t.relation_id FROM ".PREFIX."_content as c LEFT JOIN ".PREFIX."_relations as t ON t.resource_id='$dbid' AND t.relation_id=c.id  WHERE c.type='category' AND c.parent_id='$id'");
	$margin = 5*$level;
	foreach($results as $r){
		echo '<div style="margin-left:'.$margin.'px"><input type="checkbox" value="'.$r->id.'" '.((isset($r->relation_id))?'checked="yes"':'').' /> '.$r->title.'</div>';	
		tree($r->id,$level+1);
	}		
}
tree(0);
?>