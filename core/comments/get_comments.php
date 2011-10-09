<?php require(ASSETS.'/no_direct.php'); ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
	$obj = (object) $_POST['obj'];
	
	function cycle($parent, $switch){
		global $obj, $bdb;
		$results = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_post_id = '".mysql_real_escape_string($obj->pageid)."' AND comment_parent='".mysql_real_escape_string($parent)."' AND comment_approved='1' ORDER BY comment_id DESC LIMIT ".mysql_real_escape_string($obj->loaded).", ".mysql_real_escape_string($obj->count) ); 
		foreach($results as $result){
			$even = ($switch%2 == 0) ? 'even' : '';
			$author = ($result->comment_author_url != "") ? '<a href="'.$result->comment_author_url.'" target="_blank">'.htmlentities($result->comment_author).'</a>' : htmlentities($result->comment_author);
			echo '
				<div class="comment-wrapper '.$even.'">
					<div class="parent">
					<input type="hidden" name="comment-id" class="comment-id" value="'.$result->comment_id.'" />
					<div class="posted-by '.$even.'"><div class="posted-by-text">Posted by&nbsp;</div><div class="posted-by-author">'.$author.'</div></div>
					<div class="posted-on '.$even.'"><div class="posted-on-text">on&nbsp;</div><div class="posted-on-date">'.$result->comment_date.'</div></div>
					<div class="reply '.$even.'"><a href="#">Reply</a></div>
					<div class="comment-text '.$even.'">'.htmlentities($result->comment_content).'</div>
					<div class="comment-form-wrapper"></div>
					</div>
					<div class="comment-child">'; $switch++; $switch = cycle($result->comment_id, $switch); echo '</div>
				</div>
			';
			//$switch++;
		}
		return $switch;
	}
	cycle(0, 0);
?>