<?php require(ASSETS.'/no_direct.php'); ?>
<?php
class Github{
	var $prefix = 'api';
	var $errors = array();
	var $url, $repo, $user, $branch;
	
	function __construct($user='', $repo='', $branch=''){
		$this->user = $user;
		$this->repo = $repo;
		$this->branch = $branch;
	}
	
	//http://developer.github.com/v3/git/refs/
	public function refs($ref=false){
		$ref = (!empty($ref)) ? '/'.$ref : $ref;
		$this->url = 'repos/'.$this->user.'/'.$this->repo.'/git/refs'.$ref;
		return $this->com();
	}
	
	//http://developer.github.com/v3/git/refs/
	public function tags($tag=false){
		$tag = (!empty($tag)) ? '/'.$tag : $tag;
		return $this->refs('tags'.$tag);	
	}
	
	//http://developer.github.com/v3/git/tags/
	public function tag($sha=''){
		$this->url = 'repos/'.$this->user.'/'.$this->repo.'/git/tags/'.$sha;
		return $this->com();
	}
	
	//http://developer.github.com/v3/git/trees/
	public function trees($sha='', $recursive=false){
		$recursive = (!$recursive) ? false : '?recursive=1';
		$this->url = 'repos/'.$this->user.'/'.$this->repo.'/git/trees/'.$sha.$recursive;
		return $this->com();
	}
	
	//http://developer.github.com/v3/git/commits/
	public function commit($sha=''){
		$this->url = 'repos/'.$this->user.'/'.$this->repo.'/git/commits/'.$sha;
		return $this->com();
	}
	
	//http://developer.github.com/v3/git/blobs/
	public function blob($sha=''){
		$this->url = 'repos/'.$this->user.'/'.$this->repo.'/git/blobs/'.$sha;
		return $this->com();
	}
	
	private function error($err){
		array_push($this->errors, $err);
	}
	
	//Get response from Github
	private function com(){
		$fp = fsockopen("ssl://".$this->prefix.".github.com", 443, $errno, $errstr, 30);
		if (!$fp){ $this->error('Could not connect to Github'); return false; }
		$out = "GET /".$this->url." HTTP/1.1\r\n";
		$out .= "Host: ".$this->prefix.".github.com\r\n";
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
		switch($this->prefix){
			case "api":
				return json_decode($content);
				break;
			case "raw":
				return $content;
				break;
		}		
	}
}
?>