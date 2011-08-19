<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_shortcode('global', 'core_globals_func');

function core_globals_func($obj){
	global $bdb;
	$options = $obj->options;
	$html = '';
	$result = $bdb->get_result("SELECT content FROM ".PREFIX."_content WHERE title='".mysql_real_escape_string($options->name)."' AND type='global'");
	$html .= '<div class="template-variable global '.$options->name.' '.$options->class.'" rel="'.$options->name.'">';
	if($result !== false){ $html .= $result->content; }
	else if(isset($options->default)){ $html .= $options->default; }
	else{ $html .= ''; }
	$html .= '</div>';
	
	echo $html;
}
?>