<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
$data = file_get_contents('php://input');
//$headers = getallheaders(); //Can't use as it only works on apache
$upload_dir  = preg_replace('@\\\@', '/', $_GET['X-Save-Location']);
$u = realpath($upload_dir);
$s = realpath($SITE);
$pos = strripos($u, $s);
if($pos !== false){
	$f = preg_replace('@\\\@', '/', substr($u, strlen($s)));
}

if(file_put_contents($upload_dir.'/'.$_GET['X-File-Name'], $data)) {
	$url = URL.'/'.$f.'/'.$_GET['X-File-Name'];
	$file = $upload_dir.'/'.$_GET['X-File-Name'];
	echo '{"msg":"true", "url":"'.$url.'", "size":"'.sprintf("%u", filesize($file)).'", "dir":"'.$upload_dir.'", "loc":"'.$file.'", "name":"'.$_GET['X-File-Name'].'"}';
}else{ echo '{"msg":"false"}'; }

?>