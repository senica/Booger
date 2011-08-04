<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
/*	Usage:
*	[core_menus { "style":1, "include":[1,3], "levels":2, "before_title":["text",[1,2]] }]
*	type	-	The type of content to get.  Such as, 'page', 'post'.  If not set, it will get all content.
*	style	-	integer. default 1. Style of menu to use.
*	include	-	array of integers. IDs of pages you want to build menus with
				default is all pages with parent_id of 0
	levels	-	integer. the number of levels to dig down to. default is all levels
				"levels":0 will give you only the top level
	theme	-	name of theme to use. default is no theme. You can also just style in your site's css file.
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
$bg->add_shortcode('core_menus', 'core_menus_func');

//Test on Host Gator would not allow php5.3 and uploads to work, so rewrote anonymous functions to not be 5.3 dependent
$core_menus_opt = '';
function core_menus_before($tag,$level,$element){
	global $core_menus_opt;
	$tag = 'before_'.$tag;
	if(empty($core_menus_opt->{$tag}[1])){ unset($core_menus_opt->{$tag}[1]); }
	if(empty($core_menus_opt->{$tag}[2])){ unset($core_menus_opt->{$tag}[2]); }
	//( !isset($core_menus_opt->{$tag}[1]) || in_array($level,$core_menus_opt->{$tag}[1]) ) )
	//Return before text....
	//If before tag is not set in options
	//or if it is set and no options are set
	//or if it is set level options are set and no element options are set or element options are set and we are on a valid element level
	//or if no level options are set and element options are set and we are on a current element level
	if(     isset($core_menus_opt->{$tag})    &&    (    ( !isset($core_menus_opt->{$tag}[1]) && !isset($core_menus_opt->{$tag}[2]) )    ||    (   isset($core_menus_opt->{$tag}[1]) && in_array($level,$core_menus_opt->{$tag}[1]) && (  !isset($core_menus_opt->{$tag}[2]) || ( isset($core_menus_opt->{$tag}[2]) && in_array($element,$core_menus_opt->{$tag}[2]) )  )   )    ||    ( isset($core_menus_opt->{$tag}[2]) && in_array($element,$core_menus_opt->{$tag}[2]) && !isset($core_menus_opt->{$tag}[1]) )    )     ){ return html_entity_decode($core_menus_opt->{$tag}[0]); }	
};

function core_menus_after($tag,$level,$element){
	global $core_menus_opt;
	$tag = 'after_'.$tag;
	//if( isset($core_menus_opt->{$tag}) && ( !isset($core_menus_opt->{$tag}[1]) || in_array($level,$core_menus_opt->{$tag}[1]) ) ){ return html_entity_decode($core_menus_opt->{$tag}[0]); }	
	if(empty($core_menus_opt->{$tag}[1])){ unset($core_menus_opt->{$tag}[1]); }
	if(empty($core_menus_opt->{$tag}[2])){ unset($core_menus_opt->{$tag}[2]); }
	//( !isset($core_menus_opt->{$tag}[1]) || in_array($level,$core_menus_opt->{$tag}[1]) ) )
	//Return before text....
	//If before tag is not set in options
	//or if it is set and no options are set
	//or if it is set level options are set and no element options are set or element options are set and we are on a valid element level
	//or if no level options are set and element options are set and we are on a current element level
	if(     isset($core_menus_opt->{$tag})    &&    (    ( !isset($core_menus_opt->{$tag}[1]) && !isset($core_menus_opt->{$tag}[2]) )    ||    (   isset($core_menus_opt->{$tag}[1]) && in_array($level,$core_menus_opt->{$tag}[1]) && (  !isset($core_menus_opt->{$tag}[2]) || ( isset($core_menus_opt->{$tag}[2]) && in_array($element,$core_menus_opt->{$tag}[2]) )  )   )    ||    ( isset($core_menus_opt->{$tag}[2]) && in_array($element,$core_menus_opt->{$tag}[2]) && !isset($core_menus_opt->{$tag}[1]) )    )     ){ return html_entity_decode($core_menus_opt->{$tag}[0]); }	
}

function core_menus_tree($id, $tl=1, $cl=1, $type='page'){
	global $bdb,$bg;
	$results = $bdb->get_results("SELECT title,id,guid FROM ".PREFIX."_content WHERE parent_id='$id' AND type='$type' AND status='published' AND NOW() > publish_on");
	if(count($results) > 0){ 
		echo core_menus_before('children',$cl,1).'<div class="core-menus-children">';
		$i = 1;
		foreach($results as $result){
			$active = ($result->id == $bg->page_id)?'active':'';
			$extra = '';
			if($i == 1){ $extra .= ' first'; }
			if($i == count($results)){ $extra .= ' last'; }
			echo core_menus_before('parent',$cl,$i).'<div class="core-menus-parent'.$extra.' '.$active.'">'.core_menus_before('title',$cl,$i).'<a href="'.URL.'/'.$result->guid.'" class="core-menus-title'.$extra.' '.$active.'">'.$result->title.'</a>'.core_menus_after('title',$cl,$i);
				echo core_menus_before('children-wrapper',$cl,$i).'<div class="core-menus-children-wrapper">';
					( $cl < $tl || !isset($tl) ) ? core_menus_tree($result->id, $tl, $cl+1, $type) : '';
				echo '</div>'.core_menus_after('children-wrapper',$cl,$i);
			echo '</div>'.core_menus_after('parent',$cl,$i);
			$i++;
		}
		echo '</div>'.core_menus_after('children',$cl,1);
	}
}

function core_menus_func($obj){
	global $bdb, $core_menus_opt, $bg;
	$opt = $obj->options;
	$core_menus_opt = $obj->options;
	if(isset($opt->include)){
		$iq = '';
		foreach($opt->include as $include){
			$iq .= "id='$include' OR ";	
		}
		$iq = substr($iq, 0, strlen($iq)-4);
		$iq .= ' ';
	}else{ $iq = "parent_id='0'"; }
	
	//exclude pages
	if(isset($opt->exclude)){
		$eq = 'AND (';
		foreach($opt->exclude as $exclude){
			$eq .= "id!='$exclude' AND ";	
		}
		$eq = substr($eq, 0, strlen($eq)-4);
		$eq .= ')';
	}
	
	//Get menu style
	switch($opt->style){
		case 1:
			$cols = "title,id,guid";
			$bg->add_css(URL.'/core/menus/styles/menu1.css');
			break;
		
		case 2:
			$cols = "title,id,guid";
			$bg->add_css(URL.'/core/menus/styles/menu2.css');
			break;
		
		default:
			$cols = "title,id,guid";
			break;
	}
	
	//Get menu theme
	if(isset($opt->theme)){
		$bg->add_css(URL.'/core/menus/themes/'.$opt->theme.'.css');		
	}
	
	$query = "SELECT $cols FROM ".PREFIX."_content WHERE $iq $eq ".((isset($opt->type))?"AND type='".$opt->type."'":"")." AND status='published' AND NOW() > publish_on";
	$results = $bdb->get_results($query);
	if(count($results) > 0){ 
		echo core_menus_before('wrapper',0,1).'<div class="core-menus-wrapper '.$opt->class.'">';
		$i = 1;
		foreach($results as $result){
			$active = ($result->id == $bg->page_id)?'active':'';
			echo core_menus_before('parent',0,$i).'<div class="core-menus-parent '.$active.'">'.core_menus_before('title',0,$i).'<a href="'.URL.'/'.$result->guid.'" class="core-menus-title '.$active.'">'.$result->title.'</a>'.core_menus_after('title',0,$i);
				echo core_menus_before('children-wrapper',0,$i).'<div class="core-menus-children-wrapper">';
					( $opt->levels > 0 || !isset($opt->levels) ) ? core_menus_tree($result->id, $opt->levels, 1, $opt->type) : '';
				echo '</div>'.core_menus_after('children-wrapper',0,$i);
			echo '</div>'.core_menus_after('parent',0,$i);		
			$i++;
		}
		echo '</div>'.core_menus_after('wrapper',0,1);
	}
}
?>
