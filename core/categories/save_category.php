<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$obj = (object) $_POST['obj'];

function test_link($t, $c){
	global $bdb, $obj;
	($c == 1) ? $ti = $t : $ti = $t.'-'.$c;
	$results = $bdb->get_results("SELECT id FROM ".PREFIX."_content WHERE guid = '".$ti."' ".(($obj->update == 'true')?"AND id != '".$obj->id."'":'') );
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
$permalink = test_link($obj->permalink, 1);

//Get original content for category
$result = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($obj->id)."'");
$content = unserialize($result->content);
$content['description'] = $obj->description;
if($content['content'] == ''){
	if(!isset($bg->settings->site_category_content) || $bg->settings->site_category_content == ''){
		$content['content'] = "[page-list {'loop':2, 'length':400, 'filter':{'category':'[page-id]'} }]<div><br></div><div>[category-prev][category-next]</div>"; //Create Post List By Default	
	}else{
		$content['content'] = $bg->settings->site_category_content;		
	}
}
$content = serialize($content);

if($obj->update == "true"){
	$query = "UPDATE ".PREFIX."_content SET title='".mysql_real_escape_string($obj->name)."', guid='".mysql_real_escape_string($permalink)."', template='".mysql_real_escape_string($obj->template)."', viewable_by='0', allow_comments='0', allow_pingbacks='0', publish_on='0000-00-00 00:00:00', status='published', parent_id='".mysql_real_escape_string($obj->parent)."', menu_order='0', author='0', modified=NOW(), modified_gmt=FROM_UNIXTIME(UNIX_TIMESTAMP()), content='".mysql_real_escape_string($content)."' WHERE id='".mysql_real_escape_string($obj->id)."'";	
}else{
	$query = "INSERT INTO ".PREFIX."_content (title, guid, template, viewable_by, allow_comments, allow_pingbacks, publish_on, status, parent_id, menu_order, author, created_on, created_on_gmt, modified, modified_gmt, type, content)";
	$query .= "VALUES('".mysql_real_escape_string($obj->name)."', '".mysql_real_escape_string($permalink)."', '".mysql_real_escape_string($obj->template)."', '0', '0', '0', '0000-00-00 00:00:00', 'published', '".mysql_real_escape_string($obj->parent)."', '0', '0', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), 'category', '".mysql_real_escape_string($content)."' )";
}

if(!$bdb->query($query)){
	$json['error'] = 'true';	
}else{
	$json['error'] = 'false';
	$json['permalink'] = $permalink;
	$json['id'] = ($obj->update == "true") ? $obj->id : $bdb->get_id();
	$json['title'] = $obj->name;
	$json['parent'] = $obj->parent;
}
echo json_encode($json);

?>