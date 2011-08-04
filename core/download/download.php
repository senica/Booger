<?php
$bg->add_shortcode('download', 'core_download_func');
$bg->add_hook('site-foot', 'core_download_foot');
	
function core_download_func($obj){
	global $bg;
	$options = $obj->options;
	
	echo '<div class="'.$options->class.' wrapper">'; //Required for Javascript
	if(isset($options->files)){
		echo '<div class="'.$options->class.' options-wrapper">';
		$i=1;
		$id = uniqid('dl_');
		foreach($options->files as $k=>$v){
			if(isset($options->noradio)){
				echo '<div class="'.$options->class.' core-download-no-radio" rel="'.$id.'"><div class="radio '.$options->class.' '.((isset($options->check) && $i == $options->check) ? 'active' : (!isset($options->check) && $i == 1) ? 'active' : '').'"></div><span class="text '.$options->class.'">'.$v.'</span></div>';
				echo '<div class="'.$options->class.'" style="display:none;"><input class="'.$options->class.'" type="radio" name="'.$id.'" value="'.$k.'" '.((isset($options->check) && $i == $options->check) ? 'checked' : (!isset($options->check) && $i == 1) ? 'checked' : '').'></div>';	
			}else{
				echo '<div class="'.$options->class.'"><input class="'.$options->class.'" type="radio" name="'.$id.'" value="'.$k.'" '.((isset($options->check) && $i == $options->check) ? 'checked' : (!isset($options->check) && $i == 1) ? 'checked' : '').' /><span class="text '.$options->class.'">'.$v.'</span></div>';	
			}
			$i++;
		}
		echo '</div>';
		echo '<a href="#" class="core-download button '.$options->class.'">'.((isset($options->text)) ? $options->text : 'Download').'</a>';
	}else if(isset($options->file)){
		echo '<a href="ajax.php?file=core/download/handle.php&get='.$options->file.'" class=" button '.$options->class.'">'.((isset($options->text)) ? $options->text : 'Download').'</a>';	
	}
	echo '</div>';
}

function core_download_foot(){
	global $bg;
	$bg->add_js(URL.'/core/download/download.js');
}
?>