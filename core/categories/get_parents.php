<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	echo '<option value="0" guid="0">none</option>';
	$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE type='category'");
	foreach($results as $r){
		echo '<option value="'.$r->id.'" guid="'.$r->guid.'">'.$r->title.'</option>';	
	}
?>