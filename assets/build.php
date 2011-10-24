<?php require(ASSETS.'/no_direct.php'); ?>
<?php
class Booger{
	
	public $hook,$shortcodes,$url_filter,$user,$server_ip,$remote_ip,$functions_acl,$cache,$content_filter;
	public $guid,$url,$page_id,$called_guid; //Set in assets/url.php
	public $settings; //Pulled from db _settings table
	public $add_js, $add_css; //Added from $this->add_js, $this->add_css respectively
	
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
			$this->user = (object) array("id"=>0, "parent_id"=>0, "alias"=>"guest", "name"=>"Guest", "email"=>"", "website"=>"", "type"=>"user", "group_id"=>0, "group_parent_id"=>0, "group_alias"=>"public", "group_name"=>"Public", "group_email"=>"", "group_website"=>"", "group_type"=>"group", "ip"=>$_SERVER['REMOTE_ADDR'], "http_agent"=>$_SERVER['HTTP_USER_AGENT'] );
			$this->user->permissions = array();
		}else{ //Otherwise, assign DB permissions
			$this->user = $result;
			$this->user->ip = $_SERVER['REMOTE_ADDR']; //Set users IP Address
			$this->user->http_agent = $_SERVER['HTTP_USER_AGENT']; //Set users Web Browser
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
		
		//Preassign variables
		$this->add_css = array();
		$this->add_js = array();
		$this->content_filter = array();
	}
	
	//Check if user is logged in
	//Notice that Cookies are set by domain.  www.boogercms.org is different than just boogercms.org
	function logged_in($min_permissions=0){
		if(!empty($min_permissions) && array_key_exists($min_permissions, $this->user->permissions) ){ //Get specific group permissions
			return true;	
		}else if(empty($min_permissions) && count($this->user->permissions) > 1){ //They have logged in with higher than Guest group permissions
			return true;	
		}else{
			return false;
		}
	}
	
	//Takes the template content and an object (usually from a mysql query) and does a replace on key value pairs
	//$replace_obj->content is assumed to be a serialized object
	function template($template_content, $replace_obj){
		$new = $template_content;
		foreach($replace_obj as $k=>$v){
			if($k == 'content'){
				$content = unserialize($v);
				if($content !== false){
					foreach($content as $l=>$w){
						$new = str_replace('$'.$k.'->'.$l, $w, $new);	
					}
				}
			}else{
				$new = str_replace('$'.$k, $v, $new);
			}
		}
		$new = str_replace('$class', $options->class, $new);
		return $new;
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
		
		$reply_email=	(!empty($reply_email))	? $reply_email	: ((!empty($this->user->email))	? $this->user->email: 'no-reply@'.$this->settings->site_domain); //Keep phpmailer from complainig about invalid address
		$reply_name	=	(!empty($reply_name))	? $reply_name	: ((!empty($this->user->name))	? $this->user->name	: 'Guest');
		$from_email =	(!empty($from_email))	? $from_email	: ((!empty($this->user->email))	? $this->user->email: 'no-reply@'.$this->settings->site_domain);
		$from_name	=	(!empty($from_name))	? $from_name	: ((!empty($this->user->name))	? $this->user->name	: 'Guest');
		
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
	* set_permissions
	* Usually called from firstrun.php from within a plugin directory
	* When a plugin is first dropped into the system, the admin may have not
	* added assigned any permissions to the plugin.  A plugin author, can
	* call this to assign or overwrite permissions that an admin may assign
	* to a plugin.
	* Usage: $bg->plugin_permissions('core_plugins', 'sitehead', 0, 1);
	* $type is what settings to assign permissions to.
	* This can be core_plugins, user_plugins, functions_acl, files_acl
	* $name is the name of the plugin
	* $permissions is the group id
	* $active is 0 for no or 1 for yes
	***********************************************************************/
	function set_permissions($type, $name, $permissions=0, $active=0){
		global $bdb;
		$plugins = unserialize($this->settings->{$type});
		$plugins->{$name} = (object) array();
		$plugins->{$name}->active = trim($active);
		$plugins->{$name}->permissions = trim($permissions);
		$plugins = serialize($plugins);
		$bdb->query("UPDATE ".PREFIX."_settings SET setting_value='".mysql_real_escape_string($plugins)."' WHERE setting_name='".$type."'");
	}
	
	/***********************************************************************
	* url_filter
	* allows you to specify a "nice word" in the url and interpret it as
	* a $_GET variable.
	* $bg->url_filter('save', 2, false);
	* if a url is called like: http://bg/monkey/save/brown/3
	* then the page http://bg/monkey will be served up and the variable
	* $_GET['save'][0] will equal "brown"
	* $_GET['save'][1] will equal "3"
	* parameter 1 is the keyword to look for, parameter 2 the number of
	* slashes to include in the get variable, parameter 3 is a boolean
	* that says whether to leave the filter in tact or not. In our example
	* above if save was actually a page that we wanted to serve up, we
	* could set param 3 to true and $_GET['save'] would still be filled out,
	* but the page http://bg/monkey/save will be served up instead of just
	* http://bg/monkey
	***********************************************************************/
	function url_filter($var, $hold=1, $leave_filter=false){
		if(empty($var)){ $this->error('URL Filters must contain two parameters in the function call: 1)The part of the url you want to pull out. 2)How many variable the GET has'); return false; }		
		$this->url_filter[$var] = (object) array('hold'=>$hold, 'leave_filter'=>$leave_filter);
	}
	
	function run_url_filter($guid){
		$t = explode('/', $guid);
		for($i=0; $i<count($t); $i++){
			$test = $t[$i];
			if(isset($this->url_filter[$test])){
				$_GET[$test] = array();
				$h = $this->url_filter[$test]->hold;
				for($h=1; $h<=$this->url_filter[$test]->hold; $h++){
					array_push($_GET[$test], $t[$i+$h]);
					unset($t[$i+$h]); //Remove the Get variable
				}
				if(!$this->url_filter[$test]->leave_filter){ //If $leave_filter is set to true, then leave the filter in place, otherwise, remove it
					unset($t[$i]); //Remove the filter match
				}
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
			call_user_func($h->func, $h->options);
			//$func = $h->func;
			//$func($h->options);
		}
	}
	
	/***********************************************************************
	* Content Filter provides a way to alter the output content before
	* it reaches the browser.  A good example of this is the reason it was
	* created; that was to provide a way to execute php that has been added
	* to a editable area
	* $bg->content_filter('my_filter_func');
	* function my_filter_func($content){ return $content; }
	***********************************************************************/
	
	function content_filter($func=false){
		if(!$func){ $this->error("Content Filters must contain at least one parameter: 1)What function to call and pass the content into."); return false; }
		//Check Permissions on functions
		//build_plugins.php will check to see if the the entire plugin is active and check for permission to the entire plugins.  Here we are concerned with individual functions.
		//If function does not have explicit permissions, then include as public by default; is function active; do we have permission to access it.
		if($this->user->alias != 'admin' && isset($this->functions_acl->{$func}) && ($this->functions_acl->{$func}->active == 0 || !array_key_exists($this->functions_acl->{$func}->permissions, $this->user->permissions)) ){ return false; }
		array_push($this->content_filter, (object) array('func'=>$func));	
	}
	
	function call_content_filter($content){
		if(sizeof($this->content_filter) <= 0){ return $content; }
		foreach($this->content_filter as $h){
			$content = call_user_func($h->func, $content); //Reassign content from return of function
		}
		return $content;
	}
	
	function add_shortcode($name=false, $func=false, $hooks=false){
		//$hooks is an array of hooks that will be called.  At the writing of this we have 'call', 'add', 'firstcall'
		//$func is not necessary now.  shortcodes with no func are considered template variables
		if(!$name){ $this->error("Shortcodes must contain at least one parameters: 1)The name of the shortcode. 2) Optionally, what function to call"); return false; }
		if(isset($shortcodes[$name])){ $this->error("Duplicate shortcode names detected."); return false; }
		//Check Permissions on functions
		//build_plugins.php will check to see if the the entire plugin is active and check for permission to the entire plugin.  Here we are concerned with individual functions.
		//If function does not have explicit permissions, then include as public by default; is function active; do we have permission to access it.
		if($this->user->alias != 'admin' && isset($this->functions_acl->{$func}) && ($this->functions_acl->{$func}->active == 0 || !array_key_exists($this->functions_acl->{$func}->permissions, $this->user->permissions)) ){ return false; }
		$this->shortcodes[$name] = (object) array('func'=>$func, 'hooks'=>$hooks);
		if(!empty($hooks) && !empty($hooks['add'])){ @call_user_func( $hooks['add'] ); } //Not outputing any error, might need to do this
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
		
		//Handle Hooks added with add_shortcode
		if(!empty($this->shortcodes[$name]->hooks) && !empty($this->shortcodes[$name]->hooks['call'])){ call_user_func( $this->shortcodes[$name]->hooks['call'] ); } //Call hook function everytime we encounter this shortcode
		if(!empty($this->shortcodes[$name]->hooks) && !empty($this->shortcodes[$name]->hooks['firstcall']) && $this->shortcodes[$name]->index == 1){ call_user_func( $this->shortcodes[$name]->hooks['firstcall'] ); } //Call hook on first shortcode call
				
		if($func === false){ return 'tv'; } //If no function is set, shortcode should be a template variable
		$obj = $func($this->shortcodes[$name]);
		return $obj;
		//if(isset($obj["adminnoparse"]) && $obj["adminnoparse"] == "true"){ return array("interpret"=>"adminnoparse"); } //Tell the rendering to NEVER render this shortcode in the admin
		//return true;
	}
	
	function add_css($loc=false, $hook=false){
		if(!$loc){ $this->error("add_css() must have 1 parameter specifing the location of the css file."); return false; }
		if(empty($hook) && !in_array($loc, $this->add_css)){ //Only allow one css per page
			array_push($this->add_css, $loc);
			echo '<link type="text/css" href="'.$loc.'" rel="stylesheet" />'; //If no hook is specified, then just output the tag
		}else{
			$this->add_hook($hook, array($this, 'add_css'), $loc); //Add hook to hook location to be called later and then the css will be added 		
		}
	}
	
	function add_js($loc=false, $hook=false){
		if(!$loc){ $this->error("add_js() must have 1 parameter specifing the location of the javascript file."); return false; }
		if(empty($hook) && !in_array($loc, $this->add_js)){ //Only allow one js per page
			array_push($this->add_js, $loc);
			echo '<script type="text/javascript" src="'.$loc.'" ></script>';
		}else{
			$this->add_hook($hook, array($this, 'add_js'), $loc);	
		}
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
	
	//Get plugin url...only to be called from within a plugins main file
	function plugin_url($display=true, $showfile=false, $dir=true){
		$call = debug_backtrace(false, 1);
		$file = $call[0]['file'];
		if($dir === true){ $file = preg_replace('@\\\@', '/', dirname($file)); }
		$url = str_replace(SITE, URL, $file);
		if(!$display)
			return (!$showfile) ? $url : $file;
		else
			echo (!$showfile) ? $url : $file;
	}
	
	/********************************************************************
	* $pages = $bg->paginate(
	* 				total number of results,
	* 				current page,
	* 				number of results per page,
	* 				how many page numbers to show at a time,
	* 				hidden pages substitute,
	* 			);
	* foreach($pages as $p){
	*	if(is_numeric($p)){
	*		echo '<a href="?pg='.$p.'">'.$p.'</a>';	
	*	}else{
	*		echo '<span>'.$p.'</span>';	
	*	}
	* }
	* retuns an array of page numbers
	********************************************************************/
	function paginate($total=64, $page=1, $pagesize, $show=7, $dots='...'){
		if($total == 0.0 || $pagesize == 0.0){ return false; } //Catch division by zero
		$display = array();
		$pages = ceil($total / $pagesize); //Total number of pages
		
		$start = $page - floor($show / 2); //What page to start showing with
		if($start + $show > $pages){ $start = $pages - $show + 1; } //If we would runover, then backup the start
		if($start <= 1){ $start = 1; } //If we would be below 1, then start at 1
		
		$end = $start + $show - 1; //What page to end showing on
		if($end > $pages){ $end = $pages; } //If we would runover, stop at the total pages
		
		if($start > 2){ array_push($display, 1); array_push($display, $dots); }
		
		for($i=$start; $i<$end+1; $i++){
			array_push($display, $i);;	
		}
		
		if($end < $pages - 2){ array_push($display, $dots); array_push($display, $pages); }
		
		return $display;
	}
	
	/********************************************************************
	* $bg->friendly($text, $max_words)
	* Stips out all non digit and characters and replaces with a + or blank
	********************************************************************/
	function friendly($text, $max_words=10){
		$url = $this->word_limit($text, $max_words);
		$url = preg_replace('/[^a-z0-9\s]/i', '', $url);
		$url = preg_replace('/\s+/i', '-', $url); //Can also use a plus here
		return $url;
	}
	
	/********************************************************************
	* $bg->word_limit($text, $limit)
	* Limites the number or words in a string
	********************************************************************/
	function word_limit($text, $limit){
		$words = explode(' ', $text);
		return implode(' ', array_slice($words, 0, $limit));	
	}
	
	/********************************************************************
	* $bg->is_child_of(parent)
	* is the current page a child of parent 
	********************************************************************/
	function is_child_of($parent=0){
		global $bdb;
		$id = $this->page_id;
		while(($result = $bdb->get_result("SELECT parent_id FROM ".PREFIX."_content WHERE id='".$id."'")) !== false){
			$id = $result->parent_id;
			if($id == $parent){ return true; }
		};
		return false;
	}
	
	/********************************************************************
	* $bg->comments_allowed()?
	* check to see if comments are allowed on a page
	********************************************************************/
	function comments_allowed(){
		global $bdb;
		$result = $bdb->get_result("SELECT allow_comments FROM ".PREFIX."_content WHERE id = '".$this->page_id."'");
		return ($result->allow_comments == 1) ? true : false; 
	}
	
	/********************************************************************
	* $bg->get_children_of(int parent, string columns, string type)
	* Gets an array of children from a specified parent id
	* By default, the array only contains the if of the children, you
	* can modify this by including a comma separated list of columns to
	* return.
	* type is set for you to the same content type as the parent.  If
	* you need only a specific type, you can specify that.
	* Return is an array of objects such that:
	* foreach($result as $r){ echo $r->id; }
	********************************************************************/
	function get_children_of($parent, $cols='id', $type=false){
		global $bdb;
		$list = array();
		if(!$type){
			$result = $bdb->get_result("SELECT type FROM ".PREFIX."_content WHERE id = '".$parent."'");
			$type = $result->type;
		}
		$results = $bdb->get_results("SELECT ".$cols." FROM ".PREFIX."_content WHERE parent_id = '".$parent."' AND status='published' AND type='".$type."' AND NOW() > publish_on");
		if(empty($results)){ return $list; }
		else{
			foreach($results as $r){
				array_push($list, $r);
				$list = array_merge($list, $this->get_children_of($r->id, $cols, $type));
			}
			return $list;
		}
		return false; //We shouldn't get here	
	}
	
	/********************************************************************
	* $bg->setting("setting_name", "optional value");
	* if a value is passed in, then the setting_name will be added or
	* updated in the database.
	* if not value is passed in, then the db value will be passed back.
	********************************************************************/
	function setting($setting_name, $val=false){
		global $bdb;
		$result = $bdb->get_result("SELECT setting_name, setting_value, setting_id FROM ".PREFIX."_settings WHERE setting_name='".mysql_real_escape_string($setting_name)."'");
		if($val === false){ return $result->setting_value; } //If no value, then return db value
		else if(empty($result)){ //If not results, then insert
			return $bdb->insert("settings", array("setting_name"=>$setting_name, "setting_value"=>$val));	
		}else{ //otherwise update
			return $bdb->update("settings", array("setting_value"=>$val), "setting_id='".$result->setting_id."'");
		}
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
	global $bg;
	?>
	<script type="text/javascript">
		bg = {};
		bg.url = '<?php echo URL; ?>';
		bg.theme_url= '<?php echo URL.'/themes/'.THEME; ?>';
		bg.guid = '<?php echo $bg->guid; ?>';
	</script>
	<?php			
}//End bg_build_header()
?>