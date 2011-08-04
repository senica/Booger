<?php require_once("../assets/config.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booger - Password Recovery</title>
<style type="text/css">
html, body{ margin:0; padding:0; background:#F9F9F9; width:100%; text-align:center; }
#wrapper{ display:inline-block; margin-top:120px; }
#message{ color:#666; font-weight:bold; text-align:left; padding:20px; font-size:14px; font-family:"Lucida Sans Unicode", "Lucida Grande", sans-serif;  -webkit-box-shadow: 2px 2px 10px #666666; -moz-box-shadow: 2px 2px 10px #666666; box-shadow: 2px 2px 10px #666666 ; display:inline-block; height:200px; width:300px; border:0; border-radius:10px; -moz-border-radius:10px; -webkit-borde-radius:10px; background:-webkit-gradient(linear, 0% 100%, 0% 0%, color-stop(0.15, rgb(242,242,242)), color-stop(0.61, rgb(255,255,255))); background:-moz-linear-gradient( center bottom, rgb(242,242,242) 15%, rgb(255,255,255) 61% ); }
#error405{ color:#aaa; font-weight:normal; }
#logo{ display:inline-block; background-image:url("../../admin/images/booger_logo.png"); width:183px; height:86px; }

</style>
</head>

<body>
<div id="wrapper">
<div id="logo"></div><br />
<div id="message">
<?php
	$passed = false;
	function createRandomPassword() {
		$chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
		srand((double)microtime()*1000000); 
		$i = 0; 
		$pass = '' ; 
		while ($i <= 7) { 
			$num = rand() % 33; 
			$tmp = substr($chars, $num, 1); 
			$pass = $pass . $tmp; 
			$i++; 
		} 
		return $pass;
	}

	$id = $_GET['id'];
	$key = $_GET['key'];
	$result = $bdb->get_result("SELECT name,email,id,pass FROM ".PREFIX."_acl WHERE id = '".mysql_real_escape_string($id)."' AND type='user'");
	if(!$result){ echo 'You may be using an old link.<br /><br />Maybe try requesting another password recovery email.'; }
	else{
		$test = hash('sha256', $bg_key.$result->email.$result->name.$result->pass);
		if($test == $key){
			$password = createRandomPassword(); 
			$query = $bdb->query("UPDATE ".PREFIX."_acl SET pass=AES_ENCRYPT('".sha1($password)."', '$bg_key') WHERE id='$id'");
			if(!$query){
				echo 'Something went wrong on our side.  Please try your link again later.';	
			}else{
				echo 'Your temporary password is<h2>'.$password.'</h2>You can change this once <a href="'.URL.'/admin/">logged in</a>.';
				$passed = true;
			}
		}else{
			echo 'Something has changed on our side since you received your password recovery email. You can always request another one.';	
		}
	}		
?>
<br />
<br />
<div id="error405">
	<?php if(!$passed): ?>
		This page doesn't do anything without an access key. If you forgot your password, go to the login page and request a new one there.</div>
	<?php else: ?>
		Be sure to write this down in the meantime.
	<?php endif; ?>
</div>
</div>
</body>
</html>