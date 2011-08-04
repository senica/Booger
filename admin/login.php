<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['obj'];
	$result = false;
	if($obj->recover == 1){
		$result = $bdb->get_result("SELECT name,email,id,pass FROM ".PREFIX."_acl WHERE alias = '".mysql_real_escape_string($obj->user)."' AND type='user'");
		if(!$result){ $json['err'] = 'true'; $json['message'] = 'Email address does not exist.'; echo json_encode($json); }
		else{
			$domain = $_SERVER['HTTP_HOST'];
			$link = URL.'/admin/password.php?id='.$result->id.'&key='.hash('sha256', $bg_key.$result->email.$result->name.$result->pass);
			$domain = (isset($bg->settings->site_domain)) ? $bg->settings->site_domain : $_SERVER['HTTP_HOST'];
			$message = "While visiting ".$bg->settings->site_name.", someone said that this email address lost their password to ".URL.".<br /><br />If that somebody was you, visit the following link to get a new password.<br /><br /><a href='".$link."'>".$link."</a><br /><br />If you did not make this request, ignore this email.  Nothing has changed.";			
			if( !$bg->email($result->email, $result->name, 'New Password Request', $message, 'no-reply@'.$domain, 'Password Recovery', 'password-recovery@'.$domain, 'Password Recovery') ) {
			  $json['err'] = 'true'; $json['message'] = 'Could not send email'; echo json_encode($json);
			} else {
			  $json['err'] = 'false'; $json['message'] = 'Email sent'; echo json_encode($json);
			}
		}
	}else{
		$result = $bdb->get_result("SELECT * FROM ".PREFIX."_acl WHERE alias = '".mysql_real_escape_string($obj->user)."' AND AES_DECRYPT(pass,'".mysql_real_escape_string($bg_key)."') = '".mysql_real_escape_string($obj->pass)."' AND type='user'");
		if(!$result){ $json['err'] = 'true'; $json['message'] = 'Wrong username or password'; echo json_encode($json); }
		else if($result !== false){
			if($obj->remember == 1 && $bg_cookie_days > 0){ $ct = time()+60*60*24*$bg_cookie_days; }
			else if($obj->remember == 1 && $bg_cookie_days == 0){ $ct = 0; }
			else{ $ct = 0; }
			$hash = hash('sha256', $bg_key.$result->email.$result->name.$result->pass);
			setcookie("bg_authenticated_user", $hash, $ct, $bg_cookie_dir, $bg_cookie_domain, $bg_cookie_allow_js);
			$bdb->query("DELETE FROM ".PREFIX."_session WHERE session_user_id = '".mysql_real_escape_string($result->id)."'"); //Remove old sessions from expired cookies
			$bdb->query("INSERT INTO ".PREFIX."_session(session_user_id,session_data_check)VALUES('".mysql_real_escape_string($result->id)."','".mysql_real_escape_string($hash)."')");
			$json['err'] = 'false'; $json['message'] = 'Verifying Login....'; echo json_encode($json);
		}else{ $json['err'] = 'true'; $json['message'] = 'Unknown error occurred'; echo json_encode($json); }
	}
?>