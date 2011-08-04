<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['data'];
	
	if($obj->type == "image/jpeg" || $obj->type == "image/png" || $obj->type == "image/gif"){	
		$lock = $_POST['lock'];
		$width = $_POST['width'];
		$height = $_POST['height'];
		$isthumb = ($_POST['isthumb'] == "true") ? 1 : 0;
		$id = (isset($_POST['docid'])) ? $_POST['docid'] : 0;
		
		if($isthumb == 1 && $id != 0){  //Set all images with docid to not home thumb but only if docid ($id) is set
			$bdb->query("UPDATE ".PREFIX."_content SET status='0' WHERE parent_id = '$id' AND type='image'");
		} 	
		
		require_once('thumb.php');
		$image = new SimpleImage();
		
		//Get original name - Resample from Original File
		$test = preg_match("/^resample-.+?x.+?-(.*)/", $obj->file, $matches);
		if($test > 0){ $save = $matches[1]; }
		else{ $save = $obj->file; }
		
		$image->load($obj->save.'/'.$obj->file);
		
		if($image->info[0] != $width || $image->info[1] != $height){ //Make sure we actually need to resize first
			if($lock == "true"){
				$image->resizeToWidth($width);
			}else{
				$image->resize($width, $height);	
			}
			
			$obj->file = 'resample-'.$width.'x'.$height.'-'.$save;
			$obj->thumb = $save; //For List of Images
			
			$image->save($obj->save.'/'.$obj->file);
			
			//Make new database entry for new image
			$content['mime'] = $obj->type;
			$content = serialize($content);
			$bdb->query("INSERT INTO ".PREFIX."_content (parent_id, type, content)VALUES('".mysql_real_escape_string($id)."', 'image', '".mysql_real_escape_string($content)."')");
			$obj->dbid = $bdb->get_id();			
		
			$info = $image->info($obj->save.'/'.$json['new_file']);
			$obj->width = $info[0];
			$obj->height = $info[1];
		}
	}//End If Type is Image
	
	//Update db with new info
	$result = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE  id='".$obj->dbid."'");
	$content = unserialize($result->content);
	$content['description'] = $_POST['desc']; //add caption later if needed $desc['caption'] ==
	$title = $_POST['title'];
	$bdb->query("UPDATE ".PREFIX."_content SET guid='".mysql_real_escape_string($obj->file)."', status='".mysql_real_escape_string($isthumb)."', content='".mysql_real_escape_string(serialize($content))."', title='".mysql_real_escape_string($title)."' WHERE id='".$obj->dbid."'");
	
	$obj->message = "noerror";
	
	echo json_encode($obj);
?>



