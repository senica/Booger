<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	/*parse toolbar.css file located in theme directory*/
	if(file_exists(THEMES.'/'.THEME.'/toolbar.css')){
		$content = file_get_contents(THEMES.'/'.THEME.'/toolbar.css');
		preg_match_all('/^\s*(.+)?{(.*?)}/im', $content, $matches);
		foreach($matches[1] as $k => $v){
			$v = explode(',', trim($v));
			foreach($v as $m){
				$el = preg_split('/([#.])/', $m, null, PREG_SPLIT_DELIM_CAPTURE);
					echo '<div class="core-toolbar-css-item">';
						if(isset($el[2]) && $el[2] != ''){
							$class = str_replace('-', ' ', $el[2]);
							$class = ucwords($class);
							echo '<div class="core-toolbar-css-class">'.$class.'</div>';
						}
						if(isset($el[0]) && $el[0] != ''){
							echo '<div class="core-toolbar-css-tag">Inserts '.$el[0].' tag.</div>';
						}
						$css = $matches[2][$k];
						preg_match('/description\s*:[\'"](.*?)[\'"]/', $css, $cm);
						if(isset($cm[1])){
							echo '<div class="core-toolbar-css-desc">'.$cm[1].'</div>';
						}
						echo '<div class="meta" cssClass="'.$el[2].'" cssTag="'.$el[0].'" cssType="'.(($el[1] == "#") ? 'id' : 'class' ).'"></div>';
					echo '</div>';
			}
			
		
		}
	}
?>