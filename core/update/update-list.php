<?php require(ASSETS.'/no_direct.php'); ?>
<?php
require(SITE."/core/github/github.class.php");
$github = new Github('senica', 'Booger', 'master'); //user, Repository, Branch

$update_tags = end($github->tags()); //Get most recent version object from tags list
$update_tag = $github->tag($update_tags->object->sha); //Get tag object
$update_commit = $github->commit($update_tag->object->sha); //Get corresponding commit from tag
$update_tree = $github->trees($update_commit->tree->sha, true); //Get commit tree
$update_temp = $update_tree->tree;

$current = $github->tags(number_format(floatval($bg->settings->version), 2));
$current_tag = $github->tag($current->object->sha);
$current_commit = $github->commit($current_tag->object->sha);
$current_tree = $github->trees($current_commit->tree->sha, true);
$current_temp = $current_tree->tree;

$additions = array();
$updates = array();
$deletions = array();

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

echo '<div>Your current version is: '.$bg->settings->version.'</div>';
echo '<div>The latest version available is: '.$update_tag->tag.'</div>';

if(floatval($bg->settings->version) < floatval($update_tag->tag)){
	echo '<div class="button update" style="position:absolute; top:15px; right:15px;">Update Now</div>';
	echo '<div style="margin-top:20px;">There is an update available.  Remember to backup your files and database before updating.</div>';	
	echo '<div style="margin-top:20px">Release Notes</div><div style="margin-bottom:20px;"><pre>'.$update_commit->message.'</pre></div>';
	echo '<div class="accordion">';
	echo '<h3 data-action="delete">Files that will be deleted: '.count($deletions).'</h3><div class="wrapper">';
		foreach($deletions as $o){
			if($o->type == "blob"){
				echo '<div class="files" data-action="delete" data-sha="'.$o->sha.'" data-type="'.$o->type.'"><pre>'.$o->path.'</pre></div>';	
			}
		}
	echo '</div>';
	echo '<h3 data-action="add">Files that will be added: '.count($additions).'</h3><div class="wrapper">';
		foreach($additions as $o){
			echo '<div class="files" data-action="add" data-sha="'.$o->sha.'" data-type="'.$o->type.'"><pre>'.$o->path.'</pre></div>';	
		}
	echo '</div>';
	echo '<h3 data-action="update">Files that will be updated/overwritten: '.count($updates).'</h3><div class="wrapper">';
		foreach($updates as $o){
			if($o->type == "blob"){
				echo '<div class="files" data-action="update" data-sha="'.$o->sha.'" data-type="'.$o->type.'"><pre>'.$o->path.'</pre></div>';	
			}
		}
	echo '</div>';
	echo '</div>';
}else{
	echo '<div>No update available.</div>';
}
echo '<div class="iframe"></div><div class="status"></div>';

?>