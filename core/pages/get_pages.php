<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$type = $_POST['type'];
function buildTree($id, $type='', $level=0){
	global $bdb;
	$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE parent_id=$id AND type='page'");
	foreach($results as $result){
		if($type == 'select'){
			echo '<option value="'.$result->id.'" guid="'.$result->guid.'">';
			for($i=0; $i<$level; $i++){
				echo '&nbsp;&nbsp;';	
			}
			echo $result->title.'</option>';
		}
		if($type == 'flat_list'){
			for($i=0; $i<$level; $i++){
				echo '<div class="core-pages-fl-pg-title-child">';	
			}
			echo '<div class="core-pages-fl-pg-title" rel="'.$result->id.'" alt="'.$result->guid.'" dtitle="'.$result->title.'">';
			echo '<div class="core-pages-sidebar-title-menu"><div class="core-pages-sidebar-title-menu-content"><div class="arrow"></div><div class="edit">Edit</div><div class="options">Settings</div><hr /><div class="delete">Delete</div><div style="font-size:small">page id: '.$result->id.'</div></div></div>';
			echo $result->title.'</div>';	
			for($i=0; $i<$level; $i++){
				echo '</div>';	
			}
		}
		buildTree($result->id, $type, $level+1);
	}
}
buildTree('0', $type);
?>