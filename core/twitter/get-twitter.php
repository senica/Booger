<?php
$options = (object) $_POST['obj'];

$json = array();
$json['error'] = false;
$json['message'] = '';

$fp = fsockopen("ssl://api.twitter.com", 443, $errno, $errstr, 30);
if (!$fp){ $json['error'] = true; $json['message'] = 'Could not connect to Twitter'; die(json_encode($json)); }
if(!empty($options)){
	$get = '?';
	foreach($options as $k=>$v){
		$get .= urlencode($k).'='.urlencode($v).'&';		
	}
	$get = substr($get, 0, strlen($get)-1);
}
$out = "GET /1/statuses/".$options->resource.".json".$get." HTTP/1.1\r\n";
$out .= "Host: api.twitter.com\r\n";
$out .= "Connection: Close\r\n\r\n";
fwrite($fp, $out);
$buffer = '';
while (!feof($fp)) {
	$buffer .= fgets($fp, 128);
}
fclose($fp);
preg_match("/^content-length:\s*(\d+)\s*$/mi", $buffer, $matches);
$length = $matches[1];

if($length <= 2){ $json['error'] = "end"; $json['message'] = ''; die(json_encode($json)); }

$content = substr($buffer, $length*-1, $length);
$content = json_decode($content);

if($content === NULL){ $json['error'] = true; $json['message'] = 'Not a valid response from Twitter.'; die(json_encode($json)); } //Could not decode JSON so let's not output the content as appending it to the page may give undesired results

if(!empty($content)){
	//$json['message'] .= '<div class="result-wrapper">'; //Loop tweet and not result
	foreach($content as $tweet){
		$json['message'] .= '<div class="tweet-wrapper">';
		$json['message'] .= '<div class="name"><a href="'.$tweet->user->url.'">'.$tweet->user->name.'</a></div>';
		$json['message'] .= '<div class="screen-name"><a href="http://www.twitter.com/'.$tweet->user->screen_name.'">@'.$tweet->user->screen_name.'</a></div>';
		$json['message'] .= '<div class="created-at">'.$tweet->created_at.'</div>';
		$json['message'] .= '<div class="text">'.$tweet->text.'</div>';
		$json['message'] .= '</div>';
	}
	//$json['message'] .= '</div>';
}
echo json_encode($json);
?>