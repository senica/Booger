<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$replies = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_post_id = '".mysql_real_escape_string($_POST['id'])."' ORDER BY comment_id DESC LIMIT 1");
$content = file_get_contents( $_POST['template'] );
foreach($replies as $reply){
	$reply->class = $_POST['class']; //Replace $class
	echo $bg->template($content, $reply); 
}
?>