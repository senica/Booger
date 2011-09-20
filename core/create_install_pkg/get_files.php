<?php require(ASSETS.'/no_direct.php'); ?>
<?php
function core_create_install_pkg_get_list($dir){
	if ($handle = opendir($dir)) {
		while (false !== ($file = readdir($handle))) {
			$type = '';
			if($file != "." && $file != ".."){
				echo '<div class="core-create-install-pkg-parent">';
					echo '<div class="core-create-install-pkg-title">';
						$val = str_replace(SITE, '', $dir.'/'.$file);
						echo '<input type="checkbox" name="'.uniqid("id").'" value="'.$val.'" checked /> ';
						if(is_dir($dir.'/'.$file)){
							$type = 'dir';
							echo '<span class="core-create-install-pkg-img"><img src="core/create_install_pkg/images/folder.png" /></span>';	
						}elseif(is_file($dir.'/'.$file)){
							$type = 'file';
							echo '<span class="core-create-install-pkg-img"><img src="core/create_install_pkg/images/file.png" /></span>';	
						}
						echo '<span class="core-create-install-pkg-title-text"><span>'.$file.'</span></span>';
					echo '</div>';
					if(is_dir($dir.'/'.$file)){
						echo '<div class="core-create-install-pkg-child">';
							core_create_install_pkg_get_list($dir.'/'.$file);
						echo '</div>';
					}
				echo '</div>';
			}
		}
	}	
}//get_list()
ob_start();
core_create_install_pkg_get_list(SITE);
$content = ob_get_contents();
ob_end_clean();
header("Content-Length: ".strlen($content)); //For progress
echo $content;
?>