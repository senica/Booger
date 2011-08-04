<?php require(ASSETS.'/no_direct.php'); ?>
<?php
function buildTree($id, $level=0){
	global $bdb;
	$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE parent_id=$id AND type='category'");
	foreach($results as $result){
		for($i=0; $i<$level; $i++){
			echo '<div class="core-categories-fl-cat-title-child">';	
		}
		echo '<div class="core-categories-fl-cat-title" rel="'.$result->id.'" alt="'.$result->guid.'" dtitle="'.$result->title.'">';
		echo '<div class="core-categories-sidebar-title-menu"><div class="core-categories-sidebar-title-menu-content"><div class="arrow"></div><div class="settings">Settings</div><div class="edit">Edit</div><hr /><div class="delete">Delete</div><div style="font-size:small">category id: '.$result->id.'</div></div></div>';
		echo $result->title.'</div>';	
		for($i=0; $i<$level; $i++){
			echo '</div>';	
		}
		buildTree($result->id, $level+1);
	}
}
buildTree('0');
?>