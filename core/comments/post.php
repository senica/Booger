<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['obj'];
	
	/****** SEARCH INDEX ******/
	//Template Variables
	$bdb->query("INSERT INTO ".PREFIX."_index (resource_id, resource_type, resource_tv, content)VALUES('".mysql_real_escape_string($obj->pageid)."','comment', 'comment', '".mysql_real_escape_string(preg_replace('{<.*?>|\s}is', ' ', $obj->comment))."')");		
	
	$parent = (isset($obj->parent)) ? $obj->parent : 0;
	$user = $bg->get_user();
	$user = ($user) ? $user->id : 0;
	$type = (isset($obj->type)) ? $obj->type : 'comment';
	if($obj->website == ""){ $website = ""; }else{
		$website = (preg_match("#[http://|https://]#", $obj->website)) ? $obj->website : 'http://'.$obj->website;
	}
	$cols = 'comment_post_id, comment_author, comment_author_email, comment_author_url, comment_content, comment_author_ip, comment_date, comment_date_gmt, comment_approved, comment_agent, comment_type, comment_parent, user_id';
	$values = "'".mysql_real_escape_string($obj->pageid)."', '".mysql_real_escape_string($obj->name)."', '".mysql_real_escape_string($obj->email)."', '".mysql_real_escape_string($website)."', '".mysql_real_escape_string($obj->comment)."', '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), '1', '".mysql_real_escape_string($_SERVER['HTTP_USER_AGENT'])."', '".mysql_real_escape_string($type)."', '".mysql_real_escape_string($parent)."', '".mysql_real_escape_string($user)."'";
	$query = $bdb->query("INSERT INTO ".PREFIX."_comments ($cols)VALUES($values)");
	$json['error'] = (!$query) ? 'false' : 'true';
	$json['pageid'] = $obj->pageid;
	$json['name'] = $obj->name;
	$json['email'] = $obj->email;
	$json['website'] = $website;
	$json['comment'] = $obj->comment;
	$json['parent'] = $parent;
	$json['date'] = date('Y-m-d H:i:s');
	$json['id'] = $bdb->get_id();
	echo json_encode($json);
?>