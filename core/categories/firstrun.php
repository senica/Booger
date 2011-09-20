<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'categories', 0, 1);

$bg->set_permissions('functions_acl', 'core_categories_settings', 1, 1); 
$bg->set_permissions('functions_acl', 'core_categories_dialog', 1, 1); 
$bg->set_permissions('functions_acl', 'core_categories_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_categories_js', 1, 1);
$bg->set_permissions('functions_acl', 'core_categories_head', 1, 1);

$bg->set_permissions('files_acl', 'core/categories/delete_category.php', 1, 1);
$bg->set_permissions('files_acl', 'core/categories/get_categories.php', 1, 1);
$bg->set_permissions('files_acl', 'core/categories/get_info.php', 1, 1);
$bg->set_permissions('files_acl', 'core/categories/get_parents.php', 1, 1);
$bg->set_permissions('files_acl', 'core/categories/get_templates.php', 1, 1);
$bg->set_permissions('files_acl', 'core/categories/save_category.php', 1, 1);
?>