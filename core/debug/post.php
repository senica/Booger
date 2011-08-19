<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$json = array(); $json['error'] = false;
	$data = $_POST['obj'];
	$data['site'] = URL;			//Alert server what site the bug is coming from.
	
	if(empty($bg->settings->bug_server)){ $json['error'] = true; $json['message'] = 'Please update your Bug Server in Site Settings.'; die(json_encode($json)); }
	
	//Send data to bug server
	$fp = fsockopen($bg->settings->bug_server, 80, $errno, $errstr, 10);
	if (!$fp){ $json['error'] = true; $json['message'] = 'Could not connect to '.$bg->settings->bug_server; die(json_encode($json)); }
	$send = http_build_query($data);
	$out = "POST /core/debug/server.php HTTP/1.1\r\n";
	$out .= "Host: ".$bg->settings->bug_server."\r\n";
	$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$out .= "Content-Length: ".strlen($send)."\r\n";
	$out .= "Connection: Close\r\n\r\n";
	$out .= $send;
	fwrite($fp, $out);
	$buffer = '';
	while (!feof($fp)) {
		$buffer .= fgets($fp, 128);
	}
	fclose($fp);
	preg_match("/^content-length:\s*(\d+)\s*$/mi", $buffer, $matches);
	$length = $matches[1];	
	$content = substr($buffer, $length*-1, $length);
	
	$check = json_decode($content);
	$check->content = $data['bug']['value'];
	if($check->error === false){
		$q = $bdb->query("INSERT INTO ".PREFIX."_content (title, content, author, status, created_on, created_on_gmt, type)VALUES('".mysql_real_escape_string($check->id)."', '".mysql_real_escape_string(serialize($check))."', '".mysql_real_escape_string($bg->user->id)."', 'submitted', NOW(), FROM_UNIXTIME(UNIX_TIMESTAMP()), 'bug_ref')");
		if(!$q){ $json['error'] = true; $json['message'] = 'Could not record bug in your database, but bug has been successfully sent.'; die(json_encode($json)); }
	}
	
	die($content); //Return server response
?>