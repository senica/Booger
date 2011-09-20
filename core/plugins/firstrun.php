<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'plugins', 1, 1);

$bg->set_permissions('functions_acl', 'core_plugins_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_plugins_admin_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_plugins_admin_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_plugins_dialog', 1, 1);

$bg->set_permissions('files_acl', 'core/plugins/save.php', 1, 1);
?>