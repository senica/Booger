<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$code = $_POST['code'];
$file = $_POST['file'];
$write = file_put_contents($file, $code);
if($write !== FALSE){
	echo "true";	
}else{
	echo "could not write the file";	
}
?>