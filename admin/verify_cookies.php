<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	if(!isset($_COOKIE['bg_authenticated_user'])){ echo false; }
	else if(isset($_COOKIE['bg_authenticated_user'])){ echo true; }
	else{ echo false; }
?>