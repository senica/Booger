<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['obj'];
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
	echo '
		<div class="comment-form">
		<form class="comment-form-form reply-form">
			<div class="logged-in-as" style="'.$login_style.'">Logged in as <strong>'.$name.'</strong> <span class="not-you">(<a href="#">not you?</a>)</span></div>
			<div class="name-wrapper" style="'.$style.'"><input class="name-input" type="text" name="name" value="'.$name.'" /><span class="name-title">Name (required)</span></div>
			<div class="email-wrapper" style="'.$style.'"><input class="email-input" type="text" name="email" value="'.$email.'" /><span class="email-title">Email (required, will not be published)</span></div>
			<div class="website-wrapper" style="'.$style.'"><input class="website-input" type="text" name="website" value="'.$website.'" /><span class="website-title">Website (optional)</span></div>
			<div class="comment-title">Comment:</div>
			<div class="comment-wrapper"><textarea class="comment-input" name="comment"></textarea></div>
			<div class="submit-wrapper"><input class="submit-button" type="submit" value="Add Comment" name="submit" /><a class="cancel-reply" href="#">Cancel</a></div>
			<div class="loading" style="display:none;"></div>
			<input class="comment-pageid" type="hidden" name="page_id" value="'.$obj->pageid.'" />
			<input class="parentid" type="hidden" name="parent_id" value="'.$obj->parent.'" />
		</form>
		</div>
	';
?>