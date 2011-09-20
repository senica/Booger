<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'user_group', 1, 1);

$bg->set_permissions('functions_acl', 'core_user_group_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_user_group_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_user_group_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_user_group_storage', 1, 1);

$bg->set_permissions('files_acl', 'core/user_group/add_edit.php', 1, 1);
$bg->set_permissions('files_acl', 'core/user_group/delete.php', 1, 1);
$bg->set_permissions('files_acl', 'core/user_group/get_groups.php', 1, 1);
$bg->set_permissions('files_acl', 'core/user_group/get_user_group.php', 1, 1);
$bg->set_permissions('files_acl', 'core/user_group/user_group.php', 1, 1);
?>