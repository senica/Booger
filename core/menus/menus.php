<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
/*	Usage:
*	[menus { "include":[1,3], "levels":2, "before_title":["text",[1,2]] }]
*	type	-	The type of content to get.  Such as, 'page', 'post'.  If not set, it will get all content. Comma separated list
*	include	-	array of integers. IDs of pages you want to build menus with
				default is all pages with parent_id of 0
	exclude	-	array of integers. IDs of pages to exclude from the menu.  Called after include
	levels	-	integer. the number of levels to dig down to. default is all levels
				"levels":0 will give you only the top level
	parent	-	the parent id to read from
	before_wrapper -array.
					element 1 is the html to display before the specified element
					element 2 is an array of levels to include the before text at.
						If you don't specify element 2, then it will add the text at every level.
						If you want to use element 3 but not element 2, then set element 2 to "false" or 0
					element 3 is an array of tags to include the before text at.
						If you don't specify element 3, then it will add the text at every tag
					Example: "before_title":["text",[1,2], [1,2,3,4]]
							In this example, the word "text" will be printed before the <div class="title">
							tag on levels 1 and 2; and before the first 4 title tags
	before_parent
	before_title
	before_children-wrapper
	before_children
	after_*			-array.  See corresponding before_*
*/
$bg->add_shortcode('menus', 'core_menus_init');

function core_menus_none(){}

function core_menus_before($tag,$level,$element,$options){
	$tag = 'before_'.$tag;
	if(empty($options->{$tag}[1])){ unset($options->{$tag}[1]); }
	if(empty($options->{$tag}[2])){ unset($options->{$tag}[2]); }
	//( !isset($options->{$tag}[1]) || in_array($level,$options->{$tag}[1]) ) )
	//Return before text....
	//If before tag is not set in options
	//or if it is set and no options are set
	//or if it is set level options are set and no element options are set or element options are set and we are on a valid element level
	//or if no level options are set and element options are set and we are on a current element level
	if(     isset($options->{$tag})    &&    (    ( !isset($options->{$tag}[1]) && !isset($options->{$tag}[2]) )    ||    (   isset($options->{$tag}[1]) && in_array($level,$options->{$tag}[1]) && (  !isset($options->{$tag}[2]) || ( isset($options->{$tag}[2]) && in_array($element,$options->{$tag}[2]) )  )   )    ||    ( isset($options->{$tag}[2]) && in_array($element,$options->{$tag}[2]) && !isset($options->{$tag}[1]) )    )     ){ return html_entity_decode($options->{$tag}[0]); }	
};

function core_menus_after($tag,$level,$element,$options){
	$tag = 'after_'.$tag;
	//if( isset($options->{$tag}) && ( !isset($options->{$tag}[1]) || in_array($level,$options->{$tag}[1]) ) ){ return html_entity_decode($options->{$tag}[0]); }	
	if(empty($options->{$tag}[1])){ unset($options->{$tag}[1]); }
	if(empty($options->{$tag}[2])){ unset($options->{$tag}[2]); }
	//( !isset($options->{$tag}[1]) || in_array($level,$options->{$tag}[1]) ) )
	//Return before text....
	//If before tag is not set in options
	//or if it is set and no options are set
	//or if it is set level options are set and no element options are set or element options are set and we are on a valid element level
	//or if no level options are set and element options are set and we are on a current element level
	if(     isset($options->{$tag})    &&    (    ( !isset($options->{$tag}[1]) && !isset($options->{$tag}[2]) )    ||    (   isset($options->{$tag}[1]) && in_array($level,$options->{$tag}[1]) && (  !isset($options->{$tag}[2]) || ( isset($options->{$tag}[2]) && in_array($element,$options->{$tag}[2]) )  )   )    ||    ( isset($options->{$tag}[2]) && in_array($element,$options->{$tag}[2]) && !isset($options->{$tag}[1]) )    )     ){ return html_entity_decode($options->{$tag}[0]); }	
}

function core_menus_init($obj){
	global $bdb, $bg;
	$obj->options->count = (empty($obj->options->count)) ? 0 : $obj->options->count + 1;
	$options = $obj->options;	
	$columns = (!empty($options->columns))	? mysql_real_escape_string($options->columns)	: 'title,guid';
	//$parent  = (!empty($options->parent))	? mysql_real_escape_string($options->parent)	: '0';
	
	$parent = '';
	if(!empty($options->parent)){
		$parent = 'AND parent_id IN (SELECT id FROM '.PREFIX.'_content WHERE ('; //Make sure the parents have a status of published
		if(is_numeric(trim($options->parent))){
			$parent .= "id='".mysql_real_escape_string(trim($options->parent))."'";
		}else if(is_array($options->parent)){
			foreach($options->parent as $p){
				$parent .= "id='".mysql_real_escape_string(trim($p))."' OR ";			 
			}
			$parent = substr($parent, 0, strlen($parent)-4);
		}
		$parent .= ") AND status = 'published' AND NOW() > publish_on)";
	}
	
	$type = '';
	if(!empty($options->type)){
		$t = explode(',', $options->type);
		foreach($t as $ti){
			$type .= "'".mysql_real_escape_string(trim($ti))."' OR type =";	
		}
		$type = substr($type, 0, strlen($type) - 10);
	}else{
		$type = "'page'";	
	}
	
	$include = '';
	if(!empty($options->include)){
		$include .= ' AND (';
		foreach($options->include as $inc){
			$include .= "id = '".$inc."' OR ";	
		}
		$include = substr($include, 0, strlen($include) - 4);
		$include .= ')';
	}
	
	$exclude = '';
	if(!empty($options->exclude)){
		$exclude .= ' AND (';
		foreach($options->exclude as $inc){
			$exclude .= "id != '".$inc."' AND ";	
		}
		$exclude = substr($exclude, 0, strlen($exclude) - 4);
		$exclude .= ')';
	}
	
	$query = "SELECT $columns,id FROM ".PREFIX."_content WHERE type = $type $include $exclude $parent AND status='published' AND NOW() > publish_on";
	//echo $query;
	$results = $bdb->get_results($query);
	if(!empty($results)){ 
		$i = 1;
		echo core_menus_before('wrapper',0,$options->count,$options).'<div class="core-menus-wrapper '.$options->class.'">';
		foreach($results as $result){
			$active = ($result->id == $bg->page_id)?'active':'';
			echo core_menus_before('parent',$options->count,$i,$options).'<div class="core-menus-parent '.$active.'">'.core_menus_before('title',$options->count,$i,$options).'<a href="'.URL.'/'.$result->guid.'" class="core-menus-title '.$active.'">'.$result->title.'</a>'.core_menus_after('title',$options->count,$i,$options);
				echo core_menus_before('children-wrapper',$options->count,$i,$options).'<div class="core-menus-children-wrapper">';
					$obj->options->parent = $result->id;
					( empty($options->levels) || $options->count <= $options->levels ) ? core_menus_init($obj) : '';
				echo '</div>'.core_menus_after('children-wrapper',$options->count,$i,$options);
			echo '</div>'.core_menus_after('parent',$options->count,$i,$options);		
			$i++;
		}
		echo '</div>'.core_menus_after('wrapper',$options->count,1,$options);
	}
}
?>
