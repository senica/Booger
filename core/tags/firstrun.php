<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'tags', 0, 1);

$bg->set_permissions('functions_acl', 'core_tags_settings', 1, 1); 
$bg->set_permissions('functions_acl', 'core_tags_dialog', 1, 1); 
$bg->set_permissions('functions_acl', 'core_tags_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_tags_js', 1, 1);
$bg->set_permissions('functions_acl', 'core_tags_head', 1, 1);

$bg->set_permissions('files_acl', 'core/tags/delete_tag.php', 1, 1);
$bg->set_permissions('files_acl', 'core/tags/save_tag.php', 1, 1);
?>