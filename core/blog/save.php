<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/****** SEARCH INDEX ******/
//Template Variables
$bdb->query("DELETE FROM ".PREFIX."_index WHERE resource_id = '".mysql_real_escape_string($_POST['id'])."' ");
foreach($_POST['data'] as $k=>$v){
	if(trim($v) != ""){
		$bdb->query("INSERT INTO ".PREFIX."_index (resource_id, resource_type, resource_tv, content)VALUES('".mysql_real_escape_string($_POST['id'])."','post', '".mysql_real_escape_string($k)."', '".mysql_real_escape_string(preg_replace('{<.*?>|\s}is', ' ', $v))."')");		
	}
}
//We dont' search globals on posts
//Comments
$results = $bdb->get_results("SELECT comment_content FROM ".PREFIX."_comments WHERE comment_post_id='".$_POST['id']."'");
foreach($results as $r){
	if(trim($r->comment_content) != ""){
		$bdb->query("INSERT INTO ".PREFIX."_index (resource_id, resource_type, resource_tv, content)VALUES('".mysql_real_escape_string($_POST['id'])."','comment', 'comment', '".mysql_real_escape_string(preg_replace('{<.*?>|\s}is', ' ', $r->comment_content))."')");		
	}
}


$data = serialize($_POST['data']);
$id = $_POST['id'];

//Make revision entry
$result = $bdb->get_result("SELECT * FROM ".PREFIX."_content WHERE id='$id'");
if($result !== false){
	$query = "INSERT INTO ".PREFIX."_content (title, content, template, viewable_by, allow_comments, allow_pingbacks, publish_on, status, parent_id, menu_order, author, created_on, created_on_gmt, modified, modified_gmt, type)";
	//guid is not saved as it needs to be unique in the database.  Parent id is set to the current model
	$query .= "VALUES('".mysql_real_escape_string($result->title)."', '".mysql_real_escape_string($result->content)."', '".mysql_real_escape_string($result->template)."', '".mysql_real_escape_string($result->viewable_by)."', '".mysql_real_escape_string($result->allow_comments)."', '".mysql_real_escape_string($result->allow_pingbacks)."', '".mysql_real_escape_string($result->publish_on)."', '".mysql_real_escape_string($result->status)."', '".mysql_real_escape_string($id)."', '".mysql_real_escape_string($result->menu_order)."', '".mysql_real_escape_string($result->author)."', '".mysql_real_escape_string($result->created_on)."', '".mysql_real_escape_string($result->created_on_gmt)."', '".mysql_real_escape_string($result->modified)."', '".mysql_real_escape_string($result->modified_gmt)."', 'revision' )";
	$bdb->query($query);
}

$query = $bdb->query("UPDATE ".PREFIX."_content SET author='".mysql_real_escape_string($bg->user->id)."', content = '".mysql_real_escape_string($data)."', modified=NOW(), modified_gmt=FROM_UNIXTIME(UNIX_TIMESTAMP()) WHERE id='$id'");
if(!$query){ echo "false"; }else{ echo "true"; }
?>