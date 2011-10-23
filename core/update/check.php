<?php require(ASSETS.'/no_direct.php'); ?>
<?php
require(SITE."/core/github/github.class.php");
$github = new Github('senica', 'Booger', 'master');
$update_tags = end($github->tags());
$update_version = $github->tag($update_tags->object->sha)->tag;

$json = array();

if(floatval($bg->settings->version) < floatval($update_version)){
	$json['update'] = true;	
}else{
	$json['update'] = false;	
}

echo json_encode($json);
?>