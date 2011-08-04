<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$file = $_POST['file'];
echo file_get_contents($file);
?>