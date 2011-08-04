<?php require(ASSETS.'/no_direct.php'); ?>
<?php 
$id = $_POST['id'];
$status = $_POST['status'];
$query = false;
if($status == 'pending'){
	$query = $bdb->query("UPDATE ".PREFIX."_comments SET comment_type='comment', comment_approved='0' WHERE comment_id='$id'");	
}
if($status == 'approved'){
	$query = $bdb->query("UPDATE ".PREFIX."_comments SET comment_type='comment', comment_approved='1' WHERE comment_id='$id'");	
}
if($status == 'spam'){
	$query = $bdb->query("UPDATE ".PREFIX."_comments SET comment_type='spam', comment_approved='0' WHERE comment_id='$id'");	
}
if($status == 'notspam'){
	$query = $bdb->query("UPDATE ".PREFIX."_comments SET comment_type='comment', comment_approved='0' WHERE comment_id='$id'");	
}
echo (!query) ? "false" : "true";
?>