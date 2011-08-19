<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-storage', 'core_media_admin_storage');
$bg->add_hook('admin-foot', 'core_media_admin_foot');
$bg->add_hook('admin_tools', 'core_media_admin_tools');

function core_media_admin_tools(){
	echo '<div class="media"><img src="'.URL.'/core/media/images/media.png" title="Media Manager" /></div>';		
}

function core_media_admin_storage(){ ?>
	<div id="core-media-dialog" title="Media Manager">
		<div id="core-media-tabs">
			<ul>
				<li><a href="#core-media-tab-upload">Upload</a></li>
				<li><a href="#core-media-tab-gallery">Page Gallery</a></li>
				<li><a href="#core-media-tab-images">Images</a></li>
				<li><a href="#core-media-tab-other">Other Media</a></li>
			</ul>
			<div id="core-media-tab-upload" style="max-height:400px; overflow-y:scroll;">
				<div id="core-media-upload-wrapper"></div>
			</div>
			<div id="core-media-tab-gallery">
				<div style="margin-bottom:20px;"><div class="button refresh">Refresh</div></div>
				<div style="height:400px; overflow:scroll;" id="core-media-gallery-wrapper"></div>
			</div>
			<div id="core-media-tab-images">
				<div style="margin-bottom:20px;"><div class="button refresh">Refresh</div></div>
				<div style="height:400px; overflow:scroll;" id="core-media-images-wrapper"></div>
			</div>
			<div id="core-media-tab-other">
				<div style="margin-bottom:20px;"><div class="button refresh">Refresh</div></div>
				<div style="height:400px; overflow:scroll;" id="core-media-other-wrapper"></div>
			</div>
		</div>
	</div>
<?php }

function core_media_admin_foot(){
	global $bg;
	$bg->add_js(URL.'/core/media/media.js');
}
?>