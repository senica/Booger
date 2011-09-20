<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'pages', 0, 1);

$bg->set_permissions('functions_acl', 'core_pages_css', 1, 1); 
$bg->set_permissions('functions_acl', 'core_pages_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_pages_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_pages_new_page_dialog', 1, 1);

$bg->set_permissions('files_acl', 'core/pages/create_page.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/delete.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/get_revisions.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/get_templates.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/groups.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/restore.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/save.php', 1, 1);
$bg->set_permissions('files_acl', 'core/pages/users.php', 1, 1);
?>