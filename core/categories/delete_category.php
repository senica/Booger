<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$id_set = array();
	function get_pages_loop($id="false"){
		global $bdb,$id_set;
		$results = $bdb->get_results("SELECT id FROM ".PREFIX."_content WHERE parent_id='$id'");
		foreach($results as $result){
			array_push($id_set, $result->id);
			get_pages_loop($result->id);
		}
	}
	array_push($id_set, $_POST['id']);
	get_pages_loop($_POST['id']);
	foreach($id_set as $id){
		$bdb->query("DELETE FROM ".PREFIX."_content WHERE id = '".$id."'");	
	}
?>