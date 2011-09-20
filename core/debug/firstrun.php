<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'debug', 0, 1);

$bg->set_permissions('functions_acl', 'core_debug_site_settings', 1, 1); 
$bg->set_permissions('functions_acl', 'core_debug_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_debug_admin_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_debug_admin_js', 1, 1);
$bg->set_permissions('functions_acl', 'core_debug_dialog', 1, 1);

$bg->set_permissions('files_acl', 'core/debug/compile.php', 1, 1);
$bg->set_permissions('files_acl', 'core/debug/debug.php', 1, 1);
?>