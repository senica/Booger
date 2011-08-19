<?php
/*************************************************************
* Change these variables to match your server's configuration
**************************************************************/
$bg_server 	= "";
$bg_user	= "";
$bg_pass	= "";
$bg_db		= "";
define('PREFIX', ''); //Used for database prefix
$bg_key		= '1#43934%^%&'; //Used to store passwords.  Can be any mix of characters except '

/*************************************************************
* Advanced edits
**************************************************************/
$bg_cookie_days = 14; //Number of days to stay logged in for.  You can set this to 0 to force logout when the browser closes even is the user checks "Remember Me"
$bg_cookie_dir = '/'; //Refer to this page on directory strapping: http://php.net/manual/en/function.setcookie.php
$bg_cookie_domain = ''; //Refer to this page on domain strapping: http://php.net/manual/en/function.setcookie.php
$bg_cookie_allow_js = 0; //If set to 1, then this will not allow scritping languages such as javascript to access your cookies....assuming your server supports this.

//Global Definitions
$site_dir = preg_replace('@\\\@', '/', dirname(__FILE__)); //Fix windows backslashes
define('ASSETS', $site_dir);
define('SITE', dirname(ASSETS));
define('ADMIN', SITE."/admin");
define('PLUGINS', SITE."/plugins");
define('THEMES', SITE."/themes");
define('CORE', SITE."/core");
define('THUMBNAIL_WIDTH', 200);

//Database Class
class DB{
	
	//The result is a pointer to be used in things like mysql_num_rows($this->result)
	var $error, $debug, $lnk, $count=0, $result; 
	
	function __construct($server, $user, $pass, $db){
		$this->error = array();
		$link = @mysql_connect($server, $user, $pass);
		if(!$link){
			$this->error("Connection to $server failed");
			//If we cannot connect to the database, and there is an install file, go to that. Otherwise, die.
			if(file_exists("install/index.php")){
				header("Location: /install/index.php");
			}else{
				die("Cannot Connect to Database and no install script exists. Exited.");
			}
		}
		$this->lnk = $link;
		mysql_select_db($db, $this->lnk);
	}
	
	//var_dump($bdb->debug);  This will give you debug information for every query ran on a page request
	function debug($query){
		$this->count++;
		$this->debug[$this->count]['query'] = $query;
		$this->debug[$this->count]['errno'] = mysql_errno($this->lnk);
		$this->debug[$this->count]['errmsg'] = mysql_error($this->lnk);
	}
	
	function count(){
		return mysql_num_rows($this->result);		
	}
	
	function table_exists($name){
		$query = "SHOW TABLES LIKE '".$name."'";
		$this->query($query);
		$this->debug($query);
		return ($this->count() == 0) ? false : true;
	}
	
	function query($query){
		$q = mysql_query($query, $this->lnk);
		$this->debug($query);
		$this->result = $q;
		if(!$q){ return false; }else{ return true; }
	}
	
	//$values is an array where the key is the column name
	function insert($table, $values, $prefix=PREFIX){
		if(empty($values)){ $this->error("insert requires two parameters"); return false; }
		$cols = '(';
		$vals = '(';
		foreach($values as $k=>$v){
			$cols .= $k.",";
			$vals .= "'".mysql_real_escape_string($v)."',";
		}
		$cols = substr($cols, 0, strlen($cols)-1).')';
		$vals = substr($vals, 0, strlen($vals)-1).')';
		$query = "INSERT INTO ".$prefix."_".$table." ".$cols."VALUES".$vals;
		$q = $this->query($query);
		return (!$q) ? false : $this->get_id();
	}
	
	function update($table, $values, $condition=false, $prefix=PREFIX){
		if(empty($values) || empty($condition)){ $this->error("update requires 3 parameters"); return false; }
		$q = '';
		foreach($values as $k=>$v){
			$q .= $k."='".mysql_real_escape_string($v)."',";
		}
		$q = substr($q, 0, strlen($q)-1);
		$query = "UPDATE ".$prefix."_".$table." SET ".$q." WHERE ".$condition;
		$q = $this->query($query);
		return (!$q) ? false : true;
	}
	
