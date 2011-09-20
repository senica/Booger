<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//The only thing we are doing here is builing the site and displaying hooks from $bg->add_hook('core-actions-hook', 'func');
$bg->call_hook('core-actions-hook');
?>