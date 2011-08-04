<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-head', 'core_blog_css');
$bg->add_hook('admin-sidebar', 'core_blog_sidebar');
$bg->add_hook('admin-foot', 'core_blog_js');
$bg->add_hook('admin-storage', 'core_blog_new_post_dialog');

$bg->add_shortcode('blog-list-reset', 'core_blog_list_reset');
$bg->add_shortcode('blog-list-post-title', 'core_blog_list_post_title');
$bg->add_shortcode('blog-list-post-author', 'core_blog_list_post_author');
$bg->add_shortcode('blog-list-post-tags', 'core_blog_list_post_tags');
$bg->add_shortcode('blog-list-post-content', 'core_blog_list_post_content');
$bg->add_shortcode('blog-list-post-image', 'core_blog_list_post_image');
$bg->add_shortcode('blog-list-post-read-more', 'core_blog_list_post_read_more');
$bg->add_shortcode('blog-list-paginate', 'core_blog_list_paginate');

$bg->url_filter('page', 'core_blog_page');

function core_blog_list_paginate($obj){
	global $bg, $bdb;
	$options = $obj->options;
	$id = $bg->shortcodes['blog-list-paginate']->index;
	$tc = $bg->shortcodes['blog-list-post-title']->offset;
	$cc = $bg->shortcodes['blog-list-post-content']->offset;
	$ti = $bg->shortcodes['blog-list-post-title']->index;
	$ci = $bg->shortcodes['blog-list-post-content']->index;
	//Should we consider image and read more here?
	$index = max($ti, $ci);
	$offset = max($tc, $cc);
	$result = $bdb->get_result("SELECT COUNT(id) as count FROM ".PREFIX."_content WHERE status='published' AND type='post'");
	$total = $result->count;
	$pages = @((int) ($total / $offset));
	if(@($total%$offset) > 0){ $pages++; }
	
	$active = 1;
	
	if(isset($_GET['core_blog_page'])){
		$explode = explode('-', $_GET['core_blog_page']);
		if($index > $explode[1] && $index < $explode[2]){
			$active = $explode[0];
		}
	}
	
	$before = false;
	$after = false;
	
	if(!isset($options->show)){ $options->show = 9; }
	if(!isset($options->before)){ $options->before = '...'; }
	if(!isset($options->after)){ $options->after = '...'; }
	
	if(isset($options->theme)){ $bg->add_css(URL.'/core/blog/paginate_themes/'.$options->theme.'.css'); }
	
	
	switch($options->style){
		case 1:
			$asw = $options->show-2; //we really want 7
			echo '<div class="core-blog-list-paginate id-'.$id.'">';
			for($i=0; $i<$pages; $i++){
				$current = $i+1;
				   //first page     //last page            
				if($current == 1 || $current == $pages || ($current<$asw+2 && $active<$asw && $current>$active-$asw/2) || ($current>$pages-($asw+1) && $active>$pages-$asw-1 && $current<$active+$asw/2) || ($current>($active-$asw/2) && $current<($active+$asw/2))  ){ 
					echo '<a class="blog-list-paginate-item id-'.$id.' '.(($active == $i+1)?'active':'').'" href="'.$bg->url.'/page/'.($i+1).'-'.($index - $offset).'-'.($index + 1).'">'.($i+1).'</a>';	
				}else if($current < $active && !$before){
					echo '<span class="blog-list-paginate-before id-'.$id.'">'.$options->before.'</span>';
					$before = true;
				}else if($current > $active && !$after){
					echo '<span class="blog-list-paginate-after id-'.$id.'">'.$options->after.'</span>';	
					$after = true;
				}
			}
			echo '</div>';
			break;
		
		case 0:
		default:
			echo '<div class="core-blog-list-paginate id-'.$id.'">';
			for($i=0; $i<$pages; $i++){
				echo '<a class="blog-list-paginate-item id-'.$id.' '.(($active == $i+1)?'active':'').'" href="'.$bg->url.'/page/'.($i+1).'-'.($index - $offset).'-'.($index + 1).'">'.($i+1).'</a>';	
			}
			echo '</div>';
			break;
	}
}

