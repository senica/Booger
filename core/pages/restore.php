<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$current = $_POST['current'];
$restore = $_POST['restore'];
$cd = $bdb->get_result("SELECT * FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($current)."'");
$rd = $bdb->get_result("SELECT * FROM ".PREFIX."_content WHERE id='".mysql_real_escape_string($restore)."'");
//Don't restore all values to original.  Assume that the user has the page mostly where they want it in terms of order, and permissions, etc
if($rd !== false){
	$bdb->query("UPDATE ".PREFIX."_content SET title='".mysql_real_escape_string($rd->title)."', content='".mysql_real_escape_string($rd->content)."', template='".mysql_real_escape_string($rd->template)."', viewable_by='".mysql_real_escape_string($rd->viewable_by)."', allow_comments='".mysql_real_escape_string($rd->allow_comments)."', allow_pingbacks='".mysql_real_escape_string($rd->allow_pingbacks)."', publish_on='".mysql_real_escape_string($rd->publish_on)."', status='".mysql_real_escape_string($rd->status)."', menu_order='".mysql_real_escape_string($rd->menu_order)."', author='".mysql_real_escape_string($rd->author)."', modified='".mysql_real_escape_string($rd->modified)."', modified_gmt='".mysql_real_escape_string($rd->modified_gmt)."'  WHERE id='".mysql_real_escape_string($current)."'");
}
if($cd !== false){
	$bdb->query("UPDATE ".PREFIX."_content SET title='".mysql_real_escape_string($cd->title)."', content='".mysql_real_escape_string($cd->content)."', template='".mysql_real_escape_string($cd->template)."', viewable_by='".mysql_real_escape_string($cd->viewable_by)."', allow_comments='".mysql_real_escape_string($cd->allow_comments)."', allow_pingbacks='".mysql_real_escape_string($cd->allow_pingbacks)."', publish_on='".mysql_real_escape_string($cd->publish_on)."', status='".mysql_real_escape_string($cd->status)."', menu_order='".mysql_real_escape_string($cd->menu_order)."', author='".mysql_real_escape_string($cd->author)."', modified='".mysql_real_escape_string($cd->modified)."', modified_gmt='".mysql_real_escape_string($cd->modified_gmt)."'  WHERE id='".mysql_real_escape_string($restore)."'");
}
?>