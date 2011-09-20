<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('core-actions-hook', 'core_create_install_pkg_tab');

function core_create_install_pkg_tab(){
	global $bg;
	$bg->add_css('/core/create_install_pkg/create_install_pkg.css');
	echo '<div id="core-create-install-pkg-content">';
		echo '<fieldset><legend>Create Install Package</legend>';
			echo '<form class="form" method="post" target="create-install-pkg-iframe" action="/ajax.php?file=core/create_install_pkg/create.php">';
			echo '<div class="files"><span class="button load-files">Load Files</span></div>';
			echo '</form>';
			echo '<div style="font-size:small">A future release of this plugin should allow for the package to be created with the current database as a backup.</div>';
		echo '</fieldset>';
		echo '<iframe name="create-install-pkg-iframe" src="" style="width:300px; height:300px;"></iframe>';
	echo '</div>';
	$bg->add_js('/core/create_install_pkg/create_install_pkg.js');
}
?>