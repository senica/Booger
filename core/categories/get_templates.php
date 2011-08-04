<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$result = $bdb->get_result("SELECT template FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($_POST['id'])."'");
$template = (!$result || $result->template == '') ? $bg->settings->site_default_template : $result->template;
$html = '';
foreach($bg->templates as $k=>$v){
	$html .= '<option value="'.$k.'" '.(($k == $template)?'selected':'').'>'.(substr($k, 0, strlen($k)-8)).'</option>';
}
echo $html;
?>