<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	foreach($_POST as $k=>$v){
		${$k} = mysql_real_escape_string($v);
	}
	//$password = $_POST['password'];
	if($update == "true"){
		if($password != ''){
			$query = $bdb->query("UPDATE ".PREFIX."_acl SET parent_id='$parent_id', alias='$alias', name='$name', email='$email', pass=AES_ENCRYPT('$password', '$bg_key') WHERE id='$id'");		
		}else{
			$query = $bdb->query("UPDATE ".PREFIX."_acl SET parent_id='$parent_id', alias='$alias', name='$name', email='$email' WHERE id='$id'");	
		}
	}else{
		//if($password != ''){
			$query = $bdb->query("INSERT INTO ".PREFIX."_acl (parent_id, alias, name, email, pass, type, created_on)VALUES('$parent_id', '$alias', '$name', '$email', AES_ENCRYPT('$password', '$bg_key'), '$type', NOW())");
		//}else{
		//	$query = $bdb->query("INSERT INTO ".PREFIX."_acl (parent_id, alias, name, email, type)VALUES('$parent_id', '$alias', '$name', '$email', '$type')");	
		//}
	}
	if($query === false){ echo "false"; }else{ echo "true"; }
?>