<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'site_settings', 1, 1);

$bg->set_permissions('functions_acl', 'core_site_settings_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_site_settings_admin_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_site_settings_admin_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_site_settings_dialog', 1, 1);

$bg->set_permissions('files_acl', 'core/site_settings/get_settings.php', 1, 1);
$bg->set_permissions('files_acl', 'core/site_settings/save.php', 1, 1);
$bg->set_permissions('files_acl', 'core/site_settings/site_settings.php', 1, 1);
?>