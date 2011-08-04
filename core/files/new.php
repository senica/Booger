<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$dir = $_POST['dir'];
$type = $_POST['type'];
if(is_dir($dir)){
	$folder = $dir.'/New Folder';
	$file = $dir.'/untitled.php';
}else{
	$folder = dirname($dir).'/New Folder';
	$file = dirname($dir).'/untitled.php';
}



if($type == "folder"){
	if( mkdir($folder, 0755) ){ echo $folder; }else{ echo "false"; }
}else if($type == "file"){
	if($f = fopen($file, 'xb')){ fclose($f); echo $file; }else{ echo "false"; fclose($f); }
}
?>