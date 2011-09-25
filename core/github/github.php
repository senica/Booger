<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/********************
* Get Commit Lists from Github
* This is an example of a url created: https://api.github.com/repos/senica/booger/git/refs/tags
* Documentation on Github API here: http://developer.github.com/v3/git/refs/
********************/
$bg->add_shortcode('github', 'core_github_init');


//Get response from Github
function core_github_com($url, $prefix='api'){
	$fp = fsockopen("ssl://".$prefix.".github.com", 443, $errno, $errstr, 30);
	if (!$fp){ echo 'Could not connect to Github'; return false; }
	$out = "GET /".$url." HTTP/1.1\r\n";
	$out .= "Host: ".$prefix.".github.com\r\n";
	$out .= "Connection: Close\r\n\r\n";
	fwrite($fp, $out);
	$buffer = '';
	while (!feof($fp)) {
		$buffer .= fgets($fp, 128);
	}
	fclose($fp);
	preg_match("/^content-length:\s*(\d+)\s*$/mi", $buffer, $matches);
	$length = $matches[1];	
	$content = substr($buffer, $length*-1, $length);
	switch($prefix){
		case "api":
			return json_decode($content);
			break;
		case "raw":
			return $content;
			break;
	}		
}

//Parse URLs to get what we need for the next call
function core_github_parse($url){
	preg_match("/https:\/\/([^\/]+)\/(.*)/is", $url, $matches);
	$return = array();
	$return['domain'] = $matches[1];
	$return['url'] = $matches[2];
	return $return;
}

function core_github_process($r, $resource, $options){
	switch($resource){
		case "tags":
			if(is_array($r)){
				if(!empty($options->reverse)){ $r = array_reverse($r); } //Reverse Ordering of Tags
				foreach($r as $c){
					//We are on a list of tags, so get the individual tag info
					if(empty($c->tag) && $c->object->type == "tag"){
						$url = core_github_parse($c->object->url);
						$respo = core_github_com($url['url']);
						core_github_process($respo, $resource, $options);
					}
				}
			}else if(is_object($r)){
				//We should have just the tag data
				if(!empty($r->tag)){
					echo '<div class="core-github tag-wrapper '.$options->class.'">';
					echo '<div class="core-github tag-version '.$options->class.'"><a class="core-github tag-link '.$options->class.'" href="https://www.github.com/'.$options->user.'/'.$options->repo.'/zipball/'.$r->tag.'" target="_blank">'.$r->tag.'</a></div>';
					echo '<div class="core-github tag-message '.$options->class.'">'.$r->message.'</div>';
					echo '</div>';
				}
			}
			break;
			
		case "blob":
			echo '<pre><code>'.htmlentities($r).'</code></pre>';
			break;
	}	
}

//Setup URLs and parse object
function core_github_init($obj, $prefix='api'){
	$options = $obj->options;
	
	if(empty($options->resource)){ echo 'No Resource Set.'; return false;  }
	if(empty($options->user)){ echo 'No User Set.'; return false; }
	if(empty($options->repo)){ echo 'No Repository Set.'; return false; }
	
	switch($options->resource){
		case "tags":
			$url = 'repos/'.$options->user.'/'.$options->repo.'/git/refs/tags';
			break;
		
		case "blob":
			$url = $options->user.'/'.$options->repo.'/'.$options->path; //Path should be a relative path including the branch: master/admin/index.php
			$prefix = 'raw'; //Query the raw.github.com
			break;
	}
	
	$response = core_github_com($url, $prefix);
	if($response === false){ return false; }
	
	echo '<div class="core-github main-wrapper '.$options->class.'">';
	core_github_process($response, $options->resource, $options);
	echo '</div>';
}
?>