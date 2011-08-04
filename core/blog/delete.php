<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$bdb->query("DELETE FROM ".PREFIX."_content WHERE id = '".mysql_real_escape_string($_POST['id'])."'");	
?>