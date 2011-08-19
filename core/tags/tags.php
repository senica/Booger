<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-storage', 'core_tags_dialog');
$bg->add_hook('admin-foot', 'core_tags_js');
$bg->add_hook('admin-sidebar', 'core_tags_sidebar');
$bg->add_hook('admin-head', 'core_tags_head');
$bg->add_hook('site-settings', 'core_tags_settings');

$bg->add_shortcode('tag-list', 'core_tags_shortcode_tag_list');
$bg->add_shortcode('tag-prev', 'core_tags_shortcode_tag_prev');
$bg->add_shortcode('tag-next', 'core_tags_shortcode_tag_next');

//Make a textarea in the Site Settings to put the default Tag Content
function core_tags_settings(){
	global $bg;
	echo '<div><span>Tag List Content</span><textarea name="site_tag_list">'.$bg->settings->site_tag_list.'</textarea></div>';	
}

function core_tags_shortcode_tag_prev($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	$html = '';
	$id = $bg->page_id;
	$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='tag' AND id < '".$id."' ORDER BY id DESC LIMIT 1");
	if(!$r){ //If not more categories, go back to the last one
		$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='tag' ORDER BY id DESC LIMIT 1");
	}
	if(isset($opt->template)){ //Handle template passed in
		$r = (array) $r;
		$template = html_entity_decode($opt->template);	
		foreach($r as $k=>$v){
			$template = str_replace('$'.$k, $r[$k], $template);	
		}
		$html .= $template;
	}else{
		$html .= '<a href="'.URL.'/'.$r->guid.'" class="tag-previous '.$opt->class.'">&lt;&lt; '.$r->title.'</a>';	
	}
	echo $html;
}

function core_tags_shortcode_tag_next($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	$html = '';
	$id = $bg->page_id;
	$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='tag' AND id > '".$id."' ORDER BY id ASC LIMIT 1");
	if(!$r){ //If not more categories, go back to the first one
		$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='tag' ORDER BY id ASC LIMIT 1");
	}
	if(isset($opt->template)){ //Handle template passed in
		$r = (array) $r;
		$template = html_entity_decode($opt->template);	
		foreach($r as $k=>$v){
			$template = str_replace('$'.$k, $r[$k], $template);	
		}
		$html .= $template;
	}else{
		$html .= '<a href="'.URL.'/'.$r->guid.'" class="tag-next '.$opt->class.'">'.$r->title.' &gt;&gt;</a>';	
	}
	echo $html;
}

function core_tags_shortcode_tag_list($obj){
	global $bdb;
	$opt = $obj->options;
	$results = $bdb->get_results("SELECT title,guid FROM ".PREFIX."_content WHERE type='tag' ".((isset($opt->count))?'LIMIT 0,'.$opt->count:'') );
	foreach($results as $r){
		if(isset($opt->class)){ echo '<div class="'.$opt->class.'">'; }
		if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.URL.'/'.$r->guid.'">'; }
		echo $r->title;
		if(isset($opt->link) && $opt->link == "true"){ echo '</a>'; }
		if(isset($opt->class)){ echo '</div>'; }
	}
}

function core_tags_dialog(){ ?>	
	<div id="core-tags-dialog-wrapper">
		<form id="core-tags-form">
		<div><span style="width:150px; display:inline-block;">Tag Name</span> <input class="core-tags-name" type="text" /></div>
		<div style="font-size:small; margin-bottom:10px;">Permalink: <span><?php echo URL; ?>/</span><span class="core-tags-permalink" style="cursor:pointer;"></span></div>
		<div><span style="width:150px; display:inline-block;">Description</span></div>
		<div><textarea class="core-tags-description" style="width:350px"></textarea></div>
		<div style="text-align:right; margin-top:10px;"><input class="core-tags-submit" type="submit" class="button" value="Add" /></div>
		</form>
	</div>
<?php }

function core_tags_sidebar(){
	echo '<h3><a href="#">Tags</a></h3>';
	echo '<div class="content core-tags-sidebar-content">';
		echo '<div class="core-tags-new-tag"><img src="'.URL.'/core/tags/images/new.png" />New tag</div>';
		echo '<div id="core-tags-sidebar-list"></div>';
	echo '</div>';
}

function core_tags_js(){
	global $bg;
	$bg->add_js(URL.'/core/tags/tags.js');
}

function core_tags_head(){
	global $bg;
	$bg->add_css(URL.'/core/tags/tags.css');
}

?>