<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin_tools', 'core_slideshow_admin_tools');
$bg->add_hook('admin-foot', 'core_slideshow_admin_foot');
$bg->add_hook('admin-storage', 'core_slideshow_admin_storage');

$bg->add_hook('site-head', 'core_slideshow_site_head');
$bg->add_hook('site-foot', 'core_slideshow_site_foot');
$bg->add_shortcode('core_slideshow', 'core_slideshow_func');

function core_slideshow_admin_storage(){ 
	echo '
		<div id="core-slideshow-admin-dialog" title="Slideshow">
			<div>Unique ID: <input type="text" class="id" /></div>
			<div>Style: <select class="style">';
				$dir = SITE.'/core/slideshow/styles/';
				$files = scandir($dir);
				foreach($files as $file){
					if(substr($file, -4) == '.css' && !is_dir($file)){
						echo '<option value="'.substr(basename($file), 0, strlen(basename($file))-4 ).'">'.substr(basename($file), 0, strlen(basename($file))-4 ).'</option>';	
					}
				}
			echo '</select></div>
			<div>Theme: <select class="theme">';
				$dir = SITE.'/core/slideshow/themes/';
				$files = scandir($dir);
				foreach($files as $file){
					if(is_dir($dir.'/'.$file) && $file != '.' && $file != '..'){
						echo '<option value="'.$file.'">'.$file.'</option>';	
					}
				}
			echo '</select></div>
			<div>Width: <input type="text" class="width" size="4" />  Height: <input type="text" class="height" size="4" /></div>
			<div><div class="button image">Select an Image</div> <div class="button insert">Insert Slideshow</div></div>
			<div class="images"></div>
		</div>
	';
}

function core_slideshow_admin_foot(){
	global $bg;
	$bg->add_js(URL.'/core/slideshow/admin_slideshow.js');
}

function core_slideshow_admin_tools(){
	echo '<div class="slideshow"><img src="'.URL.'/core/slideshow/images/slideshow.png" title="Slideshow" /></div>';
}

function core_slideshow_site_head(){
	global $bg;
	$bg->add_js(URL.'/assets/js/jquery.cycle.all.min.js');
}

function core_slideshow_func($obj){
	global $bg;
	$opt = $obj->options;
	if(isset($opt->style)){	$bg->add_css(URL.'/core/slideshow/styles/'.$opt->style.'.css'); }
	if(isset($opt->theme)){	$bg->add_css(URL.'/core/slideshow/themes/'.$opt->theme.'/'.$opt->theme.'.css'); }
	echo '<div class="core-slideshow-wrapper id'.$opt->id.'" style="'.((isset($opt->height)) ? 'height:'.$opt->height.'px;' : '').' '.((isset($opt->width)) ? 'width:'.$opt->width.'px;' : '').'">';
		echo '<div class="core-slideshow id'.$opt->id.'" rel="'.$opt->id.'">';
		foreach($opt->images as $k=>$v){
			echo '<div class="core-slideshow-item-wrapper id'.$opt->id.'" style="'.((isset($opt->height)) ? 'height:'.$opt->height.'px;' : '').' '.((isset($opt->width)) ? 'width:'.$opt->width.'px;' : '').'">';
				echo '<img class="core-slideshow-image id'.$opt->id.'" src="'.$v.'" style="'.((isset($opt->height)) ? 'height:'.$opt->height.'px;' : '').' '.((isset($opt->width)) ? 'width:'.$opt->width.'px;' : '').'" />';
				echo '<div class="core-slideshow-meta id'.$opt->id.'">';
					echo '<div class="core-slideshow-title id'.$opt->id.'">'.$opt->titles[$k].'</div>';
					echo '<div class="core-slideshow-description id'.$opt->id.'">'.$opt->descriptions[$k].'</div>';
				echo '</div>';
			echo '</div>';	
		}
		echo '</div>';
		echo '<div class="core-slideshow-prev id'.$opt->id.'">Previous</div><div class="core-slideshow-next id'.$opt->id.'">Next</div>';
		echo '<div class="core-slideshow-nav id'.$opt->id.'"></div>';
		echo '<div class="core-slideshow-controls id'.$opt->id.'"><div class="core-slideshow-play id'.$opt->id.'" rel="'.$opt->id.'">Play</div><div class="core-slideshow-pause id'.$opt->id.'" rel="'.$opt->id.'">Pause</div></div>';
	echo '</div>';
}

function core_slideshow_site_foot(){
	global $bg;
	$bg->add_js(URL.'/core/slideshow/slideshow.js');
}
?>