<?php require(ASSETS.'/no_direct.php'); ?>
<?php
foreach($_POST as $k=>$v){
	${$k} = @mysql_real_escape_string($v);
}
function test_link($t, $c){
	global $bdb, $update, $id;
	($c == 1) ? $ti = $t : $ti = $t.'-'.$c;
	$results = $bdb->get_results("SELECT id FROM ".PREFIX."_content WHERE guid = '".$ti."' ".(($update == 'true')?"AND id != '$id'":'') );
	if(count($results) > 0){
		$c = $c+1;
		$temp = explode('-', $t);
		if(is_numeric($temp[count($temp)-1])){
			array_pop($temp);
			$t = implode('-', $temp);
		}
		$ti = test_link($t, $c);	
	}
	return $ti;
}

$permalink = test_link($permalink, 1);
$publish_on = $publishDate.' '.$publishTime;

if($update == "true"){
	$query = "UPDATE ".PREFIX."_content SET title='$title', guid='$permalink', template='$template', viewable_by='$viewable', allow_comments='".(($comments=="true")?'1':'0')."', allow_pingbacks='".(($pingbacks=="true")?'1':'0')."', publish_on='$publish_on', status='$status', author='$author', modified=NOW(), modified_gmt=FROM_UNIXTIME(UNIX_TIMESTAMP()) WHERE id='$id'";	
}else{
	$query = "INSERT INTO ".PREFIX."_content (title, guid, template, viewable_by, allow_comments, allow_pingbacks, publish_on, status, author, created_on, created_on_gmt, modified, modified_gmt, type)";
	$query .= "VALUES('$title', '$permalink', '$template', '$viewable', '".(($comments=="true")?'1':'0')."', '".(($pingbacks=="true")?'1':'0')."', '$publish_on', '$status', '$author', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), 'post' )";
}

$q = $bdb->query($query);
$id = ($update == "true") ? $id : $bdb->get_id();

if($q){
	$bdb->query("DELETE FROM ".PREFIX."_relations WHERE resource_id = '$id'");
	
	//Categories
	foreach($_POST['categories'] as $cat){
		$bdb->query("INSERT INTO ".PREFIX."_relations (resource_id, relation_id)VALUES('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($cat)."')");	
	}
	
	//Tags
	foreach($_POST['tags'] as $tag){
		$t = $bdb->get_result("SELECT id FROM ".PREFIX."_content WHERE title='".mysql_real_escape_string($tag)."' AND type='tag'");
		if(!$t){
			$update = false; //Set this for the test_link
			$tag_link = test_link($tag, 1);
			$tq = "INSERT INTO ".PREFIX."_content (title, guid, template, viewable_by, allow_comments, allow_pingbacks, publish_on, status, parent_id, menu_order, author, created_on, created_on_gmt, modified, modified_gmt, type, content)";
			$tq .= "VALUES('".mysql_real_escape_string($tag)."', '".mysql_real_escape_string($tag_link)."', '', '0', '0', '0', '0000-00-00 00:00:00', 'published', '0', '0', '0', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), 'tag', '' )";
			$bdb->query($tq);
			$tid = $bdb->get_id();
		}else{
			$tid = $t->id;	
		}
		$bdb->query("INSERT INTO ".PREFIX."_relations (resource_id, relation_id)VALUES('".mysql_real_escape_string($id)."', '".mysql_real_escape_string($tid)."')");	
	}
}

echo (!$q) ? "false" : $permalink; 
?>