<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$obj = (object) $_POST['post'];
$user = mysql_real_escape_string($obj->user['value']);
$pass = mysql_real_escape_string($obj->pass['value']);
$remember = $obj->remember['value'];
$json = array();
$json['error'] = false;
$result = false;

//Handle recovery
if(!empty($obj->recover)){ //Get new login information
	$result = $bdb->get_result("SELECT name,email,id,pass,alias FROM ".PREFIX."_acl WHERE email = '".mysql_real_escape_string(trim($obj->email['value']))."' AND type='user'");
	if(!$result){ $json['error'] = true; $json['message'] = 'Email address does not exist.'; }
	else{
		$domain = $_SERVER['HTTP_HOST'];
		$link = URL.'/admin/password.php?id='.$result->id.'&key='.hash('sha256', $bg_key.$result->email.$result->name.$result->pass);
		$domain = (isset($bg->settings->site_domain)) ? $bg->settings->site_domain : $_SERVER['HTTP_HOST'];
		$message = "While visiting ".$bg->settings->site_name.", someone said that this email address lost their password to ".URL.".<br /><br />If that somebody was you, visit the following link to get a new password.<br /><br /><a href='".$link."'>".$link."</a><br /><br />If you did not make this request, ignore this email.  Nothing has changed.<br/><br/>Your username is <b>".$result->alias."</b>.";			
		if( !$bg->email($result->email, $result->name, 'New Password Request', $message) ) {
		  $json['error'] = true; $json['message'] = 'Could not send email';
		} else {
		  $json['error'] = false; $json['message'] = 'Email sent';
		}
	}
}else if(!empty($obj->register)){ //Handle registration
	$result = $bdb->get_result("SELECT email FROM ".PREFIX."_acl WHERE email = '".mysql_real_escape_string($obj->email['value'])."' AND type='user'");
	if(!empty($result)){ $json['error'] = true; $json['message'] = 'Email already exists.'; }
	else{
		$result = $bdb->get_result("SELECT alias FROM ".PREFIX."_acl WHERE alias = '".mysql_real_escape_string($obj->alias['value'])."' AND type='user'");
		if(!empty($result)){ $json['error'] = true; $json['message'] = 'Alias already exists.'; }
		else{
			$pass = uniqid();
			$q = $bdb->query("INSERT INTO ".PREFIX."_acl (alias,name,email,website,pass)VALUES('".mysql_real_escape_string($obj->alias['value'])."', '".mysql_real_escape_string($obj->name['value'])."', '".mysql_real_escape_string($obj->email['value'])."', '".mysql_real_escape_string($obj->website['value'])."', AES_ENCRYPT('".sha1($pass)."', '".$bg_key."') )");		
			if(!$q){ $json['error'] = true; $json['message'] = 'There was a problem creating your account.'; }
			else{
				$id = $bdb->get_id();
				$result = $bdb->get_result("SELECT AES_ENCRYPT('".sha1($pass)."', '".$bg_key."') as pass");
				$domain = $_SERVER['HTTP_HOST'];
				$link = URL.'/admin/password.php?id='.$id.'&key='.hash('sha256', $bg_key.$obj->email['value'].$obj->name['value'].$result->pass);
				$domain = (isset($bg->settings->site_domain)) ? $bg->settings->site_domain : $_SERVER['HTTP_HOST'];
				$message = "Thank you for registering at ".$bg->settings->site_name.".<br /><br />Click on the following link to get a temporary password and/or set a new one.<br /><br /><a href='".$link."'>".$link."</a><br /><br />If you cannot click on the link, copy and paste into your browser.<br/><br/>Your username is <b>".$obj->alias['value']."</b>.";			
				if( !$bg->email($obj->email['value'], $obj->name['value'], 'New Account Registration', $message, '', $bg->settings->site_name, '', $bg->settings->site_name) ) {
				  $json['error'] = true; $json['message'] = 'Could not send your confirmation email.';
				} else {
				  $json['error'] = false; $json['message'] = 'Email sent';
				}		
			}
		}
	}
	
	//$q = $bdb->query("INSERT INTO ".PREFIX."_acl (name, email, website, alias)VALUES()");	
}else{ //Handle Login
	$result = $bdb->get_result("SELECT * FROM ".PREFIX."_acl WHERE alias = '".mysql_real_escape_string($user)."' AND AES_DECRYPT(pass,'".mysql_real_escape_string($bg_key)."') = '".$pass."' AND type='user'");
	if(!$result){ $json['error'] = true; $json['message'] = 'Wrong username or password'; }
	else if($result !== false){
		if($remember == 1 && $bg_cookie_days > 0){ $ct = time()+60*60*24*$bg_cookie_days; }
		else if($remember == 1 && $bg_cookie_days == 0){ $ct = 0; }
		else{ $ct = 0; }
		$hash = hash('sha256', $bg_key.$result->email.$result->name.$result->pass);
		setcookie("bg_authenticated_user", $hash, $ct, $bg_cookie_dir, $bg_cookie_domain, $bg_cookie_allow_js);
		$bdb->query("DELETE FROM ".PREFIX."_session WHERE session_user_id = '".mysql_real_escape_string($result->id)."'"); //Remove old sessions from expired cookies
		$bdb->query("INSERT INTO ".PREFIX."_session(session_user_id,session_data_check)VALUES('".mysql_real_escape_string($result->id)."','".mysql_real_escape_string($hash)."')");
		$json['error'] = false; $json['message'] = 'Verifying Login....';
	}else{ $json['error'] = true; $json['message'] = 'Unknown error occurred'; }
}

die(json_encode($json));
?>