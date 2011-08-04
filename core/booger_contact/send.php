<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	$obj = (object) $_POST['obj'];		
	if( !$bg->email('support@boogercms.org', 'The Green Team', ucwords($obj->subject), $obj->message) ) {
	  $json['err'] = 'true'; $json['message'] = 'Could not send message.  Please check your mail settings in Site Settings'; echo json_encode($json);
	} else {
	  $json['err'] = 'false'; $json['message'] = 'Your message has been sent!'; echo json_encode($json);
	}
?>