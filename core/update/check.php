<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//Get manifest from update_server
$fp = fsockopen($bg->settings->update_server, 80, $errno, $errstr, 5);
if (!$fp) {
	$json['err'] = "true";
	$json['message'] = $errstr;
    echo json_encode($json);
} else {
    $out = "GET /core/update/update.manifest HTTP/1.1\r\n";
    $out .= "Host: ".$bg->settings->update_server."\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
	$data = '';
    while (!feof($fp)) {
        $data .= fgets($fp, 128);
    }
    fclose($fp);
	//preg_match('/^content-length.*?:(.*?)$/mi', $data, $match);
	//$len = trim($match[1]);
	//$data = substr($data, $len*-1); //remove headers
	$data = explode("\n", $data);
	$lastmatch = false;
	$notes = '';
	$version = '';
	$files = '';
	foreach($data as $line){
		if(preg_match('/^#version(.*?)$/i', $line, $match)){  $version = trim($match[1]); $lastmatch = 'version'; }	
		else if(preg_match('/^#notes\s*?$/i', $line, $match)){  $lastmatch = 'notes'; }
		else if(preg_match('/^#files\s*?$/i', $line, $match)){  $lastmatch = 'files'; }
		else if(preg_match('/^#package(.*?)$/i', $line, $match)){  $package = trim($match[1]); $lastmatch = 'package'; }
		else if($lastmatch == 'notes'){ $notes .= $line; }
		else if($lastmatch == 'files'){ $files .= trim($line)."\n"; }
	}

	$html = '<div>Your current version is: '.$bg->settings->version.'</div>';
	$html .= '<div>Update version is: '.$version.'</div>';
	if($version > $bg->settings->version){
		$html .= '<div style="margin-top:20px;">There is an update available.  Remember to backup your files and database before updating.</div>';	
		$html .= '<div style="margin-top:20px;"><button class="button update">Update</button> or manually download <a href="http://'.$bg->settings->update_server.'/'.$package.'">'.basename($package).'</a></div>';
		$html .= '<div><pre class="status"></pre></div>';
		$html .= '<br /><div>Release Notes</div><div><pre>'.$notes.'</pre></div>';
		$html .= '<div>Affected Files</div><div class="files"><pre>'.$files.'</pre></div>';
	}else{
		$html .= '<div>No update is necessary.</div>';
		$html .= '<div><pre class="status"></pre></div>';
	}
	
	$json['err'] = 'false';
	$json['message'] = $html;
}
echo $json['message'];
?>