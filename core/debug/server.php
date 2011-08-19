<?php
/*************************************************
* Server.php will be called remotely, so we can't
* assume any predefined variables until config is
* called.
*************************************************/
require_once("../../assets/config.php");
$json = array();
$json['error'] = false;
if(empty($_POST)){ $json['error'] = true; $json['message'] = 'Server did not receive a correct bug report'; }
else{
	$data = $_POST;
	//$report = $data['bug-debug-info']['value'];
	//$bug = $data['bug']['value'];
	//$site = $data['site'];
	$id = uniqid('bug_');
	
	$q = $bdb->query("INSERT INTO ".PREFIX."_content (title, content, status, created_on, created_on_gmt, type)VALUES('".mysql_real_escape_string($id)."', '".mysql_real_escape_string(serialize($data))."', 'new', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), 'bug')");
	if(!$q){ $json['error'] = true; $json['message'] = 'Could not record bug in server database.'; }
	else{
		$json['id'] = $bdb->get_id();
		$json['message'] = 'We have received your bug report.';
		$json['title'] = $id;
	}
}
$return = json_encode($json);
header("Content-Length: ".strlen($return)."\r\n");
die($return);
?>