<?php require(ASSETS.'/no_direct.php'); ?>
<?php
if($_GET['type'] == 'upload'){
	function dirTree($dir){
		$files = scandir($dir);
		foreach($files as $file){
			if(is_dir($dir.'/'.$file) && $file != '.' && $file != '..'){
				dirTree($dir.'/'.$file);	
			}else if($file != '.' && $file != '..'){
				$url = str_replace(SITE, URL, $dir);
				echo '<option value="'.$url.'/'.$file.'">'.$file.'</option>';
			}
		}
	}
	dirTree(SITE.'/media/uploads');	
}else{
	$type = (isset($_GET['type'])) ? "type='".$_GET['type']."'" : '';
	function buildTree($id, $level=0){
		global $bdb,$type;
		$query = ($_GET['type'] == 'page') ? "parent_id='$id' AND" : '';
		$results = $bdb->get_results("SELECT id,title,guid FROM ".PREFIX."_content WHERE $query $type");
		foreach($results as $result){
			echo '<option value="'.$result->id.'">';
			for($i=0; $i<$level; $i++){
				echo '&nbsp;&nbsp;';	
			}
			echo $result->title.'</option>';
			if($_GET['type'] == 'page'){ buildTree($result->id, $level+1); }
		}
	}
	buildTree('0', 0);
}
?>