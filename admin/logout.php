<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	setcookie("bg_authenticated_user", '', time()-42000, $bg_cookie_dir, $bg_cookie_domain, $bg_cookie_allow_js);
	$bdb->query("DELETE FROM ".PREFIX."_session WHERE session_user_id = '".mysql_real_escape_string($bg->user->id)."'"); //Delete old sessions
	echo true;
?>