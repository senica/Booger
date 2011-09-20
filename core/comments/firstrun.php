<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'comments', 0, 1);

$bg->set_permissions('functions_acl', 'core_comments_admin_dialog', 1, 1); 
$bg->set_permissions('functions_acl', 'core_comments_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_comments_admin_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_comments_admin_css', 1, 1);

$bg->set_permissions('files_acl', 'core/comments/admin-change-status.php', 1, 1);
$bg->set_permissions('files_acl', 'core/comments/admin-get-comments.php', 1, 1);
?>