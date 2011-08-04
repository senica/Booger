<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['data'];
	
	$resize = '';
	
	if($obj->type == "image/jpeg" || $obj->type == "image/png" || $obj->type == "image/gif"){
		require_once('thumb.php');
		$original = new SimpleImage();
		$orig_info = $original->info($obj->save.'/'.$obj->file);
		
		$test = preg_match("/^resample-.+?x.+?-(.*)/", $obj->file, $matches);
		$thumb = ($test > 0) ? $matches[1] : $obj->file;	
		$tempthumb = (!file_exists($obj->save.'/thumbs/'.$thumb)) ? 'core/media/images/nothumb.png' : 'media/images/thumbs/'.$thumb;
		
		$return['img_thumb'] = URL.'/media/images/thumbs/'.$thumb;
		$return['orig_width'] = $orig_info[0];
		$return['orig_height'] = $orig_info[1];
		
		$resize = '
				<img class="tempthumb" src="'.URL.'/'.$tempthumb.'" />
				<h4 style="margin-bottom:0px;">Resize</h4>
				<div style="margin-bottom:10px;">
					width: <input class="width" type="text" size="4" value="'.$orig_info[0].'" /> height: <input class="height" type="text" size="4" value="'.$orig_info[1].'" /> <div  id="core-media-resize-lock" style="display:inline-block; width:14px; height:20px; background:url(core/media/images/resize_lock.png); background-repeat:none; background-position:0px -20px;"></div>
				</div>
				';
		$home_thumb = '
				<div style="margin-bottom:10px;">Set image as page/post thumbnail? <input type="checkbox" class="is-thumb" /></div>
				';
	}
	$return['edit_content'] = '
			<div>
				'.$resize.'
				<div>Title: <input type="text" class="title" value="'.((isset($obj->title))?$obj->title:$obj->file).'" /></div>
				<div>Description:</div>
				<div><textarea class="desc" style="width:100%; height:40px;">'.((isset($obj->description))?$obj->description:'').'</textarea></div>
				'.$home_thumb.'
				<div><div class="button save">Save</div> <div class="button cancel">Cancel</div></div>
			</div>
			';
	echo json_encode($return);
?>