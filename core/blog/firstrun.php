<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'blog', 0, 1);

$bg->set_permissions('functions_acl', 'core_blog_css', 1, 1); 
$bg->set_permissions('functions_acl', 'core_blog_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_blog_js', 1, 1); 
$bg->set_permissions('functions_acl', 'core_blog_new_post_dialog', 1, 1);

$bg->set_permissions('files_acl', 'core/blog/create_post.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/delete.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_categories.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_info.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_posts.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_revisions.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_tags.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/get_templates.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/groups.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/restore.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/save.php', 1, 1);
$bg->set_permissions('files_acl', 'core/blog/users.php', 1, 1);
?>