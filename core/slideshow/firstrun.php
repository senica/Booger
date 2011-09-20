<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'slideshow', 0, 1);

$bg->set_permissions('functions_acl', 'core_slideshow_admin_storage', 1, 1); 
$bg->set_permissions('functions_acl', 'core_slideshow_admin_foot', 1, 1); 
$bg->set_permissions('functions_acl', 'core_slideshow_admin_tools', 1, 1); 
?>