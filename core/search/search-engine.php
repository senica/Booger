<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$keyword = (empty($_GET['search'][0])) ? false : $_GET['search'][0];
$page = (empty($_GET['search'][1])) ? 1 : $_GET['search'][1];
$limit = (empty($_GET['search'][2])) ? 10 : $_GET['search'][2];

//Build Search Query
$s = "SELECT i.resource_id,i.content,i.resource_type,ct.title,ct.guid,ct.publish_on, ";
$s .= "ROUND(MATCH(i.content) AGAINST ('".mysql_real_escape_string($keyword)."' "; //Get relevance
	if(strlen($keyword) < 15){ $s .= "WITH QUERY EXPANSION"; }							   
$s .= ")) as score ";
$s .= "FROM ".PREFIX."_index as i LEFT JOIN ".PREFIX."_content as ct ON ct.id=i.resource_id ";
$s .= " WHERE ";
$s .= "MATCH(i.content) AGAINST ('".mysql_real_escape_string($keyword)."' "; //Actual search
	if(strlen($keyword) < 15){ $s .= "WITH QUERY EXPANSION"; }							   
$s .= ") ";

if(isset($opt->in)){
	$s .= "AND ";
	$s .= "(";
	foreach($opt->in as $search){ //search type (page,post,comment,etc)
		$s .= "( ";
		$s .= ($search[0] != "*") ? "i.resource_type = '".mysql_real_escape_string($search[0])."' " : "";
		$tv = explode(',', $search[1]);
		if($tv[0] != "*"){
			$s .= ($search[0] != "*") ? "AND " : "";
			$s .= " (";
			foreach($tv as $t){ //what template variables to use
				$s .= " i.resource_tv='".mysql_real_escape_string($t)."' OR ";	
			}
			$s = substr($s, 0, strlen($s)-4);
			$s .= ")";
		}
		$s .= ") OR ";
	}
	$s = substr($s, 0, strlen($s)-4);
	$s .= ")";
}

if(isset($opt->children_of)){
	//Build list of ids
	$s .= " AND ct.id IN ( ";
	foreach($opt->children_of as $cld){
		$id_list = $bg->get_children_of($cld);
		foreach($id_list as $single_id){
			$s .= $single_id->id.',';	
		}
	}
	$s = substr($s, 0, strlen($s)-1);
	$s .= " )";
}

$s .= " AND ct.status='published' AND NOW() > ct.publish_on GROUP BY i.resource_id ORDER BY score DESC LIMIT ".(($page-1)*$limit).",".$limit; //Make sure resource is published
//echo $s;
//$s result is something like: SELECT i.resource_id,i.content,ct.title,ct.guid, MATCH(i.content) AGAINST ('hello' WITH QUERY EXPANSION) as rel FROM bg_index as i LEFT JOIN bg_content as ct ON ct.id=i.resource_id WHERE MATCH(i.content) AGAINST ('hello' WITH QUERY EXPANSION) AND ((i.resource_type = 'page' AND ( i.resource_tv='content' OR i.resource_tv='core_menus' OR i.resource_tv='comment-form')) OR (i.resource_type = 'comment' )) AND ct.status='published'
//return $bdb->get_results($s);
return $bdb->get_results($s);
?>