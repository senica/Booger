<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->set_permissions('core_plugins', 'dashboard', 1, 1);

$bg->set_permissions('functions_acl', 'core_dashboard_head', 1, 1); 
$bg->set_permissions('functions_acl', 'core_dashboard_notes', 1, 1); 
$bg->set_permissions('functions_acl', 'core_dashboard_intro', 1, 1); 
?>