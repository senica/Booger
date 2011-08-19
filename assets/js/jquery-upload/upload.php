<?php
$content = file_get_contents('php://input');

$file = $_GET['file'];
$sequence = $_GET['sequence'];
$chunk = $_GET['chunk'];
$count = $_GET['count'];
$save = $_GET['save'];
$size = $_GET['size'];
$type = $_GET['type'];

function check($p, $f){
	global $file;
	if(file_exists($p.'/'.$f)){
		$g = explode('.', $f);
		$c = $g[(count($g)-2)];
		$c = explode('-', $c);
		$t = $c[(count($c)-1)];
		if(is_numeric($t)){ $t=$t+1; $c[(count($c)-1)] = $t; $c = implode('-', $c); }
		else{ $c = implode('-', $c); $c = $c.'-1'; }
		$g[(count($g)-2)] = $c;
		$g = implode('.', $g);
		check($p, $g);
	}else{
		$file = $f;		
	}
}
//Check filename
if($sequence == 1 && file_exists($save.'/'.$file)){
	check($save, $file);		
}

$data['file'] = $file;
$data['sequence'] = $sequence;
$data['total_chunks'] = $count;
$data['save'] = $save;
$data['size'] = $size;
$data['chunk_size'] = strlen($content);
$data['type'] = $type;

$handle = file_put_contents($save.'/'.$file, $content, FILE_APPEND); 
if($handle === false){ $data['error']='nowrite'; $data['message']='Could not write to file'; echo json_encode($data); return false; }
else{ $data['current_size'] = filesize($save.'/'.$file); $data['error']='noerror'; $data['message']='Wrote to file.'; echo json_encode($data); }

?>