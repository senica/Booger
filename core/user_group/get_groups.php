<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	echo '<option value="0">Public</option>';
	$result = $bdb->get_results("SELECT id,name FROM ".PREFIX."_acl WHERE type = 'group'");
	foreach($result as $r){
		echo '<option value="'.$r->id.'">'.$r->name.'</option>';	
	}
?>