function core_blog_list_reset($obj){
	global $bg;
	$bg->shortcodes['blog-list-post-title']->offset = 0;
	$bg->shortcodes['blog-list-post-content']->offset = 0;
	$bg->shortcodes['blog-list-post-image']->offset = 0;
	$bg->shortcodes['blog-list-post-read-more']->offset = 0;
}

function core_blog_list_post_title($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT title,guid FROM ".PREFIX."_content WHERE status = 'published' AND type='post' ORDER BY id DESC LIMIT ".($offset-1).",1");
	if(isset($opt->length)){ $display = substr($result->title, 0, $opt->length); }else{ $display = $result->title; }
	if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.URL.'/'.$result->guid.'" class="'.$opt->class.'">'; }
	echo $display;
	if(isset($opt->link) && $opt->link == "true"){ echo '</a>'; }
}

function core_blog_list_post_author($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT a.name,a.website FROM ".PREFIX."_content c LEFT JOIN ".PREFIX."_acl a ON c.author=a.id  WHERE c.status = 'published' AND c.type='post' ORDER BY c.id DESC LIMIT ".($offset-1).",1");
	if(isset($opt->length)){ $display = substr($result->name, 0, $opt->length); }else{ $display = $result->name; }
	if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.$result->website.'" class="'.$opt->class.'" target="_blank">'; }
	echo $display;
	if(isset($opt->link) && $opt->link == "true"){ echo '</a>'; }
}

function core_blog_list_post_tags($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT id FROM ".PREFIX."_content WHERE status = 'published' AND type='post' ORDER BY id DESC LIMIT ".($offset-1).",1");
	$results = $bdb->get_results("SELECT c.title,c.guid FROM ".PREFIX."_relations r LEFT JOIN ".PREFIX."_content c ON c.id=r.relation_id WHERE r.resource_id='".$result->id."' AND c.type='tag'");
	$display = '';
	foreach($results as $r){
		if(isset($opt->link) && $opt->link == "true"){ $display .= '<a href="'.URL.'/'.$r->guid.'" class="'.$opt->class.'">'; }
		$display .= $r->title;
		if(isset($opt->link) && $opt->link == "true"){ $display .= '</a>'; }
		$display .= ', ';
	}
	$display = substr($display, 0, strlen($display)-2);
	if(isset($opt->length)){ $display = substr($display, 0, $opt->length); }
	echo $display;
}

function core_blog_list_post_read_more($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT guid FROM ".PREFIX."_content WHERE status = 'published' AND type='post' ORDER BY id DESC LIMIT ".($offset-1).",1");
	echo '<a href="'.URL.'/'.$result->guid.'" class="'.$opt->class.'">';
		echo (isset($opt->text)) ? $opt->text : 'Read More';
	echo '</a>';
}

function core_blog_list_post_image($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT i.title,i.guid,i.content,c.guid as link FROM ".PREFIX."_content as i LEFT JOIN ".PREFIX."_content as c ON (c.id=i.parent_id)  WHERE i.parent_id=(SELECT id FROM ".PREFIX."_content WHERE status = 'published' AND type='post' ORDER BY id DESC LIMIT ".($offset-1).",1) AND i.status = '1' AND i.type='image'");
	
	$width = (isset($opt->width)) ? 'width="'.$opt->width.'"' : '';
	$height = (isset($opt->height)) ? 'height="'.$opt->height.'"' : '';
	$test = preg_match("/^resample-.+?x.+?-(.*)/", $result->guid, $matches);
	$thumb = ($test > 0) ? $matches[1] : $result->guid;
	
	if(isset($opt->link) && $opt->link == "true"){ echo '<a href="'.URL.'/'.$result->link.'" class="core-blog-list-post-image id-'.$index.' '.$opt->class.'" target="'.$opt->target.'">'; }
	if(file_exists(SITE.'/media/images/thumbs/'.$thumb) && is_file(SITE.'/media/images/thumbs/'.$thumb)){ //need to make sure it is_file because file_exists returns true on directory apparently
		echo '<img class="'.$opt->class.'" src="'.URL.'/media/images/thumbs/'.$thumb.'" '.$width.' '.$height.' />';		
	}else if(isset($opt->noimage)){
		echo '<img class="'.$opt->class.'" src="'.$opt->noimage.'" '.$width.' '.$height.' />';			
	}
	if(isset($opt->link) && $opt->link == "true"){ echo '</a>'; }
}

