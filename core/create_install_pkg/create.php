<?php require(ASSETS.'/no_direct.php'); ?>
<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
flush();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Package Creation</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
echo '<script>var comet = window.parent.core_create_install_pkg.comet;</script>';
function send($message){ echo '<script>comet("<div>'.$message.'</div>")</script>'; flush(); usleep(1000); }
$package = trim($_POST['pkg_name']);
if(empty($package)){ $package = 'booger.pkg'; }
send('Writing Package: '.$package);
$output = SITE.'/media/uploads/'.$package;
if(!is_dir(SITE.'/media/uploads')){ send('Make sure /media/uploads/ exists and is writable.'); die(); }
$check = file_put_contents($output, ''); //Clear file
if($check === false){ send('Failed to initialize package.'); }
$clear = false;
if(!empty($_POST['clear_config'])){ $clear = true; }
foreach($_POST as $k=>$v){
	if($v == '/assets/config.php' && $clear === true){
		$content = file_get_contents(SITE.$v);
		$content = preg_replace('/(\$bg_server\s+=\s+")[^"]*(";)/s', '$1'.'$2', $content);
		$content = preg_replace('/(\$bg_user\s+=\s+")[^"]*(";)/s', '$1'.'$2', $content);
		$content = preg_replace('/(\$bg_pass\s+=\s+")[^"]*(";)/s', '$1'.'$2', $content);
		$content = preg_replace('/(\$bg_db\s+=\s+")[^"]*(";)/s', '$1'.'$2', $content);
		$content = preg_replace('/(\$bg_key\s+=\s+\')[^\']*(\';)/s', '${1}'.'1#43934%^%&'.'$2', $content);
		$content = preg_replace('/(define\(\'PREFIX\'\,\s+\')[^\']*(\'\);)/s', '$1'.'$2', $content);
		$check = file_put_contents($output, pack("H*", "0A00").pack("H*", sprintf("%04X", strlen('./'.$v))).'./'.$v.pack("H*", sprintf("%08X", strlen($content))).$content, FILE_APPEND);
	}else if($k != 'pkg_name' && $k != 'clear_config'){
		send('Adding '.$v);
		if(is_dir(SITE.$v)){
			$check = file_put_contents($output, pack("H*", "0A01").pack("H*", sprintf("%04X", strlen('./'.$v))).'./'.$v, FILE_APPEND);
		}else{
			$content = file_get_contents(SITE.$v);
			$check = file_put_contents($output, pack("H*", "0A00").pack("H*", sprintf("%04X", strlen('./'.$v))).'./'.$v.pack("H*", sprintf("%08X", filesize(SITE.$v))).$content, FILE_APPEND);
		}
		if($check === false){ send('Failed to write to package.'); die(); }
	}
}
//Add checksum
$check = file_put_contents($output, pack("H*", sprintf("%08X", filesize($output))), FILE_APPEND);
if($check === false){ send('Failed to write checksum.'); die(); }
send('Finished');
send('Package located at: /media/uploads/'.$package);
?>
</body>
</html>