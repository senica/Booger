<?php
//Should allow result_template from shortcode
//Need to paginate results
/*********************************************************************************************************************
 * [search {'template':'home', 'page_id':'6', 'tv':'content', 'in':[['page','*'],['comment','*'],['post','*']], 'children_of':[3027] }]
 * template is the template you want to use
 * tv is the template variable you want to render search results in
 * page_id is the id of a page you want to use to render results in.  If no template is specified, this is required.
 *   Otherwise it's optional and vice versa.  You don't have to specify a template if you specify a page_id
 * in is an array specifyin what type of content you want to search.  The second value in the array says what TV you
 *   want to search in. For example if a page had 3 template variables and you wanted to search in only the content tv,
 *   then you would specify 'in':[['page','content']]  An asterisk says you want to search in all tvs.
 *   You can also specify an asterisk for the type and then specify a tv if you want to search in all type, but only
 *   certain TVs.  If you don't specify 'in', then the search will take place in all indexed content.
 * children_of is an array of ids in which to search ONLY the children of the id.
 
 
 * Rendering of search index needs work.  We need to index the content and not shortcodes
 *********************************************************************************************************************/

$bg->add_hook('site-settings', 'core_search_site_settings');
$bg->add_shortcode('search', 'core_search_shortcode');
$bg->add_hook('site-foot', 'core_search_site_foot');
$bg->url_filter('search', 2, true); //1 - search phrase; 2 - page
$bg->url_redirect('search', 'core_search_redirect');

function core_search_site_foot(){
	global $bg;
	$bg->add_js(URL.'/core/search/search-site.js');
}

function core_search_shortcode($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	
	echo '<div class="core-search-form-wrapper">';
	echo '<form class="core-search-form" action="'.URL.'/search" method="post">';
	
	$input = '<input class="core-search-input" name="s" type="text" value="'.$_GET['search'][0].'" />';
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
	echo '<input type="hidden" name="o" value="'.base64_encode(serialize($opt)).'"/>';
	echo '</form></div>';	
}

function core_search_redirect($guid){
	global $bdb, $bg;
	
	$bg->add_css($bg->plugin_url(false).'/search-site.css');
	
	$result = (object) array();
	$opt = unserialize(base64_decode($_POST['o']));
	
	//Set default search template variable
	if(empty($opt->in) && !empty($bg->settings->core_search_content)){
		$opt->in = array(array('*',$bg->settings->core_search_content));	
	}
	
	$results = include($bg->plugin_url(false, true).'/search-engine.php');
	
	$temp = '<div class="core-search-wrapper">';
	foreach($results as $r){
		$temp .= '<div class="core-search-line">';
			$temp .= '<div class="core-search-title"><a href="'.URL.'/'.$r->guid.'">'.$r->title.'</a></div>';
			$temp .= '<div class="core-search-type">'.$r->resource_type.'</div>';
			$temp .= '<div class="core-search-publish">'.$r->publish_on.'</div>';
			$temp .= '<div class="core-search-score">'.$r->score.'</div>';
			$temp .= '<div class="core-search-score-meter"><meter min="1" max="100" value="'.$r->score.'"></meter></div>';
			$temp .= '<div class="core-search-content">';
			if(isset($opt->length)){ $temp .= substr(strip_tags($r->content), 0, $opt->length); }
			else{ $temp .= strip_tags($r->content); }
			$temp .= '</div>';
		$temp .= '</div>';
	}
	if(!$results){
		$temp .= '<div class="core-search-title">No results found.</div>';	
	}
	$temp .= '</div>';	
	
	//Set page template
	if(isset($opt->page_id)){
		$result = $bdb->get_result("SELECT id,content,template FROM ".PREFIX."_content WHERE id='".$opt->page_id."'");	
		if(!$result){ $result->die = "Page ID given for search does not exist."; } //Don't process rest of page.
	}else if(!empty($opt->temp)){
		$result->template = base64_decode($_GET['t']);
		//set content results here
	}else if(!empty($bg->settings->core_search_page_id)){ //Get site settings value
		$result = $bdb->get_result("SELECT id,content,template FROM ".PREFIX."_content WHERE id='".$bg->settings->core_search_page_id."'");	
		if(!$result){ $result->die = "Search Page does not exist.  Check Site Settings."; } //Don't process rest of page.
	}else{
		$result->template = 'home.tpl.php';	
	}
	
	//Set template variable
	if(!empty($opt->tv)){
		$tv = $opt->tv;	
	}else if(!empty($bg->settings->core_search_page_tv)){ //Site settings value
		$tv = $bg->settings->core_search_page_tv;	
	}else{
		$tv = 'content';	
	}
	//Replace TV with search results
	$content = unserialize($result->content);
	$content[$tv] = $temp;
	$result->content = serialize($content);
	
	return $result;
}

function core_search_site_settings(){
	global $bg, $bdb;
	$bg->add_js($bg->plugin_url(false).'/search-admin.js');
	echo '<div><span>Search Page</span><select name="core_search_page_id" size="7">';
		$page = $bdb->get_results("SELECT id,title FROM ".PREFIX."_content WHERE type='page' ORDER BY title ASC");
		foreach($page as $p){
			echo '<option value="'.$p->id.'" '.(($p->id == $bg->settings->core_search_page_id)?'selected':'').'>'.$p->title.'</option>';	
		}
	echo '</select></div>';
	echo '<div><span>Search TV</span><select name="core_search_page_tv">';
		$tv = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE id='".$bg->settings->core_search_page_id."'");
		$tv = unserialize($tv->content);
		if(is_array($tv)){
			foreach($tv as $k=>$v){
				echo '<option value="'.$k.'" '.(($k == $bg->settings->core_search_page_tv)?'selected':'').'>'.$k.'</option>';		
			}
		}
	echo '</select></div>';	
	echo '<div><span>Default Search Content</span><input type="text" name="core_search_content" value="'.$bg->settings->core_search_content.'" /></div>';
}

?>