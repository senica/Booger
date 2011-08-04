<?php require(ASSETS.'/no_direct.php'); ?>
<?php
class Booger{
	
	public $hook,$shortcodes,$url_filter,$user,$server_ip,$remote_ip,$functions_acl,$cache;
	public $guid,$url,$page_id,$called_guid; //Set in assets/url.php
	public $settings; //Pulled from db _settings table
	
	function __construct(){
		global $bdb,$bg_key;
		
		//Assign Database Settings
		$results = $bdb->get_results("SELECT setting_name, setting_value FROM ".PREFIX."_settings WHERE onload='1' "); 
		foreach($results as $result){
			$this->settings[$result->setting_name] = $result->setting_value;
		}
		$this->settings = (object) $this->settings;
		
		//Assign User and User Permissions
		$result = $bdb->get_result("SELECT u.id as id, u.parent_id as parent_id, u.alias as alias, u.name as name, u.email as email, u.website as website, u.type as type, g.id as group_id, g.parent_id as group_parent_id, g.alias as group_alias, g.name as group_name, g.email as group_email, g.website as group_website, g.type as group_type FROM ".PREFIX."_session s LEFT JOIN ".PREFIX."_acl u ON s.session_user_id = u.id LEFT JOIN ".PREFIX."_acl g ON u.parent_id = g.id AND g.type='group' WHERE session_data_check = '".mysql_real_escape_string($_COOKIE['bg_authenticated_user'])."' AND u.type='user' LIMIT 1");
		if(!$result){ //If not in DB, assign Guest Permissions
			$this->user = (object) array("id"=>0, "parent_id"=>0, "alias"=>"guest", "name"=>"Guest", "email"=>"", "website"=>"", "type"=>"user", "group_id"=>0, "group_parent_id"=>0, "group_alias"=>"public", "group_name"=>"Public", "group_email"=>"", "group_website"=>"", "group_type"=>"group" );
			$this->user->permissions = array();
		}else{ //Otherwise, assign DB permissions
			$this->user = $result;
			//Assign User Group Permissions - Cascades Up, so if a user is a under admin, and admin is under authors, the user is inheritly given permissions as authors and admin
			$this->user->permissions = array();
			$this->user->permissions[$result->group_id] = (object) array("id"=>$result->group_id, "alias"=>$result->group_alias, "name"=>$result->group_name, "email"=>$result->group_email, "website"=>$result->group_website, "type"=>$result->group_type, "parent"=>$result->group_parent_id);
			while($result = $bdb->get_result("SELECT id, parent_id as group_parent_id, alias, name, email, website, type FROM ".PREFIX."_acl WHERE id='".$result->group_parent_id."' AND type='group' ")){
				$this->user->permissions[$result->id] = (object) array("id"=>$result->id, "alias"=>$result->alias, "name"=>$result->name, "email"=>$result->email, "website"=>$result->website, "type"=>$result->type, "parent"=>$result->group_parent_id);		
			}
		}
		//Assign Public Group permissions
		$this->user->permissions[0] = (object) array("id"=>0, "alias"=>"public", "name"=>"Public", "email"=>"", "website"=>"", "type"=>"group", "parent"=>0);
	
		//Assign Local and Remote IP Addresses
		$this->server_ip = $_SERVER['SERVER_ADDR'];
		$this->remote_ip = $_SERVER['REMOTE_ADDR'];
		
		//Assign Functions Access Control List
		$this->functions_acl = unserialize($this->settings->functions_acl);
		
		//Assign Files Access Control List
		$this->files_acl = unserialize($this->settings->files_acl);
		
		//Convert Mail Password to plain text
		$result = $bdb->get_result("SELECT AES_DECRYPT(BINARY(UNHEX('".$this->settings->mail_password."')), '".$bg_key."') as setting_value");
		$this->settings->mail_password = $result->setting_value;
	
		//Build List of Templates - May need to find a quicker way to do this.  I started doing it this way so users were free to put templates wherever they want in the theme directory
		$this->templateTree(THEMES.'/'.THEME);
	}
	
