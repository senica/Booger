<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$id = $_POST['id'];
	$file = $_POST['file'];
	$type = $_POST['type'];
	$bdb->query("DELETE FROM ".PREFIX."_content WHERE id = '$id'");
	if($type == 'image'){
		@unlink(SITE.'/media/images/'.$file);
		@unlink(SITE.'/media/images/thumbs/'.$file);
	}else{
		@unlink(SITE.'/media/uploads/'.$file);	
	}
	echo "true";
?>