	function date(){
		$date = $this->get_result("SELECT NOW() as d");
		return $date->d;
	}
	
	function gmt_date(){
		$date = $this->get_result("SELECT FROM_UNIXTIME(UNIX_TIMESTAMP()) as d");
		return $date->d;
	}
	
	//Returns 1 result
	function get_result($query){
		$q = mysql_query($query, $this->lnk);
		$this->debug($query);
		$this->result = $q;
		return mysql_fetch_object($q);
	}
	
	//Returns all results
	function get_results($query){
		$stack = array();
		$q = mysql_query($query, $this->lnk);
		$this->debug($query);
		$this->result = $q;
		while($row = mysql_fetch_object($q)){
			array_push($stack, $row);		
		}
		return $stack;
	}
	
	//Get id of last inserted row
	function get_id(){
		return mysql_insert_id($this->lnk);	
	}
	
	function error($error){
		array_push($this->error, $error);	
	}
	
}

//Initialize $bdb
$bdb = new DB($bg_server, $bg_user, $bg_pass, $bg_db);


/* Messaging and Errors for System */
class MSG{
	
	public $formatted_errors;
	public $errors;
	
	function error($error){
		$this->errors[] = $error; 
		$this->formatted_errors .= '<div class="bg_error"><img src="admin/images/alert_error.png" />'.$error.'</div>';		
	}
}

$bg_msg = new MSG();

/*
* Get URLs as defined in database and test secure url
* If not able to connect to secure url, set secure url as regular url
* and send message to global message for admin.
*/
$results = $bdb->get_results("SELECT setting_name,setting_value FROM ".PREFIX."_settings WHERE setting_name='site_theme' OR setting_name='site_url' OR setting_name='site_secure_url' ");
foreach($results as $result){
	if($result->setting_name == "site_url"){ $site_url = $result->setting_value; }
	if($result->setting_name == "site_secure_url"){ $site_secure_url = $result->setting_value; }
	if($result->setting_name == "site_theme"){ $site_theme = $result->setting_value; }
}

//verify regular url
$port = '80';
$purl = @parse_url($site_url);
if(!$purl){ $bg_msg->error("Oy Vey! The url you defined is busted. This is what we're dealing with: <b>".$site_url."</b>"); }
if(@strlen($purl['port']) > 0){ $port = $purl['port']; }
$urltest = @fsockopen($purl['host'], $port, $errno, $errstr, 2);
if(!$urltest){
	$url = 'http://'.$_SERVER['HTTP_HOST'];
	$bg_msg->error("Oops! Cannot connect to your site. Please check to make sure <b>".$site_url."</b> is up and running. In the meantime we'll use <i>".$url."</i>." );
}else{
	$url = $site_url;	
}

//verify secure url
$port = '443';
$purl = @parse_url($site_secure_url);
if(!$purl){ $bg_msg->error("Oy Vey! The secure url you defined is busted. This is what we're dealing with: <b>".$site_secure_url."</b>"); }
if(@strlen($purl['port']) > 0){ $port = $purl['port']; }
$ssltest = @fsockopen($purl['host'], $port, $errno, $errstr, 2);
if(!$ssltest){
	$ssl_url = $url;
	$bg_msg->error("Oops! Cannot connect to your secure site. Please check to make sure <b>".$site_secure_url.((@strlen($purl['port']) == 0) ? ":443" : "")."</b> is up and running. In the meantime we'll use <i>".$ssl_url."</i>." );
}else{
	$ssl_url = $site_secure_url;	
}


define('URL', $url);
define('SSL', $ssl_url);
define('THEME', $site_theme);
define('THEME_URL', URL.'/themes/'.THEME);
define('THEME_DIR', THEMES.'/'.THEME);
?>