	//Used to extract templates out of theme directory - called from constructor
	function templateTree($dir){
		$files = scandir($dir);
		foreach($files as $file){
			if(is_dir($dir.'/'.$file) && $file != '.' && $file != '..'){
				$this->templateTree($dir.'/'.$file);	
			}else if(substr($file, -7) == 'tpl.php'){
				$this->templates[$file] = $dir.'/'.$file;	
			}
		}
	}
	
	//Used for plugins that need to email stuff
	function email($to_email, $to_name='', $subject='', $message='', $reply_email='', $reply_name='', $from_email='', $from_name=''){
		if(!isset($this->settings->mail_type)){ $this->error("Check your mail settings under Site Settings"); return false; }
		require_once(CORE."/phpmailer/class.phpmailer.php");
		$mail = new PHPMailer();															// defaults to using php "mail()"
		if($this->settings->mail_type == 'smtp'){
			$mail->IsSMTP();
			$mail->SMTPDebug  = 0;															// enables SMTP debug information (for testing)
			$mail->SMTPAuth   = (trim($this->settings->mail_user) != "") ? true : false;	// enable SMTP authentication
			$mail->SMTPSecure = $this->settings->mail_security;								// sets the prefix to the servier
			$mail->Host       = $this->settings->mail_server;								// SMTP server
			$mail->Port       = $this->settings->mail_port;									// SMTP port
			$mail->Username   = $this->settings->mail_user;									// username
			$mail->Password   = $this->settings->mail_password;								// password
		}
		
		$reply_email = ($reply_email != '') ? $reply_email : $this->user->email;
		$reply_name = ($reply_name != '') ? $reply_name : $this->user->name;
		$from_email = ($from_email != '') ? $from_email : $this->user->email;
		$from_name = ($from_name != '') ? $from_name : $this->user->name;
		
		$mail->AddReplyTo($reply_email, $reply_name);			
		$mail->SetFrom($from_email, $from_name);						
		$mail->AddAddress($to_email, $to_name);			
		$mail->Subject	= $subject;			
		$mail->AltBody	= strip_tags( preg_replace('#<br\s*/>|<br>#', "\r\n", $message) );
		$mail->MsgHTML($message);			
		if(!$mail->Send()) {
		  return false;
		} else {
		  return true;
		}	
	}
	
	/***********************************************************************
	* url_filter
	* allows you to specify a "nice word" in the url and interpret it as
	* a $_GET variable.
	* $bg->url_filter('save', 'save-var');
	* if a url is called like: http://bg/monkey/save/brown
	* then the page http://bg/monkey will be served up and the variable
	* $_GET['save-var'] will equal "brown"
	* parameter 1 is the keyword to look for, parameter 2 is the name you
	* want the $_GET variable to have.
	***********************************************************************/
	function url_filter($var, $get){
		if(!$var || !$get){ $this->error('URL Filters must contain two parameters in the function call: 1)The part of the url you want to pull out. 2)Where to assign the $_GET variable'); return false; }		
		$this->url_filter[$var] = (object) array('get'=>$get);
	}
	
	function run_url_filter($guid){
		$t = explode('/', $guid);
		$count = count($t);
		for($i=0; $i<$count; $i++){
			$test = $t[$i];
			if(isset($this->url_filter[$test])){
				$get = $this->url_filter[$test]->get;
				$_GET[$get] = $t[$i+1];
				unset($t[$i]); unset($t[$i+1]);
			}
		}
		$new_guid = implode('/', $t);
		return $new_guid;
	}
	
	/***********************************************************************
	* url_redirect
	* allows you to interrupt what would be a 404 error and process your
	* own informatin. It is important to note that by design, pages in the
	* database and url_filters take precedence over a url_redirect
	* Let's say I have no page named "search" and no url_filters named
	* "search", I can create a url_redirect named "search" and specify
	* a function to be called and anytime a url is called with the keyword
	* "search" in it, my function will be called.
	* This function is responsible for returning an object that contains
	* necessary information to process the rest of the page.
	***********************************************************************/
	function url_redirect($var, $func){
		if(!$var || !$func){ $this->error('URL Redirects must contain two parameters in the function call: 1)The part of the url you want to redirect on. 2)A function to call for the redirect'); return false; }		
		$this->url_redirect[$var] = (object) array('func'=>$func);	
	}
	
