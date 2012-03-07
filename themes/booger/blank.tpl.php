<?php $bg->get_header(); ?>

<?php require(ASSETS.'/no_direct.php'); ?>
<?php

require(SITE."/core/github/github.class.php");

$github = new Github('senica', 'Booger', 'master');

$update_tags = end($github->tags());
$update_tag = $github->tag($update_tags->object->sha);
$update_commit = $github->commit($update_tag->object->sha);
$update_tree = $github->trees($update_commit->tree->sha, true);
$update_temp = $update_tree->tree;

$current = $github->tags('0.01');
$current_tag = $github->tag($current->object->sha);
$current_commit = $github->commit($current_tag->object->sha);
$current_tree = $github->trees($current_commit->tree->sha, true);
$current_temp = $current_tree->tree;

$additions = array();
$updates = array();
$deletions = array();

echo microtime().'<br>';
foreach($update_tree->tree as $ut){
	$present = false;
	$same = true;
	foreach($current_temp as $key => $ct){
		if($ct->path == $ut->path){
			$present = true;
			if($ct->sha != $ut->sha){
				$same = false;	
			}
			unset($current_temp[$key]); //Shorten array on a match
			break;
		}
	}
	if($present === false){
		array_push($additions, $ut);	
	}
	if($same === false){
		array_push($updates, $ut);	
	}
}

foreach($current_tree->tree as $ct){
	$present = false;
	foreach($update_temp as $key => $ut){
		if($ut->path == $ct->path){
			$present = true;
			unset($update_temp[$key]); //Shorten array on a match
			break;
		}
	}
	if($present === false){
		array_push($deletions, $ct);	
	}
}
echo microtime();

echo '<textarea>';
var_dump($deletions);
echo '</textarea>';
echo '<textarea>';
var_dump($additions);
echo '</textarea>';
echo '<textarea>';
var_dump($updates);
echo '</textarea>';
//var_dump($additions);
//var_dump($updates);
//echo $current;
//if(floatval($bg->settings->version) < floatval($current)){
	//echo 'yes there is an update';	
//}
//var_dump($update->tags());
echo '</textarea>';
?>

<?php $bg->get_footer(); ?>