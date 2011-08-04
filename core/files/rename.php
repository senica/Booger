<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$file = $_POST['file'];
	$new = $_POST['val'];
	$newfile = dirname($file).'/'.$new; //preg_replace('@([/\\\\])[^/\\\\]+$@', '$1'.$new, $file);
	if(!rename($file, $newfile)){
		echo 'false';	
	}else{
		echo 'true';	
	}
?>