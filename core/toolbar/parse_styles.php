<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	/*parse toolbar.css file located in theme directory*/
	if(file_exists(THEMES.'/'.THEME.'/toolbar.css')){
		$content = file_get_contents(THEMES.'/'.THEME.'/toolbar.css');
		$content = preg_replace('|^\s*@.*;|', '', $content); //Remove charsets eg @charset "utf-8";
		preg_match_all('/^[^{]*{[^}]*}.*?$/ims', $content, $matches); //Get CSS statements and all comments
		$objects = array(); //Object Holder
		foreach($matches[0] as $k => $v){
			$comments = array(); //Comment holder
			$v = preg_replace_callback('|/\*(.*?)\*/|', create_function('$m', 'global $comments; array_push($comments, $m[1]);'), $v); 
			$comment_holder = array();
			if(!empty($comments[0])){ array_push($comment_holder, trim($comments[0])); } //Only take first two comments Title
			if(!empty($comments[1])){ array_push($comment_holder, trim($comments[1])); } //Description
			$v = str_replace(array("\r\n", "\n", "\r"), ' ', $v); //Remove any line breaks as we don't need them
			$v = preg_replace('|{.*?}|', '', $v); //Remove the CSS styling
			$item = array();
			$item['tag'] = trim($v);
			if(!empty($comment_holder)){ $item['notes'] = $comment_holder; }
			array_push($objects, $item);
		}
		echo json_encode($objects);
	}else{
		$objects = array();
		$objects['err'] = 'No toolbar.css files exists in your theme directory.';
		echo json_encode($objects);
	}
?>