<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$offset = (isset($_GET['offset'])) ? $_GET['offset'] : 0;
$limit = (isset($_GET['limit'])) ? $_GET['limit'] : 5;
$extra = (isset($_GET['id']) && $_GET['id'] != 'undefined') ? "AND parent_id='".$_GET['id']."'" : "";
$type = (isset($_GET['type'])) ? $_GET['type'] : 'image'; //Get Images by default
$results = $bdb->get_results("SELECT id,title,guid,status,parent_id,content FROM ".PREFIX."_content WHERE type='".$type."' $extra ORDER BY id DESC LIMIT $offset,$limit");
foreach($results as $result){
	$content = unserialize($result->content);
	$mime = $content['mime'];
	$description = $content['description'];
	$test = preg_match("/^resample-.+?x.+?-(.*)/", $result->guid, $matches);
	$thumb = ($test > 0) ? $matches[1] : $result->guid;
	echo '<div dbid="'.$result->id.'" title="'.$result->title.'" description="'.$description.'" file="'.$result->guid.'" mime="'.$mime.'" style="margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:10px;">';
		if($type == 'image'){
			echo '<img src="'.URL.'/media/images/thumbs/'.$thumb.'" style="max-width:30px; max-height:30px; float:left; margin-right:15px" />';
		}
		echo '<div class="display-title">&nbsp;'.$result->title.'</div>';
		echo '<div><div class="button delete">Delete</div> <div class="button edit">Edit</div> <div class="button insert">Insert</div></div>';
		echo '<div style="width:100%; clear:both;"></div>';
		echo '<div class="edit-container" style="margin-top:10px;"></div>';
	echo '</div>';	
}
?>