function core_blog_list_post_content($obj){
	global $bdb, $bg;
	$opt = $obj->options;
	$offset = $obj->offset;
	
	//Handle pagination offset
	$index = $obj->index;
	if(isset($_GET['core_blog_page'])){
		$o = explode('-', $_GET['core_blog_page']); 
		if($index > $o[1] && $index < $o[2]){ $offset = $offset + ($o[0]-1)*($o[2]-($o[1]+1)); }
	}
	
	$result = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE status = 'published' AND type='post' ORDER BY id DESC LIMIT ".($offset-1).",1");
	$content = unserialize($result->content);
	if(isset($opt->tv)){ $src = $opt->tv; }else{ $src = 'content'; }
	$display = parseShortCodes($content[$src]); //Parse shortcodes here so we can limit length and stip tags if necessary
	if(isset($opt->nohtml)){ $display = strip_tags($display, $opt->allowed_html); }
	if(isset($opt->length)){ $display = substr($display, 0, $opt->length); }
	echo $display;
}
	
function core_blog_css(){
	global $bg;
	$bg->add_css(URL."/core/blog/blog.css");		
}

function core_blog_sidebar(){
	echo '<h3><a href="#">Blog</a></h3>';
	echo '<div class="content core-blog-sidebar-content">';
		echo '<div class="core-blog-new-post"><img src="'.URL.'/core/blog/images/new.png" />New Post</div>';
		echo '<div id="core-blog-sidebar-list"></div>';
	echo '</div>';
}

function core_blog_js(){
	global $bg;
	$bg->add_js(URL."/core/blog/blog.js");	
}

function core_blog_new_post_dialog(){
	?>
	<div title="New post" id="core-blog-new-post-dialog">
		<div style="width:500px; display:inline-block; float:left;">
			<div>Title <input type="text" id="core-blog-title" /></div>
			<div id="core-blog-permalink-wrapper" style="margin-top:5px; font-size:10px;"><strong>Permalink</strong> <?php echo URL; ?>/<span id="core-blog-permalink" style="cursor:pointer;"></span></div>
			<div style="margin:15px 0;">Template <span id="core-blog-templates-select"></span></div>
			<div>Who can view this post? <select id="core-blog-viewable-by"></select></div>
			<div>Allow Comments? <input id="core-blog-comments" type="checkbox" checked /></div>
			<div>Allow Pingbacks and Trackbacks? <input id="core-blog-pingbacks" type="checkbox" /></div>
			<div>Publish on: <input type="text" id="core-blog-publish-on-date" size="10" value="<?php echo date('Y-m-d'); ?>" /><input type="text" id="core-blog-publish-on-time" size="5" value="00:00" /></div>
			<div style="margin-top:20px;">
				<div>Revisions</div>
				<div id="core-blog-revisions" style="height:200px; overflow-y:scroll; width:400px;"></div>
			</div>
		</div>
		<div style="display:inline-block; float:left;">
			<div><span style="display:inline-block; width:75px;">Status</span><select id="core-blog-status"><option value="published">Published</option><option value="draft">Draft</option></select></div>
			<div><span style="display:inline-block; width:75px;">Author</span><select id="core-blog-author"></select></div>
			<fieldset>
				<legend>Categories</legend>
				<div id="core-blog-categories-list" style="border:1px solid #ccc; padding:5px; height:150px; overflow-y:scroll"></div>
				<div id="core-blog-categories-add" style="margin-bottom:7px;"><a href="#">add new</a></div>
			</fieldset>
			<div><span style="display:inline-block; width:75px;">Tags</span><input type="text" id="core-blog-tags" /></div>
			<div id="core-blog-tag-list" style="width:260px;"></div>
			<div><input id="core-blog-post-id" type="hidden" /></div>
			<div style="margin-top:20px;"><span id="core-blog-create" class="button core-blog-create" style="float:right;"></span></div>
		</div>
	</div>
	<?php	
}
?>