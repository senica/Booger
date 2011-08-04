<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE type='tag'");
	foreach($results as $result){
		echo '<div class="core-tags-fl-tg-title" rel="'.$result->id.'" alt="'.$result->guid.'" dtitle="'.$result->title.'">';
		echo '<div class="core-tags-sidebar-title-menu"><div class="core-tags-sidebar-title-menu-content"><div class="arrow"></div><div class="edit">Edit</div><hr /><div class="delete">Delete</div><div style="font-size:small">tag id: '.$result->id.'</div></div></div>';
		echo $result->title.'</div>';	
	}
?>