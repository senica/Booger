<?php require(ASSETS.'/no_direct.php'); ?>
<?php
function core_files_get_list($dir, $loc){
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			$type = '';
			if($file != "." && $file != ".."){
				echo '<div class="core-files-parent">';
					echo '<div class="core-files-title">';
						if(is_dir($dir.'/'.$file)){
							$type = 'dir';
							echo '<span class="core-files-img"><img src="core/files/images/folder.png" /></span>';	
						}elseif(is_file($dir.'/'.$file)){
							$type = 'file';
							echo '<span class="core-files-img"><img src="core/files/images/file.png" /></span>';	
						}
						echo '<span class="core-files-title-text" label="'.$type.'" file="'.$dir.'/'.$file.'" save="'.$dir.'/'.$file.'" loc="'.$loc.'/'.$file.'" title="'.$file.'" alt="'.$loc.'/'.$file.'"><span>'.$file.'</span>';
							echo '<div class="core-files-sidebar-title-menu"><div class="core-files-sidebar-title-menu-content"><div class="arrow"></div>'.(($type=='file')?'<div class="open">Open</div>':'').'<div class="folder">New Folder</div><div class="file">New File</div><div class="rename">Rename</div><hr /><div class="delete">Delete</div><hr />'.(($type == "file") ? '<div style="font-size:small; white-space:normal">Double-click to open</div>' : '<div style="font-size:small; white-space:normal">Single-click to expand</div>').'</div></div>';
						echo '<span>';
					echo '</div>';
					if(is_dir($dir.'/'.$file)){
						echo '<div class="core-files-child">';
							core_files_get_list($dir.'/'.$file, $loc.'/'.$file);
						echo '</div>';
					}
				echo '</div>';
			}
		}
	}	
}//get_list()
ob_start();
core_files_get_list(SITE, URL);
$content = ob_get_contents();
ob_end_clean();
header("Content-Length: ".strlen($content)); //For progress
echo $content;
?>