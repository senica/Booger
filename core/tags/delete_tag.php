<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bdb->query("DELETE FROM ".PREFIX."_content WHERE id = '".$_POST['id']."'");	
?>