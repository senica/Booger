<?php
//Should allow result_template from shortcode
//Need to paginate results
/*********************************************************************************************************************
 * [search {'template':'home', 'page_id':'6', 'tv':'content', 'in':[['page','*'],['comment','*'],['post','*']] }]
 * template is the template you want to use
 * tv is the template variable you want to render search results in
 * page_id is the id of a page you want to use to render results in.  If no template is specified, this is required.
 *   Otherwise it's optional and vice versa.  You don't have to specify a template if you specify a page_id
 * in is an array specifyin what type of content you want to search.  The second value in the array says what TV you
 *   want to search in. For example if a page had 3 template variables and you wanted to search in only the content tv,
 *   then you would specify 'in':[['page','content']]  An asterisk says you want to search in all tvs.  If you don't
 *   specify 'in', then the search will take place in all indexed content.
 *********************************************************************************************************************/
$bg->add_shortcode('search', 'core_search_shortcode');
$bg->add_hook('site-foot', 'core_search_site_foot');
$bg->url_redirect('search', 'core_search_redirect');

function core_search_site_foot(){
	global $bg;
	$bg->add_js(URL.'/core/search/search-site.js');
}

function core_search_shortcode($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	
	echo '<div class="core-search-form-wrapper">';
	echo '<form class="core-search-form" action="'.URL.'/search" method="get">';
	
	$input = '<input class="core-search-input" name="s" type="text" value="'.$_GET['s'].'" />';
	$button = '<input class="core-search-submit" type="submit" value="Search" />';
	if(isset($opt->form_template)){ //Handle template passed in
		$template = html_entity_decode($opt->form_template);	
		$template = str_replace('$input', $input, $template);
		$template = str_replace('$button', $button, $template);
		echo $template;
	}else{
		echo $input;
		echo $button;
	}
	echo '<div style="display:none;" class="core-search-default">'.((isset($opt->default))?$opt->default:'Search....').'</div>';
	echo '<input type="hidden" name="o" value="'.(base64_encode(serialize($opt))).'" />';
	echo '</form></div>';	
}

function core_search_redirect($guid){
	global $bdb;
	$result = (object) array();
	$opt = unserialize(base64_decode($_GET['o']));
	$keyword = $_GET['s'];
	
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
			$s .= "(i.resource_type = '".mysql_real_escape_string($search[0])."' ";
			$tv = explode(',', $search[1]);
			if($tv[0] != "*"){
				$s .= "AND (";
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
	$s .= " AND ct.status='published' ORDER BY score DESC"; //Make sure resource is published
	//echo $s;
	//$s result is something like: SELECT i.resource_id,i.content,ct.title,ct.guid, MATCH(i.content) AGAINST ('hello' WITH QUERY EXPANSION) as rel FROM bg_index as i LEFT JOIN bg_content as ct ON ct.id=i.resource_id WHERE MATCH(i.content) AGAINST ('hello' WITH QUERY EXPANSION) AND ((i.resource_type = 'page' AND ( i.resource_tv='content' OR i.resource_tv='core_menus' OR i.resource_tv='comment-form')) OR (i.resource_type = 'comment' )) AND ct.status='published'
	$results = $bdb->get_results($s);
	
	$temp = '<div class="core-search-wrapper">';
	foreach($results as $r){
		$temp .= '<div class="core-search-line">';
			$temp .= '<div class="core-search-title"><a href="'.URL.'/'.$r->guid.'">'.$r->title.'</a></div>';
			$temp .= '<div class="core-search-type">'.$r->resource_type.'</div>';
			$temp .= '<div class="core-search-publish">'.$r->publish_on.'</div>';
			$temp .= '<div class="core-search-score">'.$r->score.'</div>';
			$temp .= '<div class="core-search-score-meter"><meter min="1" max="100" value="'.$r->score.'"></meter></div>';
			$temp .= '<div class="core-search-content">';
			if(isset($opt->length)){ $temp .= substr($r->content, 0, $opt->length); }
			else{ $temp .= $r->content; }
			$temp .= '</div>';
		$temp .= '</div>';
	}
	if(!$results){
		$temp .= '<div class="core-search-title">No results found.</div>';	
	}
	$temp .= '</div>';	
	
	//Set page template stuff
	if(isset($opt->page_id)){
		$result = $bdb->get_result("SELECT id,content,template FROM ".PREFIX."_content WHERE id='".$opt->page_id."'");	
		if(!$result){ $result->die = "Page ID given for search does not exist."; } //Don't process rest of page.
	}else if(isset($opt->temp)){
		$result->template = base64_decode($_GET['t']);
		//set content results here
	}else{
		$result->template = 'home.tpl.php';	
	}
	
	//Replace TV with search results
	$content = unserialize($result->content);
	$tv = (isset($opt->tv))?$opt->tv:'content';
	$content[$tv] = $temp;
	$result->content = serialize($content);
	
	return $result;
}
?>