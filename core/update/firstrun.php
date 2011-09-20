<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'update', 1, 1);

$bg->set_permissions('functions_acl', 'core_update_admin_sidebar_top', 1, 1); 
$bg->set_permissions('functions_acl', 'core_update_admin_css', 1, 1); 
$bg->set_permissions('functions_acl', 'core_update_admin_storage', 1, 1); 
$bg->set_permissions('functions_acl', 'core_update_admin_js', 1, 1);

$bg->set_permissions('files_acl', 'core/update/check.php', 1, 1);
$bg->set_permissions('files_acl', 'core/update/client.php', 1, 1);
$bg->set_permissions('files_acl', 'core/update/update.manifest', 1, 1);
$bg->set_permissions('files_acl', 'core/update/update.php', 1, 1);
?>