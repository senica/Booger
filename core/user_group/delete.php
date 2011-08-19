<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$id_set = array();
	function get_user_group_loop($id=0){
		global $bdb,$id_set;
		$results = $bdb->get_results("SELECT id FROM ".PREFIX."_acl WHERE parent_id='$id'");
		foreach($results as $result){
			array_push($id_set, $result->id);
			get_user_group_loop($result->id);
		}
	}
	array_push($id_set, $_POST['id']);
	get_user_group_loop($_POST['id']);
	foreach($id_set as $id){
		$bdb->query("DELETE FROM ".PREFIX."_acl WHERE id = '".$id."'");	
	}
?>