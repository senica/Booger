<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$obj = (object) $_POST['obj'];

//Generate thumbnail if Upload is an image
if($obj->type == "image/jpeg" || $obj->type == "image/png" || $obj->type == "image/gif"){
	require_once('thumb.php');
	$image = new SimpleImage();
	$test = preg_match("/^resample-.+?x.+?-(.*)/", $obj->file, $matches);
	$thumb = ($test > 0) ? $matches[1] : $obj->file;
	$image->load($obj->save.'/'.$obj->file);
	$width = $image->info[0]; $height = $image->info[1];
	if(!file_exists($obj->save.'/thumbs/'.$thumb)){
		if($width > $height){
			$image->resizeToWidth(THUMBNAIL_WIDTH); //This size in config.php
		}else{
			$image->resizeToHeight(THUMBNAIL_WIDTH);	
		}
		$image->save($obj->save.'/thumbs/'.$thumb);;
	}
	$json['width'] = $width;
	$json['height'] = $height;
	$type = 'image';
}else{
	$type = 'upload';	
}

$content['mime'] = $obj->type;
$content = serialize($content);
$result = $bdb->query("INSERT INTO ".PREFIX."_content (title, guid, parent_id, type, content)VALUES('".mysql_real_escape_string($obj->file)."', '".mysql_real_escape_string($obj->file)."', '".mysql_real_escape_string($obj->gallery_id)."', '".mysql_real_escape_string($type)."', '".mysql_real_escape_string($content)."')");
$json['dbid'] = $bdb->get_id();

echo json_encode($json); //return the id of the inserted row;
?>