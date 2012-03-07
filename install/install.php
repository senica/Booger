<?php
$version = (isset($_GET['version'])) ? $_GET['version'] : '0.16'; //Change with version releases
if(!isset($_POST['post'])){ die('This page should be accessed by <a href="index.php">/install/index.php</a>'); }
foreach($_POST['post'] as $k=>$v){ ${$k} = $v['value']; }

//Perform Checks
$json = array(); $json['error_type'] = array(); $json['error'] = false;
$link = @mysql_connect($db_server, $db_user, $db_pass);
if(!$link){ $json['error'] = true; array_push($json['error_type'], 'db_server'); }
else if(!@mysql_select_db($db_db, $link)){ $json['error'] = true; array_push($json['error_type'], 'db_db'); }
if(!is_writable('../assets/config.php') && !isset($config)){ $json['error'] = true; array_push($json['error_type'], 'config'); }

if($json['error'] === true){ die(json_encode($json)); }

//Write config file
if(is_writable('../assets/config.php')){
	$content = @file_get_contents('../assets/config.php');
	$content = preg_replace('/(\$bg_server\s+=\s+")(";)/s', '$1'.$db_server.'$2', $content);
	$content = preg_replace('/(\$bg_user\s+=\s+")(";)/s', '$1'.$db_user.'$2', $content);
	$content = preg_replace('/(\$bg_pass\s+=\s+")(";)/s', '$1'.$db_pass.'$2', $content);
	$content = preg_replace('/(\$bg_db\s+=\s+")(";)/s', '$1'.$db_db.'$2', $content);
	$content = preg_replace('/(\$bg_key\s+=\s+\')[^\']*(\';)/s', '${1}'.$site_key.'$2', $content);
	$content = preg_replace('/(define\(\'PREFIX\'\,\s+\')(\'\);)/s', '$1'.$db_prefix.'$2', $content);
	$test = @file_put_contents('../assets/config.php', $content);
	if($test === false){ $json['error'] = true; array_push($json['error_type'], 'config_write'); }
}

//Reassign variables with mysql_real_string_escape
foreach($_POST['post'] as $k=>$v){ ${$k} = mysql_real_escape_string($v['value']); }
require_once("sql.php");
$lines = explode(PHP_EOL, $query); // run each query from $query from sql.php
$q = '';
foreach($lines as $line){
	if(trim($line) == ''){}
	else if(substr(trim($line), -1, 1) == ';'){
		$q .= $line;
		$result = mysql_query($q, $link);
		//echo mysql_error($link)."\r\n\r\n";
		if($result === false){ $json['error'] = true; array_push($json['error_type'], 'query'); }
		$q = '';
	}else{ $q .= $line; }
}

if($json['error'] === true){ die(json_encode($json)); }

die(json_encode($json)); //Complete

?>