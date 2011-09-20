<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'files', 1, 1);

$bg->set_permissions('functions_acl', 'core_files_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_files_storage', 1, 1); 
$bg->set_permissions('functions_acl', 'core_files_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_files_js', 1, 1);

$bg->set_permissions('files_acl', 'core/files/delete.php', 1, 1);
$bg->set_permissions('files_acl', 'core/files/get_code.php', 1, 1);
$bg->set_permissions('files_acl', 'core/files/get_files.php', 1, 1);
$bg->set_permissions('files_acl', 'core/files/new.php', 1, 1);
$bg->set_permissions('files_acl', 'core/files/rename.php', 1, 1);
$bg->set_permissions('files_acl', 'core/files/save_code.php', 1, 1);
?>