	function run_url_redirect($guid){
		foreach($this->url_redirect as $k=>$v){
			if(preg_match("#(/|\A)".$k."(/|\Z)#", $guid)){ //Do we just need to trim this?
				$func = $this->url_redirect[$k]->func;
				return $func($guid);
			}
		}
		return false;
	}
	
	/***********************************************************************
	* Processors allow a way for you to specify how to process a page of
	* a particular type.
	* The default types that are handled by the system at the writing of
	* this are page, post, category, tag
	* For example, let's say you want to create a content entry in the
	* database with a type of archives.  And let's say your content entry
	* has a guid of my-processor-example.  When you browse to
	* http://yoursite/my-processor-example, the system will see that the
	* page has a type of "archives".  By default it will try to process
	* the page as a "page" type.  This won't work since you probably want
	* to display a list of archives.  So you can add a processor to tell
	* the system what to do.
	* $bg->add_processor('type', 'function_to_call');
	* In this case: $bg->add_processor('archive', 'my-archive-function');
	* Now the system will interrupt and go to the my-archive-function for
	* instructions on how to process the page.
	*
	* THIS IS NOT IMPLEMENTED YET.  HERE TO REMIND ME TO DO SO.  SHOULD DO
	* IN URL.PHP
	***********************************************************************/
	
	function add_processor($type=false, $func=false){
		if(!$type || !$func){ $this->error("Processors must contain at least two parameters: 1)What 'type' to call the processor on. 2)What function to call"); return false; }
		$this->processor[$type] = (object) array('func'=>$func);	
	}
	
	function call_processor($type=false){
		if(!$type){ $this->error("To call processors, you must specify what processor you want to call"); return false; }
		$func = $this->processor[$type]->func;
		$func();
	}
	
	/***********************************************************************
	* Hooks allow a way for external code to be brought into the main
	* system.
	* Example: $bg->add_hook('admin-head', 'init', $options);
	* Takes two or three parameters, where you want to perform the action, and the
	* function you want called. Optionally any options you want to pass to
	* the called function. You should never need to use call_hook
	* unless you are creating your own defined hook.
	* Current called hooks:
	* admin-head
	* admin_body
	* admin_foot
	* admin_tabs
	* admin_bottom_bar
	* admin_toolbox
	* admin_sidebar
	* admin_storage
	* see documentation for updated list of hooks
	***********************************************************************/
	
	function add_hook($where=false, $func=false, $options=false){
		if(!$where || !$func){ $this->error("Hooks must contain at least two parameters: 1)Where to add the hook. 2)What function to call"); return false; }
		//Check Permissions on functions
		//build_plugins.php will check to see if the the entire plugin is active and check for permission to the entire plugins.  Here we are concerned with individual functions.
		//If function does not have explicit permissions, then include as public by default; is function active; do we have permission to access it.
		if($this->user->alias != 'admin' && isset($this->functions_acl->{$func}) && ($this->functions_acl->{$func}->active == 0 || !array_key_exists($this->functions_acl->{$func}->permissions, $this->user->permissions)) ){ return false; }
		$this->hook[$where][] = (object) array('func'=>$func, 'options'=>$options);	
	}
	
	function call_hook($where=false){
		if(!$where){ $this->error("To call hooks, you must specify what hooks you want to load"); return false; }
		if(sizeof($this->hook[$where]) <= 0){ return false; }
		foreach($this->hook[$where] as $h){
			$func = $h->func;
			$func($h->options);
		}
	}
	
	function add_shortcode($name=false, $func=false){
		//$func is not necessary now.  shortcodes with no func are considered template variables
		if(!$name){ $this->error("Shortcodes must contain at least one parameters: 1)The name of the shortcode. 2) Optionally, what function to call"); return false; }
		if(isset($shortcodes[$name])){ $this->error("Duplicate shortcode names detected."); return false; }
		//Check Permissions on functions
		//build_plugins.php will check to see if the the entire plugin is active and check for permission to the entire plugin.  Here we are concerned with individual functions.
		//If function does not have explicit permissions, then include as public by default; is function active; do we have permission to access it.
		if($this->user->alias != 'admin' && isset($this->functions_acl->{$func}) && ($this->functions_acl->{$func}->active == 0 || !array_key_exists($this->functions_acl->{$func}->permissions, $this->user->permissions)) ){ return false; }
		$this->shortcodes[$name] = (object) array('func'=>$func);
	}
	
