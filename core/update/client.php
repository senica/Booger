<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//pre.php and post.php MUST return true as the last line before the PHP end tag

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
flush();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Booger Update</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
function msg($msg, $die=false){ echo '<script>window.parent.core_update.status("<div>'.$msg.'</div>");</script>'; flush(); ob_flush(); usleep(10000); if($die === true){ die('<script>window.parent.core_update.status("<div>Update Failed: '.$msg.'</div>");</script>'); } }
function action($action){ echo '<script>window.parent.core_update.action("'.$action.'");</script>'; flush(); ob_flush(); usleep(10000); }
function cross($sha){ echo '<script>window.parent.core_update.cross("'.$sha.'");</script>'; flush(); ob_flush(); usleep(10000); }

function is_empty($file){ //Recursively remove empty directories
	msg("Checking if ".dirname($o->path)." is empty");
	if ( ($files = @scandir(dirname($file))) && (count($files) <= 2) ){  
		msg("Deleting ".dirname($file));
		rmdir(dirname($file));
		is_empty(dirname(dirname($file)));
		return true;
	}
	return false;
}

msg("Compiling Update Manifest");

if(!file_exists(SITE."/core/github/github.class.php")){ msg("Update requires Github Gear", true); }
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

msg("Manifest Received");

msg("Processing Deletions");
action("delete");
foreach($deletions as $o){
	if($o->type == "blob"){ //Only process files
		if(file_exists(SITE.'/'.$o->path)){
			msg("Deleting ".$o->path);
			$test = unlink(SITE.'/'.$o->path);
			if($test !== true){
				msg("Could not delete ".$o->path." Skipping.");	
			}
		}else{
			msg($o->path.' does not exist');
		}
		is_empty(SITE.'/'.$o->path); //Delete empty directories
	}else{
		msg("Skipping ".$o->path);	
	}
	cross($o->sha);
}

msg("Processing Additions");
action("add");
foreach($additions as $o){
	if($o->type == "blob"){ //Only process files
		$c = $github->blob($o->sha);
		if(!empty($c->content)){
			msg("Adding file ".$o->path);
			if($c->encoding == "base64"){
				$content = base64_decode($c->content);	
			}else{
				$content = $c->content;	
			}
			$test = file_put_contents(SITE.'/'.$o->path, $content);
			if($test === false){
				msg("Failed to write file ".$o->path, true); //Die on failed update
			}else{
				msg("Created file ".$o->path);
			}
		}else{
			msg("Skipping file ".$o->path);
		}
	}else{
		msg("Skipping ".$o->path);	
	}
	cross($o->sha);
}

msg("Processing Updates");
action("update");
foreach($updates as $o){
	if($o->type == "blob"){ //Only process files
		$c = $github->blob($o->sha);
		if(!empty($c->content)){
			msg("Updating file ".$o->path);
			if($c->encoding == "base64"){
				$content = base64_decode($c->content);	
			}else{
				$content = $c->content;	
			}
			$test = file_put_contents(SITE.'/'.$o->path, $content);
			if($test === false){
				msg("Failed to update file ".$o->path, true); //Die on failed update
			}else{
				msg("Updated file ".$o->path);
			}
		}else{
			msg("Skipping file ".$o->path);
		}
	}else{
		msg("Skipping ".$o->path);	
	}
	cross($o->sha);
}
msg("Updating version in database");
$bg->setting("version", $update_tag->tag); //Update version
msg("Update Successful");
?>

</body>
</html>