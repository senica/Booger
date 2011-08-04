<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$obj = $_POST['obj'];
foreach($obj as $k=>$v){
	$name = $k;
	$type = $obj[$k]['type'];
	$value= $obj[$k]['value'];
	if($type == 'password'){
		$bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('".$name."', HEX(AES_ENCRYPT('".mysql_real_escape_string($value)."', '".$bg_key."')) ) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");
	}else{
		$bdb->query("INSERT INTO ".PREFIX."_settings (setting_name, setting_value)VALUES('".$name."', '".mysql_real_escape_string($value)."') ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value) ");		
	}
}
echo 'true';
?>