	function call_shortcode($name=false, $options=false, $stack=array()){
		if(!$name){ $this->error("To call shortcodes, you must specify what shortcode name you want to load"); return false; }
		if(!isset($this->shortcodes[$name])){ return false; }
		$this->shortcodes[$name]->index = $this->shortcodes[$name]->index + 1; //This should not be manually set
		$this->shortcodes[$name]->offset = $this->shortcodes[$name]->offset + 1; //This can be reset from the code
		$this->shortcodes[$name]->options = $options;
		$this->shortcodes[$name]->name = $name;
		$this->shortcodes[$name]->stack = $stack;
		$func = $this->shortcodes[$name]->func;
		if($func === false){ return 'tv'; } //If no function is set, shortcode should be a template variable
		$obj = $func($this->shortcodes[$name]);
		return $obj;
		//if(isset($obj["adminnoparse"]) && $obj["adminnoparse"] == "true"){ return array("interpret"=>"adminnoparse"); } //Tell the rendering to NEVER render this shortcode in the admin
		//return true;
	}
	
	function add_css($loc=false){
		if(!$loc){ $this->error("add_css() must have 1 parameter specifing the location of the css file."); return false; }
		echo '<link type="text/css" href="'.$loc.'" rel="stylesheet" />';
	}
	
	function add_js($loc=false){
		if(!$loc){ $this->error("add_js() must have 1 parameter specifing the location of the javascript file."); return false; }
		echo '<script type="text/javascript" src="'.$loc.'" ></script>';
	}
	
	function error($msg){
		global $bg_msg;
		$bg_msg->error($msg);
	}
	
	function get_header(){
		$file = THEMES.'/'.THEME.'/header.php';
		if(file_exists($file)){
			$bg = $this;
			require($file);		
		}
	}
	
	function get_footer(){
		$file = THEMES.'/'.THEME.'/footer.php';
		if(file_exists($file)){
			$bg = $this;
			require($file);		
		}
	}
	
	function get_page($page){
		$file = THEMES.'/'.THEME.'/'.$page;
		if(file_exists($file)){
			$bg = $this;
			require($file);		
		}
	}
	
	function get_user(){
		global $bdb;
		$result = $bdb->get_result("SELECT a.id,a.name,a.parent_id,a.email,a.website,a.created_on FROM ".PREFIX."_session as s LEFT JOIN ".PREFIX."_acl as a ON s.session_user_id=a.id WHERE session_data_check = '".mysql_real_escape_string($_COOKIE['bg_authenticated_user'])."' AND a.type='user'");
		return ($result !== false) ? $result : false;
	}
}//end class

/* Make instance of class */
$bg = new Booger;

/*Define More Javascript Globals. Must be called in page header to use. $bg->call_hook('header'); */
$bg->add_hook('header', 'bg_build_header');
function bg_build_header(){
	?>
	<script type="text/javascript">
		var bg	= {};
		bg.site = '<?php echo SITE; ?>';
		bg.url = '<?php echo URL; ?>';
		bg.themes	= '<?php echo URL; ?>/themes';
		bg.theme	= '<?php echo THEME; ?>';
		bg.theme_dir= '<?php echo THEMES.'/'.THEME; ?>';
		bg.theme_url= '<?php echo URL.'/themes/'.THEME; ?>';
	</script>
	<?php			
}//End bg_build_header()

/* Define Javascript variables for Site Plugins to use */
$bg->add_hook('site-head', 'bg_build_site_header');
function bg_build_site_header(){
	?>
	<script type="text/javascript">
		bg = {};
		bg.url = '<?php echo URL; ?>';
		bg.theme_url= '<?php echo URL.'/themes/'.THEME; ?>';
	</script>
	<?php			
}//End bg_build_header()
?>