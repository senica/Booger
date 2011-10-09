<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-sidebar', 'core_comments_sidebar');
$bg->add_hook('admin-foot', 'core_comments_admin_js');
$bg->add_hook('admin-head', 'core_comments_admin_css');
$bg->add_hook('admin-storage', 'core_comments_admin_dialog');

$bg->add_shortcode('comment-form', 'core_comments_form');
$bg->add_shortcode('comment-list', 'core_comments_list');
$bg->add_hook('site-foot', 'core_comments_site_foot'); //Must call $bg->call_hook('site-foot') in template

function core_comments_admin_dialog(){
	?>
	<div title="Comments" id="core-comments-admin-dialog">
		<div id="core-comments-admin-tabs">
			<ul>
				<li><a id="core-comment-admin-dialog-comments" href="#core-comments-admin-comments">Comments</a></li>
				<li><a id="core-comment-admin-dialog-approved" href="#core-comments-admin-approved">Approved</a></li>
				<li><a id="core-comment-admin-dialog-pending" href="#core-comments-admin-pending">Pending</a></li>
				<li><a id="core-comment-admin-dialog-spam" href="#core-comments-admin-spam">Spam</a></li>
			</ul>
			<div id="core-comments-admin-comments" style="height:350px; overflow-y:scroll;"></div>
			<div id="core-comments-admin-approved" style="height:350px; overflow-y:scroll;"></div>
			<div id="core-comments-admin-pending" style="height:350px; overflow-y:scroll;"></div>
			<div id="core-comments-admin-spam" style="height:350px; overflow-y:scroll;"></div>
		</div>	
	</div>
	<?php	
}

function core_comments_sidebar(){
	global $bdb;
	echo '<h3><a href="#">Comments</a></h3>';
	echo '<div id="core-comments-sidebar-content" class="content">';
		echo '<div class="count"></div>';
		echo '<div id="core-comments-sidebar-list">';
			echo '<div><span class="number">';
				$result = $bdb->get_result("SELECT COUNT(comment_id) AS count FROM ".PREFIX."_comments WHERE comment_type = 'comment'");
				echo $result->count;
			echo '</span><a href="#" class="total">Comments</a></div>';
			
			echo '<div><span class="number">';
				$result = $bdb->get_result("SELECT COUNT(comment_id) AS count FROM ".PREFIX."_comments WHERE comment_type = 'comment' AND comment_approved='1'");
				echo $result->count;
			echo '</span><a href="#" class="approved">Approved</a></div>';
			
			echo '<div><span class="number">';
				$result = $bdb->get_result("SELECT COUNT(comment_id) AS count FROM ".PREFIX."_comments WHERE comment_type = 'comment' AND comment_approved='0'");
				echo $result->count;
			echo '</span><a href="#" class="pending">Pending</a></div>';
			
			echo '<div><span class="number">';
				$result = $bdb->get_result("SELECT COUNT(comment_id) AS count FROM ".PREFIX."_comments WHERE comment_type = 'spam'");
				echo $result->count;
			echo '</span><a href="#" class="spam">Spam</a></div>';
		echo '</div>';
	echo '</div>';
}

function core_comments_admin_js(){
	global $bg;
	$bg->add_js(URL."/core/comments/admin-comments.js");	
}

function core_comments_admin_css(){
	global $bg;
	$bg->add_css(URL."/core/comments/admin-comments.css");	
}

function core_comments_list($obj){
	global $bg, $bdb;
	$bg->add_css($bg->plugin_url(false)."/site-comments-list.css"); //If list is called, add css
	$opt = $obj->options;
	$count = (isset($opt->count)) ? $opt->count : 5;
	//If page allows comments then show comment form
	if($bg->comments_allowed()){
	echo '
		<div class="comment-list">
		<input type="hidden" class="pageid" value="'.$bg->page_id.'" />
		<input type="hidden" class="count" value="'.$count.'" />
		<input type="hidden" class="loaded" value="0" />
		<div class="comment-list-list"></div>
		<div class="loading" style="display:none;"></div>
		<div><a href="#" class="more-comments">More Comments</a></div>
		</div>
	';
	}
}

function core_comments_form($obj){
	global $bg, $bdb;
	$bg->add_css($bg->plugin_url(false)."/site-comments-form.css"); //If form is called, add css
	$opt = $obj->options;
	if($bg->user->id == 0){
		$style = '';
		$login_style = 'display:none;';
		$name = '';
		$email = '';
		$website = '';
	}else{
		$style = 'display:none;';
		$login_style = '';
		$name = $bg->user->name;
		$email = $bg->user->email;
		$website = $bg->user->website;
	}
	//If page allows comments then show comment form
	if($bg->comments_allowed()){
	echo '
		<form class="comment-form-form">
			<div class="logged-in-as" style="'.$login_style.'">Logged in as <strong>'.$name.'</strong> <span class="not-you">(<a href="#">not you?</a>)</span></div>
			<div class="name-wrapper" style="'.$style.'"><input class="name-input" type="text" name="name" value="'.$name.'" /><span class="name-title">Name (required)</span></div>
			<div class="email-wrapper" style="'.$style.'"><input class="email-input" type="text" name="email" value="'.$email.'" /><span class="email-title">Email (required, will not be published)</span></div>
			<div class="website-wrapper" style="'.$style.'"><input class="website-input" type="text" name="website" value="'.$website.'" /><span class="website-title">Website (optional)</span></div>
			<div class="comment-title">Comment:</div>
			<div class="comment-wrapper"><textarea class="comment-input" name="comment"></textarea></div>
			<div class="submit-wrapper"><input class="submit-button" type="submit" value="Add Comment" name="submit" /></div>
			<div class="loading" style="display:none;"></div>
			<input class="comment-pageid" type="hidden" name="page_id" value="'.$bg->page_id.'" />
			<input class="parentid" type="hidden" name="parent_id" value="0" />
		</form>
	';
	}
}

function core_comments_site_foot(){
	global $bg;
	$bg->add_js(URL.'/core/comments/site-comments.js');
}

?>