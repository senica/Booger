<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-storage', 'core_categories_dialog');
$bg->add_hook('admin-foot', 'core_categories_js');
$bg->add_hook('admin-sidebar', 'core_categories_sidebar');
$bg->add_hook('admin-head', 'core_categories_head');
$bg->add_hook('site-settings', 'core_categories_settings');

$bg->add_shortcode('category-list', 'core_categories_shortcode_category_list');
$bg->add_shortcode('category-prev', 'core_pages_shortcode_category_prev');
$bg->add_shortcode('category-next', 'core_pages_shortcode_category_next');

//Make a textarea in the Site Settings to put the default Category Content
function core_categories_settings(){
	global $bg;
	echo '<div><span>Default Category Content</span><textarea name="site_category_content">'.$bg->settings->site_category_content.'</textarea></div>';	
}

function core_pages_shortcode_category_prev($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	$html = '';
	$id = $bg->page_id;
	$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='category' AND id < '".$id."' ORDER BY id DESC LIMIT 1");
	if(!$r){ //If not more categories, go back to the last one
		$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='category' ORDER BY id DESC LIMIT 1");
	}
	if(isset($opt->template)){ //Handle template passed in
		$r = (array) $r;
		$template = html_entity_decode($opt->template);	
		foreach($r as $k=>$v){
			$template = str_replace('$'.$k, $r[$k], $template);	
		}
		$html .= $template;
	}else{
		$html .= '<a href="'.URL.'/'.$r->guid.'" class="category-previous '.$opt->class.'">&lt;&lt; '.$r->title.'</a>';	
	}
	echo $html;
}

function core_pages_shortcode_category_next($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	$html = '';
	$id = $bg->page_id;
	$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='category' AND id > '".$id."' ORDER BY id ASC LIMIT 1");
	if(!$r){ //If not more categories, go back to the first one
		$r = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE type='category' ORDER BY id ASC LIMIT 1");
	}
	if(isset($opt->template)){ //Handle template passed in
		$r = (array) $r;
		$template = html_entity_decode($opt->template);	
		foreach($r as $k=>$v){
			$template = str_replace('$'.$k, $r[$k], $template);	
		}
		$html .= $template;
	}else{
		$html .= '<a href="'.URL.'/'.$r->guid.'" class="category-next '.$opt->class.'">'.$r->title.' &gt;&gt;</a>';	
	}
	echo $html;
}

function core_categories_shortcode_category_list($obj){
	global $bdb;
	$opt = $obj->options;
	$results = $bdb->get_results("SELECT title,guid FROM ".PREFIX."_content WHERE type='category' ".((isset($opt->count))?'LIMIT 0,'.$opt->count:'') );
	foreach($results as $r){
		if(isset($opt->class)){ echo '<div class="'.$opt->class.'">'; }
		if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.URL.'/'.$r->guid.'">'; }
		echo $r->title;
		if(isset($opt->link) && $opt->link == "true"){ echo '</a>'; }
		if(isset($opt->class)){ echo '</div>'; }
	}
}

function core_categories_dialog(){ ?>	
	<div id="core-categories-dialog-wrapper">
		<form id="core-categories-form">
		<div><span style="width:150px; display:inline-block;">Category Name</span> <input class="core-categories-name" type="text" /></div>
		<div style="font-size:small; margin-bottom:10px;">Permalink: <span><?php echo URL; ?>/</span><span class="core-categories-permalink" style="cursor:pointer;"></span></div>
		<div style="margin-bottom:10px;"><span style="width:150px; display:inline-block;">Parent</span> <select class="core-categories-parent"></select></div>
		<div><span style="width:150px; display:inline-block;">Template</span> <select class="core-categories-template"></select></div>
		<div><span style="width:150px; display:inline-block;">Description</span></div>
		<div><textarea class="core-categories-description" style="width:350px"></textarea></div>
		<div style="text-align:right; margin-top:10px;"><input class="core-categories-submit" type="submit" class="button" value="Add" /></div>
		</form>
	</div>
<?php }

function core_categories_sidebar(){
	echo '<h3><a href="#">Categories</a></h3>';
	echo '<div class="content core-categories-sidebar-content">';
		echo '<div class="core-categories-new-category"><img src="'.URL.'/core/categories/images/new.png" />New Category</div>';
		echo '<div id="core-categories-sidebar-list"></div>';
	echo '</div>';
}

function core_categories_js(){
	global $bg;
	$bg->add_js(URL.'/core/categories/categories.js');
}

function core_categories_head(){
	global $bg;
	$bg->add_css(URL.'/core/categories/categories.css');
}

?>