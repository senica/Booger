<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$loc = $_POST['dir'];
$type = $_POST['type'];

function delTree($dir) { 
	//By holger1 at NOSPAMzentralplan dot de
	if (is_dir($dir)) { 
		$objects = scandir($dir); 
		foreach ($objects as $object) { 
			if ($object != "." && $object != "..") { 
				if (is_dir($dir."/".$object)) delTree($dir."/".$object); else unlink($dir."/".$object); 
			} 
		} 
		reset($objects); 
		return rmdir($dir); 
	} 
}

if($type == "dir"){
	if(delTree($loc)){ echo "true"; }else{ echo "false"; }
}else if($type == "file"){
	if(unlink($loc)){ echo "true"; }else{ echo "false"; }
}
?>