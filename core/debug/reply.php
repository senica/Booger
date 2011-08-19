<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$json = array();
$json['error'] = false;

$obj = (object) $_POST['post'];

$insert = array(
	'comment_post_id'		=> $obj->id['value'],
	'comment_author'		=> $bg->user->name,
	'comment_author_email'	=> $bg->user->email,
	'comment_author_url'	=> $bg->user->website,
	'comment_author_ip'		=> $bg->user->ip,
	'comment_date'			=> $bdb->date(),
	'comment_date_gmt'		=> $bdb->gmt_date(),
	'comment_content'		=> $obj->text['value'],
	'comment_approved'		=> 1,
	'comment_agent'			=> $bg->user->http_agent,
	'comment_type'			=> 'comment',
	'comment_parent'		=> 0,
	'user_id'				=> $bg->user->id
	
);
$id = $bdb->insert('comments', $insert);
if($id === false){
	$json['error'] = true; $json['message'] = "Could not insert comment into database";		
}else{
	$update = array(
		'status'			=> $obj->status['value']				
	);
	$q = $bdb->update('content', $update, "id='".mysql_real_escape_string($obj->id['value'])."'");
	if($q === false){
		$json['error'] = true; $json['message'] = "Could not change the status of the bug";	
	}
}

die(json_encode($json));
?>