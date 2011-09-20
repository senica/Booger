<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'media', 1, 1);

$bg->set_permissions('functions_acl', 'core_media_admin_tools', 1, 1); 
$bg->set_permissions('functions_acl', 'core_media_admin_storage', 1, 1); 
$bg->set_permissions('functions_acl', 'core_media_admin_foot', 1, 1); 

$bg->set_permissions('files_acl', 'core/media/db_insert.php', 1, 1);
$bg->set_permissions('files_acl', 'core/media/delete.php', 1, 1);
$bg->set_permissions('files_acl', 'core/media/get_edit.php', 1, 1);
$bg->set_permissions('files_acl', 'core/media/get_images.php', 1, 1);
$bg->set_permissions('files_acl', 'core/media/save_changes.php', 1, 1);
?>