<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$offset = $_POST['offset'];
$limit = $_POST['limit'];
$type = $_POST['type'];
if($type == 'comments'){
	$results = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_type!='spam' ORDER BY comment_id DESC LIMIT $offset,$limit");
}else if($type == 'approved'){
	$results = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_type!='spam' AND comment_approved='1' ORDER BY comment_id DESC LIMIT $offset,$limit");
}else if($type == 'pending'){
	$results = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_type!='spam' AND comment_approved='0' ORDER BY comment_id DESC LIMIT $offset,$limit");
}else if($type == 'spam'){
	$results = $bdb->get_results("SELECT * FROM ".PREFIX."_comments WHERE comment_type='spam' ORDER BY comment_id DESC LIMIT $offset,$limit");
}

foreach($results as $result){
	echo '<div class="comment-wrapper" dbid="'.$result->comment_id.'" style="position:relative; border-bottom:1px solid #ccc; padding-bottom:10px; margin-bottom:10px;">';
		echo '<div class="status">';
			if($result->comment_type == "comment" && $result->comment_approved == "1"){ echo '<span class="approved">Approved</span>'; }else{ echo '<a href="#" class="approved">Approve</a>'; }
			if($result->comment_type == "comment" && $result->comment_approved == "0"){ echo '<span class="pending">Pending</span>'; }else{ echo '<a href="#" class="pending">Make Pending</a>'; }
			if($result->comment_type == "spam"){ echo '<a href="#" class="notspam">Not Spam</a>'; }else{ echo '<a href="#" class="spam">Spam</a>'; }
		echo '</div>';
		echo '<div class="date" style="font-size:small">'.$result->comment_date_gmt.'</div>';
		echo '<div class="author"><strong>'.htmlentities($result->comment_author).'</strong></div>';
		echo '<div class="email" style="font-size:small">'.htmlentities($result->comment_author_email).'</div>';
		echo '<div class="website" style="font-size:small"><a href="'.htmlentities($result->comment_author_url).'" target="_blank">'.htmlentities($result->comment_author_url).'</a></div>';
		echo '<div class="ip" style="font-size:small; margin-bottom:7px;">'.$result->comment_author_ip.'</div>';
		echo '<div class="content">'.htmlentities($result->comment_content).'</div>';
	echo '</div>';	
}
?>