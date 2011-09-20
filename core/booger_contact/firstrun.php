<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'booger_contact', 1, 1);

$bg->set_permissions('functions_acl', 'core_booger_contact_admin_sidebar_top', 1, 1); 
$bg->set_permissions('functions_acl', 'core_booger_contact_admin_css', 1, 1); 
$bg->set_permissions('functions_acl', 'core_booger_contact_admin_storage', 1, 1); 
$bg->set_permissions('functions_acl', 'core_booger_contact_admin_js', 1, 1);

$bg->set_permissions('files_acl', 'core/booger_contact/booger_contact.php', 1, 1);
$bg->set_permissions('files_acl', 'core/booger_contact/get_form.php', 1, 1);
$bg->set_permissions('files_acl', 'core/booger_contact/send.php', 1, 1);
?>