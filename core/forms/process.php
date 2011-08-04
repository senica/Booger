<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$form = $_POST['obj'];
$obj = $_POST['post'];

//var_dump($form);
$json = array();
$json['error'] = false;

if(isset($form['action']['db'])){
	//If table does not exist, try to create the table
	if( !$bdb->table_exists(PREFIX."_".$form['action']['db']) ){
		$result = $bdb->query("CREATE TABLE ".PREFIX."_".$form['action']['db']." ( id bigint(22) primary key auto_increment, form_data longtext, time_stamp timestamp default current_timestamp)");
		if(!$result){ $json['error'] = true; $json['msg'] = 'Could not create table'; } //Could not create a table
	}
	//Insert data into database
	$result = $bdb->query("INSERT INTO ".PREFIX."_".$form['action']['db']." (form_data)VALUES('".serialize($obj)."')");
	if(!$result){ $json['error'] = true; $json['msg'] = 'Could not insert data into database'; } //Could not insert
}

$text = '';
foreach($obj as $k=>$v){
	$text .= $k.' - '.$obj[$k]['value'].'<br>';
}

//Email address set in action method
if(isset($form['action']['email'])){
	$email = explode(',', $form['action']['email']);
	foreach($email as $e){
		if(!$bg->email(trim($e), '', $form[0]['ref'], $text)){
			$json['error'] = true; $json['msg'] = 'Could not email';	
		}
	}
}

//If no action is set, then assume the admin email address is to be notified
if(!isset($form['action'])){
	$email = $bg->settings->admin_email;
	$test = $bg->email($email, '', $form[0]['ref'], $text); 
	if($test === false){
		$json['error'] = true; $json['msg'] = 'Could not email';	
	}
}

echo json_encode($json);
?>