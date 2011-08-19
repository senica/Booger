<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$pass = false;
if(isset($_COOKIE['bg_authenticated_user'])){
	$result = $bdb->get_result("SELECT * FROM ".PREFIX."_session WHERE session_data_check = '".mysql_real_escape_string($_COOKIE['bg_authenticated_user'])."'");		
	if(!$result){
		setcookie("bg_authenticated_user", '', time()-42000, $bg_cookie_dir, $bg_cookie_domain, $bg_cookie_allow_js);
		$pass = false;
	}else if(count($result) > 0){ $pass = true; }
}else{ $pass = false; }
if(!$pass || $force_login==true){ die('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" id="bg-admin-login-html">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booger Admin Login</title>

<link type="text/css" href="admin/css/skin.css" rel="stylesheet" />
<script type="text/javascript" src="assets/js/sha1.js"></script>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/js/jquery-ui/jquery-ui-1.8.11.custom.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready( function(){
		jQuery("#bg-admin-login-form").submit( function(){
			var obj = {};
			obj.pass = sha1(jQuery("#bg-admin-login-form input[name=pass]").val());
			obj.user = jQuery("#bg-admin-login-form input[name=user]").val();
			obj.remember = (jQuery("#bg-admin-login-form input[name=remember]").is(":checked")) ? 1 : 0;
			obj.recover = jQuery("#bg-admin-login-form input[name=recovery]").val();
			var form = jQuery(this);
			jQuery("input", form).attr("disabled", "true");
			
			jQuery("#bg-admin-login-error").html("One moment please...");	
			jQuery("#bg-admin-login-error").slideDown("slow");
			
			jQuery.post("/ajax.php?file=admin/login.php", { obj:obj }, function(data){
				var json = jQuery.parseJSON(data);
				if(json.err == "true"){
					jQuery("#bg-admin-login-error").html(json.message);
					jQuery("#bg-admin-login-error").effect("shake", { times:1 }, 100);
					jQuery("input", form).removeAttr("disabled");
				}else if(json.err == "false"){
					if(obj.recover == 1){
						jQuery("#bg-admin-login-error").html(json.message);	
					}else{
						jQuery("#bg-admin-login-error").html("Alright, sit tight...");
						jQuery("#bg-admin-login-error").slideDown("slow");
						jQuery.post("/ajax.php?file=admin/verify_cookies.php", {}, function(data){
							if(data == "false"){
								jQuery("#bg-admin-login-error").html("Please enable cookies!");
								jQuery("#bg-admin-login-error").effect("shake", { times:1 }, 100);
								jQuery("input", form).removeAttr("disabled");
							}else{
								jQuery("#bg-admin-login-error").html("Here we go!");
								jQuery("#bg-admin-login-error").slideDown("slow");	
								document.location.href = '.( (isset($force_return)) ? '"'.$force_return.'"' : '"index.php"' ).';
							}
						});
					}
				}
			});
			return false;
		});
		
		jQuery("#lost-password").click( function(){
			if(jQuery(this).html() == "Login"){
				jQuery("#password-wrapper").show();
				jQuery("#login-submit-button").val("Login");
				jQuery("#lost-password").html("Lost your password?");
				jQuery("#bg-admin-login-form input[name=recovery]").val(0);
				jQuery("#bg-admin-login-form input").removeAttr("disabled");
			}else{
				jQuery("#password-wrapper").hide();
				jQuery("#login-submit-button").val("Send Me A New Password");
				jQuery("#lost-password").html("Login");
				jQuery("#bg-admin-login-form input[name=recovery]").val(1);
				jQuery("#bg-admin-login-form input").removeAttr("disabled");
			}
		});
	});
</script>

</head>

<body id="bg-admin-login-body">
	'.$bg_msg->formatted_errors.'
		<div id="bg-admin-login-wrapper">
			<form action="" method="post" id="bg-admin-login-form">
			<div id="bg-admin-login">
				<div id="bg-admin-login-logo"></div><br />
				'.((isset($force_message))?'<div id="bg-admin-login-msg-wrapper"><div id="bg-admin-login-msg">'.$force_message.'</div></div><br />':'').'
				<div id="bg-admin-login-error-wrapper"><div id="bg-admin-login-error"></div></div><br />
				<div id="bg-admin-login-box">
					Username
					<input type="text" name="user" />
					<div id="password-wrapper">
						Password
						<input type="password" name="pass" />
						<div id="bg-admin-remember"><input type="checkbox" name="remember" /> Remember Me</div>
					</div>
					<input id="login-submit-button" type="submit" name="submit" value="Login" class="submit" />
				</div><br />
				<input type="hidden" name="recovery" value="0" />
				<div id="bg-admin-lost"><a href=""><!-- Register</a> | --><a href="#" id="lost-password">Lost your password?</a></div>
			</div>
			</form>
		</div>
</body>
</html>
'); } ?>