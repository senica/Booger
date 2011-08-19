<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*	If you need to change the menu, there are four places to change it.
*	Two in this file, and two places in file.js
*/
/* Things to Do */
// Allow for drag and drop moving of files around.
$bg->add_hook('admin-sidebar', 'core_files_sidebar');
$bg->add_hook('admin-foot', 'core_files_js');
$bg->add_hook('admin-storage', 'core_files_storage');
$bg->add_hook('admin-head', 'core_files_head');

function core_files_head(){
	global $bg;
	$bg->add_css(URL.'/core/files/files.css');
}

function core_files_storage(){
	echo '<div class="core-files-storage"></div>';	
}

function core_files_sidebar(){
	echo '<h3 id="core-files-sidebar-title"><a href="#">Files</a></h3>';
	echo '<div class="content">';
		echo '<div style="font-size:small;">Drag and drop files from your computer.</div>';
		echo '<div class="core-files-sidebar-loading"><progress max="100" style="width:100px;"></progress></div>';
		//core_pages_get_list(THEMES.'/'.THEME, URL.'/themes/'.THEME);
		//Create parent directory so we can create new directories and files
		echo '<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="core/files/images/folder.png" /></span><span class="core-files-title-text" rel="site_root" label="dir" title="'.basename(SITE).'" save="'.SITE.'" loc="" alt="" file="'.SITE.'"><span>'.basename(SITE).'</span>';
			echo '<div class="core-files-sidebar-title-menu"><div class="core-files-sidebar-title-menu-content"><div class="arrow"></div><div class="folder">New Folder</div><div class="file">New File</div><div class="rename">Rename</div><hr /><div class="delete">Delete</div><hr />'.(($type == "file") ? '<div style="font-size:small; white-space:normal">Double-click to open</div>' : '<div style="font-size:small; white-space:normal">Single-click to expand</div>').'</div></div>';
		echo '</span></div><div id="core-files-child" class="core-files-child" style="display:block">';
		echo '</div></div>';
	echo '</div>';
}

function core_files_js(){
	global $bg;
	$bg->add_js(URL."/core/files/files.js");	
}
?>