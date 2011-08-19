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
echo '<script>window.parent.core_update.status("Getting Manifest...");</script>';
flush(); ob_flush(); usleep(10000);

$fp = fsockopen($bg->settings->update_server, 80, $errno, $errstr, 5);
if (!$fp) {
	echo '<script>window.parent.core_update.status("Failed.<br />Could not connect to your update server.");</script>';
	flush(); ob_flush(); usleep(10000); die();
}

// We first get the manifest again to find out what file we need
// Don't grab from html as html injection would be simple
$out = "GET /core/update/update.manifest HTTP/1.1\r\n";
$out .= "Host: ".$bg->settings->update_server."\r\n";
$out .= "Connection: Close\r\n\r\n";
fwrite($fp, $out);
$data = '';
while (!feof($fp)) {
	$data .= fgets($fp, 128);
}
fclose($fp);

echo '<script>window.parent.core_update.status("Done.<br />Parsing...");</script>';
flush(); ob_flush(); usleep(10000);

if(!preg_match('/^#package(.*?)$/im', $data, $match)){
	echo '<script>window.parent.core_update.status("Failed.<br />No package is specified.");</script>';
	flush(); ob_flush(); usleep(10000); die();	
}
$package = trim($match[1]);

if(!preg_match('/^#version(.*?)$/im', $data, $match)){
	echo '<script>window.parent.core_update.status("Failed.<br />No version specified.");</script>';
	flush(); ob_flush(); usleep(10000); die();	
}
$version = trim($match[1]);

echo '<script>window.parent.core_update.status("Done.<br />Fetching '.$package.'...");</script>';
flush(); ob_flush(); usleep(10000);

if(file_exists(dirname(__FILE__).'/'.$package)){
	echo '<script>window.parent.core_update.status("Already exists.<br />Removing...");</script>';
	flush(); ob_flush(); usleep(10000);
	
	if(!unlink(dirname(__FILE__).'/'.$package)){
		echo '<script>window.parent.core_update.status("Failed.<br />Could not remove existing package.  Please delete manually.");</script>';
		flush(); ob_flush(); usleep(10000);	 die();
	}else{
		echo '<script>window.parent.core_update.status("Done.<br />Fetching '.$package.'...");</script>';
		flush(); ob_flush(); usleep(10000);	
	}
}

//Download package
$fp = fsockopen($bg->settings->update_server, 80, $errno, $errstr, 5);
if (!$fp) {
	echo '<script>window.parent.core_update.status("Failed.<br />Could not connect download location.");</script>';
	flush(); ob_flush(); usleep(10000); die();
}

$out = "GET /".$package." HTTP/1.1\r\n";
$out .= "Host: ".$bg->settings->update_server."\r\n";
$out .= "Connection: Close\r\n\r\n";
fwrite($fp, $out);
$bytes = 0;
$chunk = 4096;
$header = true;
while (($buffer = fgets($fp, $chunk)) !== false) {
	if($header === true && $buffer == "\r\n"){ $header = false; } //discard headers
	else if($header === false){
		if(file_put_contents(dirname(__FILE__).'/'.$package, $buffer, FILE_APPEND) === false){
			echo '<script>window.parent.core_update.status("Failed.<br />Error saving package.");</script>';
			flush(); ob_flush(); usleep(10000);	die();	
		}
		$bytes = $bytes+$chunk;
		echo '<script>window.parent.core_update.status("<br />'.$bytes.' bytes transferred.");</script>';
		flush(); ob_flush(); usleep(10000);
	}
}

//Verify entire package
if (!feof($fp)) {
	//Cannot verify end of file
	echo '<script>window.parent.core_update.status("Failed.<br />Could not download entire package.");</script>';
	flush(); ob_flush(); usleep(10000); die();
}
fclose($fp);

//Completed transferring, extract
echo '<script>window.parent.core_update.status("<br />Done.<br />Extracting...");</script>';
flush(); ob_flush(); usleep(10000);

require_once(SITE.'/core/tar/tar.class.php');
$tar = new tar();
if(!$tar->openTar(dirname(__FILE__).'/'.$package)){
	echo '<script>window.parent.core_update.status("Failed.<br />Could not open package.");</script>';
	flush(); ob_flush(); usleep(10000); die();
}

//Create or overwrite files
if($tar->numFiles > 0) {
	
	//Run Pre-Processor
	if($tar->containsFile("pre.php")){
		echo '<script>window.parent.core_update.status("<br />Running pre-processor...");</script>';
		flush(); ob_flush(); usleep(10000);	
		$file = $tar->getFile("pre.php");
		if( eval(' ?> '.$file['file'].' <?php ') !== true){
			echo '<script>window.parent.core_update.status("Failed.");</script>';
			flush(); ob_flush(); usleep(10000); die();	
		}
		echo '<script>window.parent.core_update.status("Done.");</script>';
		flush(); ob_flush(); usleep(10000);
	}
	
	foreach($tar->files as $id => $file) {
		
		//Create directories
		$dir = dirname(SITE.'/'.$file['name']);
		$dir_array = array();
		while( !is_dir($dir) ){
			array_push($dir_array, $dir);
			$dir = dirname($dir);
		}
		foreach(array_reverse($dir_array) as $dir){
			echo '<script>window.parent.core_update.status("<br />Creating directory '.$dir.'...");</script>';
			flush(); ob_flush(); usleep(10000);	
			if( !mkdir($dir, 0755) ){
				echo '<script>window.parent.core_update.status("Failed.");</script>';
				flush(); ob_flush(); usleep(10000); die();		
			}
			echo '<script>window.parent.core_update.status("Done.");</script>';
			flush(); ob_flush(); usleep(10000);	
		}
		
		//Create or overwrite files
		if($file['name'] != 'pre.php' && $file['name'] != 'post.php'){ //Don't extract post and pre processors
			if(file_put_contents(SITE.'/'.$file['name'], $file['file']) === false){
				echo '<script>window.parent.core_update.status("<br />Failed extracting '.$file['name'].'");</script>';
				flush(); ob_flush(); usleep(10000);	die();
			}
			echo '<script>window.parent.core_update.status("<br />Writing file '.$file['name'].'");</script>';
			flush(); ob_flush(); usleep(10000);
		}
	}
	
	//Run Post
	if($tar->containsFile("post.php")){
		echo '<script>window.parent.core_update.status("<br />Cleaning up...");</script>';
		flush(); ob_flush(); usleep(10000);	
		$file = $tar->getFile("post.php");
		if( eval(' ?> '.$file['file'].' <?php ') !== true){
			echo '<script>window.parent.core_update.status("Failed.");</script>';
			flush(); ob_flush(); usleep(10000); die();	
		}
		echo '<script>window.parent.core_update.status("Done.");</script>';
		flush(); ob_flush(); usleep(10000);
	}
}

//If we are here, the script hasn't died, so we should be good to notify of successful update
echo '<script>window.parent.core_update.status("<br />Update was successful!");</script>';
flush(); ob_flush(); usleep(10000);

//Verify update by running check.php
echo '<script>window.parent.core_update.done();</script>';
flush(); ob_flush(); usleep(10000);
?>

</body>
</html>