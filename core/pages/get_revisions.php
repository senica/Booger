<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$dbid = $_POST['id'];
$results = $bdb->get_results("SELECT id,UNIX_TIMESTAMP(modified) as modified FROM ".PREFIX."_content WHERE (parent_id='".mysql_real_escape_string($dbid)."' AND type='revision') OR id='".mysql_real_escape_string($dbid)."' ORDER BY modified DESC ");		
foreach($results as $r){
	echo '<div>'.date("F d, Y",$r->modified).' @ '.date("H:i:s",$r->modified).' ';
	if($dbid == $r->id){
		echo 'current';	
	}else{
		echo '<a href="#" class="restore" current="'.$dbid.'" dbid="'.$r->id.'">restore</a>';
	}
	echo '</div>';	
}

?>