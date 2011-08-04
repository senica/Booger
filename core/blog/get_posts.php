<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$type = $_POST['type'];
$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE type='post' ORDER BY id DESC");
foreach($results as $result){
	if($type == 'select'){
		echo '<option value="'.$result->id.'" guid="'.$result->guid.'">'.$result->title.'</option>';
	}
	if($type == 'flat_list'){
		echo '<div class="core-blog-fl-pg-title" rel="'.$result->id.'" alt="'.$result->guid.'" dtitle="'.$result->title.'">';
		echo '<div class="core-blog-sidebar-title-menu"><div class="core-blog-sidebar-title-menu-content"><div class="arrow"></div><div class="edit">Edit</div><div class="options">Settings</div><hr /><div class="delete">Delete</div><div style="font-size:small">post id: '.$result->id.'</div></div></div>';
		echo $result->title.'</div>';	
	}
}
?>