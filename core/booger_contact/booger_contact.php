<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-sidebar-top', 'core_booger_contact_admin_sidebar_top');
$bg->add_hook('admin-head', 'core_booger_contact_admin_css');
$bg->add_hook('admin-storage', 'core_booger_contact_admin_storage');
$bg->add_hook('admin-foot', 'core_booger_contact_admin_js');

function core_booger_contact_admin_sidebar_top(){
	echo '<a href="#" class="booger-contact" title="Contact Booger"></a>';	
}

function core_booger_contact_admin_css(){
	global $bg;
	$bg->add_css(URL.'/core/booger_contact/booger_contact.css');
}

function core_booger_contact_admin_storage(){
	echo '<div id="core-booger-contact-admin-dialog" title="Contact Booger"></div>';	
}

function core_booger_contact_admin_js(){
	global $bg;
	$bg->add_js(URL.'/core/booger_contact/booger_contact.js');
}
?>