<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_debug_admin_head');
$bg->add_hook('admin-foot', 'core_debug_admin_js');
$bg->add_hook('admin-sidebar-top', 'core_debug_sidebar');
$bg->add_hook('admin-storage', 'core_debug_dialog');
$bg->add_hook('site-settings', 'core_debug_site_settings');
$bg->add_shortcode('bug-list', 'core_debug_bug_list');
$bg->url_filter('bug', 2); //Allows urls to be formed with http://www.boogercms.org/page/bug/id/title

function core_debug_bug_list($obj){
	global $bdb, $bg;
	
	$bg->add_css(URL.'/core/debug/style.css', 'site-foot');
	$bg->add_js(URL.'/core/debug/reply.js', 'site-foot');
	
	$options = $obj->options;
	$results = $bdb->get_results("SELECT id, title, status, content FROM ".PREFIX."_content WHERE type='bug' ".((!empty($_GET['bug'][0])) ? "AND id='".$_GET['bug'][0]."'" : '')." ORDER BY id DESC");
	foreach($results as $r){
		$content = unserialize($r->content);
		echo '<div class="'.$options->class.' core-bug-wrapper">';
			echo '<div class="'.$options->class.' core-bug-content"><a href="'.$bg->url.'/bug/'.$r->id.'/'.$bg->friendly($content['bug']['value']).'">'.htmlentities($content['bug']['value']).'</a></div>';
			if(!empty($_GET['bug'][0])){
				echo '<div class="'.$options->class.' core-bug-more">';
					echo '<div class="'.$options->class.' core-bug-debug-title">Debug Dump</div>';
					echo '<div class="'.$options->class.' core-bug-debug-info">'.$content['bug-debug-info']['value'].'</div>';
				echo '</div>';	
			}
			echo '<div class="'.$options->class.' core-bug-title">'.$r->title.'</div>';
			echo '<div class="'.$options->class.' core-bug-status ';
				if($r->status == 'new'){ echo 'new '; }
				else if($r->status == 'closed'){ echo 'closed '; }
			echo '">'.$r->status.'</div>';
			echo '<div class="'.$options->class.' core-bug-site"><a href="'.$content['site'].'" target="_blank">'.$content['site'].'</a></div>';
			if(!empty($_GET['bug'][0])){
				echo '<div class="'.$options->class.' core-bug-replies-text">Replies</div>';
				echo '<div class="'.$options->class.' core-bug-replies-wrapper">';
					$replies = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_post_id = '".mysql_real_escape_string($r->id)."' ORDER BY comment_id DESC");
					$content = file_get_contents( (!empty($options->reply_template)) ? $options->reply_template : SITE.'/core/debug/reply_template.php' );
					foreach($replies as $reply){
						$reply->class = $options->class; //Replace $class
						echo $bg->template($content, $reply); 
					}
				echo '</div>';
				echo '<div class="'.$options->class.' core-bug-reply-wrapper">';
					if(!$bg->logged_in()){
						echo '<div>Login to Reply</div>';
					}
					echo '[login]';
					if($bg->logged_in()){
						echo '<div class="'.$options->class.' core-bug-reply-text">[forms {"ref":"core-bug-reply", "name":"text", "type":"textarea", "action":{"ajax":"core/debug/reply.php"}, "req":1 }]</div>';
						echo '<div class="'.$options->class.' core-bug-reply-submit">';
							echo 'Status: [forms {"ref":"core-bug-reply", "name":"status", "type":"select", "options":{"New":"new", "Reviewed":"reviewed", "More Info Needed":"more-info", "Closed":"closed", "Fixed":"fixed"}}]';
							echo '[forms {"ref":"core-bug-reply", "type":"submit", "value":"Reply"}]';
						echo '</div>';
						echo '[forms {"ref":"core-bug-reply", "type":"hidden", "name":"id", "value":"'.$_GET['bug'][0].'"}]';
						echo '[forms {"ref":"core-bug-reply", "type":"hidden", "name":"template", "value":"'.((!empty($options->reply_template)) ? $options->reply_template : SITE.'/core/debug/reply_template.php').'"}]';
						echo '[forms {"ref":"core-bug-reply", "type":"hidden", "name":"class", "value":"'.$options->class.'"}]';
						echo '[forms {"ref":"core-bug-reply", "type":"message", "error":"Could not submit reply right now.", "pending":"Submitting reply...", "complete":"Reply posted"}]';
					}
				echo '</div>';
			}
		echo '</div>';
	}
}

function core_debug_site_settings(){
	global $bg;
	echo '<div><span>Bug Server</span><input type="text" name="bug_server" value="'.$bg->settings->bug_server.'" /></div>';	
}

function core_debug_sidebar(){
	echo '<a href="#" class="debug" title="Debug Info"></a>';	
}

function core_debug_admin_head(){
	global $bg;
	$bg->add_css(URL.'/core/debug/debug.css');
}

function core_debug_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/debug/debug.js');
}

function core_debug_dialog(){
	echo '<div id="core-debug-wrapper" title="Debug Info">';
	echo '</div>';
}
?>