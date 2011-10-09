<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_pages_css');
$bg->add_hook('admin-sidebar', 'core_pages_sidebar');
$bg->add_hook('admin-foot', 'core_pages_js');
$bg->add_hook('admin-storage', 'core_pages_new_page_dialog');

$bg->add_shortcode('page-title', 'core_pages_shortcode_page_title');
$bg->add_shortcode('page-content', 'core_pages_shortcode_page_content');
$bg->add_shortcode('page-thumb', 'core_pages_shortcode_page_thumb');
$bg->add_shortcode('page-list', 'core_pages_shortcode_page_list');
$bg->add_shortcode('page-id', 'core_pages_shortcode_page_id');

function core_pages_shortcode_page_id($obj){ global $bg; echo $bg->page_id; }

function core_pages_shortcode_page_list($obj){
	//type: should be a comma separated list
	//filter: can be things such as category:id
	//parent: can be a single integer or an array of integers
	//id: can be a single integer or an array of integers
	global $bdb, $bg;
	$opt = $obj->options;
	
	$html = '';
	
	$type = 'AND (';
	if(!empty($opt->type)){
		$t = explode(',', $opt->type);
		foreach($t as $c){
			$type .= "c.type='".trim($c)."' OR ";		
		}
		$type = substr($type, 0, strlen($type)-4);
	}else{
		$type .= "c.type='post'";	
	}
	$type .= ')';
	
	$parent = '';
	if(!empty($opt->parent)){
		$parent = 'AND (';
		if(is_numeric(trim($opt->parent))){
			$parent .= "c.parent_id='".trim($opt->parent)."'";	
		}else if(is_array($opt->parent)){
			foreach($opt->parent as $p){
				$parent .= "c.parent_id='".trim($p)."' OR ";			 
			}
			$parent = substr($parent, 0, strlen($parent)-4);
		}
		$parent .= ')';
	}
	
	$id = '';
	if(!empty($opt->id)){
		$id = 'AND (';
		if(is_numeric(trim($opt->id))){
			$id .= "c.id='".trim($opt->id)."'";	
		}else if(is_array($opt->id)){
			foreach($opt->id as $p){
				$id .= "c.id='".trim($p)."' OR ";			 
			}
			$id = substr($id, 0, strlen($id)-4);
		}
		$id .= ')';
	}
	
	if(isset($opt->filter)){
		foreach($opt->filter as $k=>$v){
			switch($k){
				case 'category':
					$query = "SELECT c.* FROM ".PREFIX."_relations r LEFT JOIN ".PREFIX."_content c ON c.id=r.resource_id WHERE r.relation_id='".$v."' AND c.status='published' $type $parent $id";
					break;
				case 'tag':
					$query = "SELECT c.* FROM ".PREFIX."_relations r LEFT JOIN ".PREFIX."_content c ON c.id=r.resource_id WHERE r.relation_id='".$v."' AND c.status='published' $type $parent $id";
					break;
				default:
					$query = "SELECT c.* FROM ".PREFIX."_content c WHERE c.status='published' $type $parent $id";
					break;
			}
		}	
	}else{
		$query = "SELECT c.* FROM ".PREFIX."_content c WHERE c.status='published' $type $parent $id";
	}
	
	$results = $bdb->get_results($query);
	$i = 0;
	foreach($results as $r){
		$content = unserialize($r->content);
		$content = (isset($opt->tv)) ? $content[$tv] : $content['content'];
		if(isset($opt->template)){ //Handle template passed in
			$template = html_entity_decode($opt->template);	
			$html .= $bg->template($template, $r);
		}else if(!empty($opt->template_file)){ //Handle specified template file
			$file = file_get_contents($opt->template_file);
			if($file !== false){
				$html .= $bg->template($file, $r);
			}
		}else{
			$html .= '<div class="page-list-item '.(($i==0)?'first':'').'">';
				$html .= '<div class="page-list-title '.(($i==0)?'first':'').'"><a href="'.URL.'/'.$r->guid.'" class="page-list-link '.(($i==0)?'first':'').'">'.$r->title.'</a></div>';
				$html .= '<div>';
					if($obj->stack[$obj->name]['level'] >= 0){
						$hold = strip_tags(parseShortCodes($content));	
					}else{
						$hold = parseShortCodes($content);	
					}
					if(isset($opt->length)){ $hold = substr($hold, 0, $opt->length); }
					$html .= $hold;
					//parseShortCodes($content);
				$html .= '</div>';
				//echo '<div class="page-list-content">'.(substr($content, 0, 100)).'</div>';
			$html .= '</div>';
		}
		$i++;
	}
	
	echo $html;
}

function core_pages_shortcode_page_thumb($obj){
	global $bg, $bdb;
	$opt = $obj->options;
	if(!isset($opt->id)){ return false; }
	$result = $bdb->get_result("SELECT i.guid,c.guid as link FROM ".PREFIX."_content as i LEFT JOIN ".PREFIX."_content as c ON (c.id=i.parent_id)  WHERE i.parent_id='".$opt->id."' AND i.status = '1' AND i.type='image'");
	
	$width = (isset($opt->width)) ? 'width="'.$opt->width.'"' : '';
	$height = (isset($opt->height)) ? 'height="'.$opt->height.'"' : '';
	$test = preg_match("/^resample-.+?x.+?-(.*)/", $result->guid, $matches);
	$thumb = ($test > 0) ? $matches[1] : $result->guid;
	
	if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.URL.'/'.$result->link.'" target="'.$opt->target.'">'; }
	if(file_exists(SITE.'/media/images/thumbs/'.$thumb) && is_file(SITE.'/media/images/thumbs/'.$thumb)){
		echo '<img class="'.$opt->class.'" src="'.URL.'/media/images/thumbs/'.$thumb.'" '.$width.' '.$height.' />';		
	}else if(isset($opt->noimage)){
		echo '<img class="'.$opt->class.'" src="'.$opt->noimage.'" '.$width.' '.$height.' />';			
	}
	if(isset($opt->url) && $opt->url == "true"){ echo '</a>'; }
}

