<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_actions', 'actions', 1, 1);

$bg->set_permissions('functions_acl', 'core_actions_sidebar', 1, 1); 
$bg->set_permissions('functions_acl', 'core_actions_dialog', 1, 1);
?>