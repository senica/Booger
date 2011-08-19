<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$results = $bdb->get_results("SELECT id,name FROM ".PREFIX."_acl WHERE type = 'user'");
	foreach($results as $r){
		echo '<option value="'.$r->id.'">'.$r->name.'</option>';	
	}
?>