function core_pages_shortcode_page_title($obj){
	global $bg, $bdb;
	$options = $obj->options;
	$id = (isset($options->id)) ? $options->id : $bg->page_id;
	$result = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE id='".$id."'");
	if(isset($options->link) && $options->link == true){ echo '<a href="'.URL.'/'.$result->guid.'"  '.((isset($options->target))?'target="'.$options->target.'"':'').' '.((isset($options->class))?'class="'.$options->class.'"':'').'>';  }
	echo $result->title;
	if(isset($options->link) && $options->link == true){ echo '</a>'; }
}

function core_pages_shortcode_page_content($obj){
	global $bg, $bdb;
	$options = $obj->options;
	if(!isset($options->id) || !isset($options->tv)){ return false; }
	$result = $bdb->get_result("SELECT content,guid FROM ".PREFIX."_content WHERE id='".$options->id."'");
	$content = unserialize($result->content);
	$content = $content[$options->tv];
	if(isset($options->notags) && $options->notags == true){ $content = strip_tags($content); }
	if($content == ''){ return true; } //Prevent loops with break on
	if(isset($options->link) && $options->link == true){ echo '<a href="'.URL.'/'.$result->guid.'"  '.((isset($options->target))?'target="'.$options->target.'"':'').' '.((isset($options->class))?'class="'.$options->class.'"':'').'>';  }
	
	if(isset($options->length)){ $length = $options->length; }else{ $length = 1; }
	if(isset($options->breakon)){
		switch($options->breakon){
			case 'word':
				while(substr($content, $length, 1) != " "){
					$length++;
					if($length >= strlen($content)){ break; }
				}
				break;
				//Need to implement paragraph, linebreak, etc
		}
	}
	
	if(isset($options->length)){ echo substr($content, 0, $length); }
	else{ echo $content; }
	
	if(isset($options->link) && $options->link == true){ echo '</a>'; }
}
	
function core_pages_css(){
	global $bg;
	$bg->add_css(URL."/core/pages/pages.css");		
}

function core_pages_sidebar(){
	echo '<h3><a href="#">Pages</a></h3>';
	echo '<div class="content core-pages-sidebar-content">';
		echo '<div class="core-pages-new-page"><img src="'.URL.'/core/pages/images/new.png" />New Page</div>';
		echo '<div id="core-pages-sidebar-list"></div>';
	echo '</div>';
}

function core_pages_js(){
	global $bg;
	$bg->add_js(URL."/core/pages/pages.js");	
}

function core_pages_new_page_dialog(){
	?>
	<div title="New Page" id="core-pages-new-page-dialog">
		<div style="width:500px; display:inline-block; float:left;">
			<div>Title <input type="text" id="core-pages-title" /></div>
			<div id="core-pages-permalink-wrapper" style="margin-top:5px;"><strong>Permalink</strong> <?php echo URL; ?>/<span id="core-pages-permalink" style="cursor:pointer;"></span></div>
			<div style="margin:15px 0;">Template <span id="core-pages-templates-select"></span></div>
			<div>Who can view this page? <select id="core-pages-viewable-by"></select></div>
			<div>Allow Comments? <input id="core-pages-comments" type="checkbox" checked /></div>
			<div>Allow Pingbacks and Trackbacks? <input id="core-pages-pingbacks" type="checkbox" /></div>
			<div>Publish on: <input type="text" id="core-pages-publish-on-date" size="10" value="<?php echo date('Y-m-d'); ?>" /><input type="text" id="core-pages-publish-on-time" size="5" value="00:00" /></div>
			<div style="margin-top:20px;">
				<div>Revisions</div>
				<div id="core-pages-revisions" style="height:200px; overflow-y:scroll; width:400px;"></div>
			</div>
		</div>
		<div style="display:inline-block; float:left;">
			<div><span style="display:inline-block; width:75px;">Status</span><select id="core-pages-status"><option value="published">Published</option><option value="draft">Draft</option></select></div>
			<div><span style="display:inline-block; width:75px;">Parent</span><span><select id="core-pages-parent"></select></span></div>
			<div><span style="display:inline-block; width:75px;">Order</span><span><input id="core-pages-sort-order" type="text" size="3" value="0" /></span></div>
			<div><span style="display:inline-block; width:75px;">Author</span><select id="core-pages-author"></select></div>
			<fieldset>
				<legend>Categories</legend>
				<div id="core-pages-categories-list" style="border:1px solid #ccc; padding:5px; height:150px; overflow-y:scroll"></div>
				<div id="core-pages-categories-add" style="margin-bottom:7px;"><a href="#">add new</a></div>
			</fieldset>
			<div><span style="display:inline-block; width:75px;">Tags</span><input type="text" id="core-pages-tags" /></div>
			<div id="core-pages-tag-list" style="width:260px;"></div>
			<div><input id="core-pages-page-id" type="hidden" /></div>
			<div style="margin-top:20px;"><span id="core-pages-create" class="button core-pages-create" style="float:right;"></span></div>
		</div>
	</div>
	<?php	
}
?>