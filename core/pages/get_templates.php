<?php require(ASSETS.'/no_direct.php'); ?>
<?php
echo '<select id="core-pages-template">';
function dirTree($dir){
	$files = scandir($dir);
	foreach($files as $file){
		if(is_dir($dir.'/'.$file) && $file != '.' && $file != '..'){
			dirTree($dir.'/'.$file);	
		}else if(substr($file, -7) == 'tpl.php'){
			echo '<option value="'.basename($file).'">'.substr(basename($file), 0, strlen(basename($file))-8 ).'</option>';	
		}
	}
}
dirTree(THEME_DIR);
echo '</select>';
?>