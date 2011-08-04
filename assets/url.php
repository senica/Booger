<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	$guid = preg_replace('{/$}', '', $_GET['guid']); //Remove trailing slash so we don't get 404 errors 
	if(is_file($guid)){ require_once($guid); return true; }
	
	//Set the actual called guid global variable for a page
	$bg->called_guid = $guid;
	
	//Run url filter
	$hold_guid = $bg->run_url_filter($guid); //Parses any registered url_filters and turns them into GET variables
	
	//Check for a page that has the original guid first
	$result = $bdb->get_result("SELECT id,template,content,viewable_by,UNIX_TIMESTAMP(publish_on) as publish_time FROM ".PREFIX."_content WHERE guid='".mysql_real_escape_string($guid)."' AND status='published'");
	if(!$result){
		//If original guid does not work, let's try the url_filter guid
		$guid = $hold_guid;
		$result = $bdb->get_result("SELECT id,template,content,viewable_by,UNIX_TIMESTAMP(publish_on) as publish_time FROM ".PREFIX."_content WHERE guid='".mysql_real_escape_string($guid)."' AND status='published'");
		if(!$result){
			//If url_filter does not give us a good page, then let's see if there are any url_redirects
			if($result = $bg->run_url_redirect($guid)){
				//The url_redirect is responsible for returning a result object that has among other things a template 
				//If you do not want the rest of the page to process, return a die in your object 
				if(isset($result->die)){
					echo $result->die;
					return false; die(); 
				}
			}else{
				//handle 404 errors.  You can specify a 404.php page in your template folder.
				$url = $guid;
				if(is_file(THEMES.'/'.THEME.'/404.php')){ require_once(THEMES.'/'.THEME.'/404.php'); }	
				else{ require_once(ASSETS.'/404.php'); }
				return false; die();	
			}
		}
	}
	
	//Check for page permissions
	//Allow the admin alias to bypass security check
	if($bg->user->alias != 'admin' && isset($result->viewable_by) && !array_key_exists($result->viewable_by, $bg->user->permissions)){
		$force_login = true;
		$result = $bdb->get_result("SELECT name FROM ".PREFIX."_acl WHERE id='".$result->viewable_by."'");
		$force_message = 'This page requires you to be a member of the '.$result->name.' group.';
		$force_return = URL.'/'.$guid;
		require('admin/auth.php'); die();	
	}
	
	//Check publish times
	if($result->publish_time > time()){
		$time = date('F jS, Y H:i:s T', $result->publish_time);
		require_once(ASSETS.'/204.php'); return false; die();
	}
	
	//Set the actual used guid for global use.
	$bg->guid = $guid;	
	
	//Set global variables for a page
	$bg->url = URL.'/'.$guid;
	$bg->called_url = URL.'/'.$bg->called_guid;
	$bg->page_id = $result->id;
	
	$content = unserialize($result->content);
	
	//Get Page Template
	$holder = '';
	if(file_exists($bg->templates[$result->template])){									//Use template defined for page if it exists
		ob_start();
		require_once($bg->templates[$result->template]);
		$holder = ob_get_contents();
		ob_end_clean();
	}else if(file_exists($bg->templates[$bg->settings->site_default_template])){		//Use site's default template if it exists and it's defined
		ob_start();
		require_once($bg->templates[$bg->settings->site_default_template]);
		$holder = ob_get_contents();
		ob_end_clean();	
	}else{																				//No template defined for page and no default template defined
		echo '<html><head><style>#no-template-message{position:fixed; top:0px; left:0px; width:100%; background:#ccc; padding:10px;}</style></head><body><div id="no-template-message">Booger Message: The template for this page is blank or cannot be found.  Did you rename the template file?</div>';
		foreach($content as $v){
			echo parseShortCodes($v);	
		}
		echo '</body></html>';
		return false;
	}
	
	//Parse content shortcodes
	echo parseShortCodes($holder);

	
	function getShortCode($shortcode, $options=false, $stack){
		global $content,$bg,$bdb;
		
		//$shortcode = trim($shortcode); //Should already be trimed and formatted before being passed in
		
		if($options !== false){
			//Fix malformed json
			$options = preg_replace("/\r\n|\n|\t/ims", '', $options);	//Remove new lines and tabs from multi-line shortcodes
			$options = preg_replace('/{[\s|&nbsp;]+/', '{', $options);	//Remove spaces after the opening curly
			$options = preg_replace('/[\s|&nbsp;]+}/', '}', $options);	//Remove spaces before the closing curly
			$options = preg_replace("/'/", '"', $options);				//Use single quotes to avoid html rewrites with &quot;
			
			$options = json_decode($options);
			if($options === NULL){ return false; }						//Return false and display shortcode as text since it has malformed json
		}
		ob_start();
		$return = $bg->call_shortcode($shortcode,$options,$stack);
		$display = ob_get_contents();
		ob_end_clean();
		
		if($return == 'tv'){											//This should be a template variable, show db info
			$display = (isset($_GET['noparse'])) ? parseShortCodes($content[$shortcode], $stack, true) : parseShortCodes($content[$shortcode], $stack, false);			//Parse any shortcodes in db info
			$display = preg_replace("/&amp;lsbkt;|&lsbkt;/", '[', $display); //Replace &lsbkt; with [
			if($display == "" && isset($options->default)){
				$display = parseShortCodes($options->default, $stack, false);			//If nohting is saved in db and shortcode has a default, show that
			}
			return '<div class="template-variable editable '.$shortcode.' '.$options->class.'" rel="'.$shortcode.'">'.$display.'</div>';											//Return the db info.  No further processing required
		}
		if(isset($_GET['noparse']) && $return == 'noparse'){			//If shortcode returns 'noparse' don't parse when $_GET['noparse'] is set
			return false;	
		}
		$display = (isset($_GET['noparse'])) ? parseShortCodes($display, $stack, true) : parseShortCodes($display, $stack, false);
		$display = (isset($_GET['noparse'])) ? $display : preg_replace("/&amp;lsbkt;|&lsbkt;/", '[', $display);
		return $display;
	}
	
	//Parse Shortcodes.  We can't use regex because we get into situations with inner shortcodes and we can't match against open and end tags within each other	
	function parseShortCodes($content, $stack=array(), $noparse=false){
		global $bg;
		
		//if parseShortCode was called from a shortcode function, then do the heavy lifing for it.
		//Stop run-away parsing of shortcodes!
		$caller = debug_backtrace(); //When 5.4 is available on most hosting providers, change this to limit only two level backtrace
		if(isset($caller[1]["args"][0]->name)){
			$obj = $caller[1]["args"][0];	
			
			$stack = $obj->stack; //Reassign stack to continue level count from $bg->call_shortcode
			
			if(!isset($stack[$obj->name])){
				$stack[$obj->name]['level'] = 0; //Keep track of the individual stack level	
			}
			
			if( isset($obj->options->loop) && $obj->options->loop != 0 && !isset($stack[$obj->name]['loop']) ){
				$stack[$obj->name]['level'] = 0; //reassign the beginning of a stack loop
				$stack[$obj->name]['loop']	= $obj->options->loop; //Assign the maximum iterations of that particular shortcode.
			}
			
			$stack[$obj->name]['level']++; //Increase the individual stack level
		}		
		$stack['level']++; //Keep track of how many levels we have looped down overall
			
		$c = '';
		
		$sc = explode('[', $content);
		$c .= array_shift($sc);
		$hold = '';
		while(count($sc) > 0){
			$cur = array_shift($sc);
			if(preg_match('/(.*?)([{\]])(.*)/ism', $cur, $match) > 0){						//Need to test if this is an array, a shortcode, or text.		
				$match[1] = trim(str_replace('&nbsp;', '', $match[1]));						//Format shortcode - remove any html spaces or any whitespace
				if( !isset($bg->shortcodes[$match[1]]) ){									//We are parsing either text or an array. Either way, just append it.
					$c .= '['.$cur;											   
				}else if( (isset($stack[$match[1]]['loop']) && $stack[$match[1]]['loop'] == $stack[$match[1]]['level']) || (!isset($stack[$match[1]]['loop']) && $stack[$match[1]]['level'] >= 1) ){
					//If shortcode had a 'loop' option assigned and it wasn't 0 and we have reached the end of that loop
					//Or if no 'loop' option was assigned when the shortcode was called or 'loop' was '0' and we have reached 1 level down
					//Allow function to return nothing and stop parsing so it can continue to the next shortcode.
					//We allow 1 level by default which will not parse the same shortcode twice.
				}else{																		//We are parsing a shortcode, does it have options?
					if($match[2] == ']'){													//We have the entire shortcode, no options.
						$test = getShortCode($match[1], false, $stack);								//Render shortcode
						if($test === false){												//If the rendering returns false, just display the shortcode
							$c .= '['.$match[1].']';	
						}else{
							if($noparse === true){ $c .= '<span class="template-variable-noparse" contenteditable="false"><span class="template-variable-noparse-code" style="display:none;">['.$match[1].']</span><span class="template-variable-noparse-content">'; } //If noparse is true, then render the shortcode, but also allow for seeing the original text 
							$c .= $test;
							if($noparse === true){ $c .= '</span></span>'; }
						}
						$c .= $match[3];											   
					}else if($match[2] == '{'){												//We have options that we need to extract
						while(preg_match('/(.*}).*\](.*)/ism', $match[3], $code) == 0 && count($sc) > 0){	//Gather until the end of the options and shortcode or the end of the content array
							$match[3] .= '['.array_shift($sc);		
						}
						if($code[0] == ''){													//If we ran to the end of the array, we had a false positive match on a shortcode.
							$c .= '['.$match[1].' {';										//Back up and parse from the false positive match.
							$c .= parseShortCodes($match[3], $stack, $noparse);
						}else{
							$test = getShortCode($match[1], '{'.parseShortCodes($code[1], $stack, $noparse), $stack );	//Render Short Codes inside options
							if($test === false){											//If there is a parse error on the json or the shortcode requests to not be parsed, just display shortcode as text								
								$c .= '['.$match[1].' {'.$match[3];	
							}else{
								if($noparse === true){ $c .= '<span class="template-variable-noparse" contenteditable="false"><span class="template-variable-noparse-code" style="display:none;">['.$match[1].' {'.$code[1].']</span><span class="template-variable-noparse-content">'; } //If noparse is true, then render the shortcode, but also allow for seeing the original text 
								$c .= $test;
								if($noparse === true){ $c .= '</span></span>'; }
								$c .= $code[2];												//Otherwise, display the rendered shortcode
							}
						}
					}
				}	
			}else{																			//Not sure what we are parsing, so let's just append it.
				$c .= '['.$cur;	
			}	
		}
		return $c;
		
		//Wishful thinking - This is what I was using up until right before the first packaging and realized I couldn't have complex shortcodes within complex shortcodes :(
		//return preg_replace_callback('/\[(.*?)({.*?})*?\]/ims', "getShortCode", $content);
	}		
?>

