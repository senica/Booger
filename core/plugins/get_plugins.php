<?php require(ASSETS.'/no_direct.php'); ?>
<?php
//Plugins and functions are active and public by default
$results = $bdb->get_results("SELECT name,id,alias FROM ".PREFIX."_acl WHERE type='group'");
$acl = $bg->functions_acl;

function parse_tokens(){
	global $tokens,$results,$acl;
	$check = false;
	echo '<table style="border-collapse:collapse; width:100%">';
	foreach($tokens as $token){
		switch($token[0]){
			case T_FUNCTION:
				$check = true;
				break;
				
			case T_STRING:
				if($check){
					echo '<tr style="background:#F2F2F2" class="functions"><td align="center" style="padding-right:20px;"><input class="isactive" type="checkbox" value="'.$token[1].'" '.(($acl->{$token[1]}->active == 1 || !isset($acl->{$token[1]}) )?'checked':'').'></td><td style="font-size:small">'.( ucwords(str_replace('_', ' ', $token[1])) ).'</td>';
					echo '<td style="padding-left:20px;">';
						echo '<select class="group" style="font-size:small">';
							echo '<option value="0">Public</option>';
								foreach($results as $result){
									echo '<option value="'.$result->id.'" '.(($result->id == $acl->{$token[1]}->permissions)?'selected':'').'>'.$result->name.'</option>';	
								}
						echo '</select>';
					echo '</td></tr>';	
				}
				//echo ($check) ? '<tr><td colspan="3">'.$token[1].'</td></tr>' : '';
				$check = false;
				break;
		}
	}
	echo '</table>';
}

echo '<form id="core-plugins-form">';
echo '<div id="core-plugins-accordion">';
	//Core Plugins
	$core = unserialize($bg->settings->core_plugins);
	echo '<h3><a href="#">Core Plugins</a></h3>';
	echo '<div><table style="border-collapse:collapse; width:100%;"><tr><td style="padding-right:20px;">Active</td><td>Core Plugin Name</td><td style="padding-left:20px;">Access</td><td>Options</td></tr>';
		$files = scandir(CORE);
		foreach($files as $file){
			if(is_dir(CORE.'/'.$file) && $file != "." && $file != ".."){
				echo '<tr class="core"><td align="center" style="padding-right:20px;"><input class="isactive" type="checkbox" value="'.$file.'" '.(($core->{$file}->active == 1)?'checked':'').'></td><td>'.( ucwords(str_replace('_', ' ', $file)) ).'</td>';
				echo '<td style="padding-left:20px;">';
					echo '<select class="group">';
						echo '<option value="0">Public</option>';
							foreach($results as $result){
								echo '<option value="'.$result->id.'" '.(($result->id == $core->{$file}->permissions)?'selected':'').'>'.$result->name.'</option>';	
							}
					echo '</select>';
				echo '</td><td style="font-size:small; white-space:nowrap"><a href="#" class="more">more access options</a></td></tr>';
				echo '<tr class="children" style="display:none;"><td colspan="4">';
					$tokens = token_get_all(file_get_contents(CORE.'/'.$file.'/'.$file.'.php'));
					parse_tokens();
				echo '</td></tr>';
			}
		}
	echo '</table></div>';
	
	//User Plugins
	$user = unserialize($bg->settings->user_plugins);
	echo '<h3><a href="#">User Plugins</a></h3>';
	echo '<div><table style="border-collapse:collapse; width:100%;"><tr><td style="padding-right:20px;">Active</td><td>User Plugin Name</td><td style="padding-left:20px;">Access</td><td>Options</td></tr>';
		$files = scandir(PLUGINS);
		foreach($files as $file){
			if(is_dir(PLUGINS.'/'.$file) && $file != "." && $file != ".."){
				echo '<tr class="user"><td align="center" style="padding-right:20px;"><input class="isactive" type="checkbox" value="'.$file.'" '.(($user->{$file}->active == 1)?'checked':'').'></td><td>'.( ucwords(str_replace('_', ' ', $file)) ).'</td>';
				echo '<td style="padding-left:20px;">';
					echo '<select class="group">';
						echo '<option value="0">Public</option>';
							foreach($results as $result){
								echo '<option value="'.$result->id.'" '.(($result->id == $user->{$file}->permissions)?'selected':'').'>'.$result->name.'</option>';	
							}
					echo '</select>';
				echo '</td><td style="font-size:small; white-space:nowrap"><a href="#" class="more">more access options</a></td></tr>';
				echo '<tr class="children" style="display:none"><td colspan="4">';
					$tokens = token_get_all(file_get_contents(PLUGINS.'/'.$file.'/'.$file.'.php'));
					parse_tokens();
				echo '</td></tr>';
			}
		}
	echo '</table></div>';

	//Files
	$facl = $bg->files_acl;
	function core_plugins_get_files($dir,$l){
		global $results,$facl;
		$files = scandir($dir);
		foreach($files as $file){
			if($file != "." && $file != ".."){
				$margin = $l*15;
				echo '<div style="margin-left:'.$margin.'px; padding:5px; "><a href="#" class="dir" style="text-decoration:none;">'.$file.'</a>';
					if(is_file($dir.'/'.$file)){
						echo '<span style="float:right;" class="file">';
							$val = str_replace(SITE.'/', '', $dir.'/'.$file); //Get relative path
							echo '<input class="name" type="hidden" value="'.$val.'" />';
							echo '<select class="group">';
								echo '<option value="0">Public</option>';
									foreach($results as $result){
										echo '<option value="'.$result->id.'" '.(($result->id == $facl->{$val}->permissions)?'selected':'').'>'.$result->name.'</option>';	
									}
							echo '</select>';
						echo '</span>';
					}
				echo '</div>';
				if(is_dir($dir.'/'.$file)){ echo '<div class="child" style="display:none;">'; core_plugins_get_files($dir.'/'.$file,$l+1); echo '</div>'; }		
			}
		}
	}
	//$user = unserialize($bg->settings->user_plugins);
	echo '<h3><a href="#">Core Plugin Files</a></h3>';
	echo '<div>';
		core_plugins_get_files(CORE,0);
	echo '</div>';
	
	echo '<h3><a href="#">User Plugin Files</a></h3>';
	echo '<div>';
		core_plugins_get_files(PLUGINS,0);
	echo '</div>';
	
echo '</div>';
echo '<div style="text-align:right"><button type="submit" id="core-plugins-submit">Save</button></div>';
echo '</form>';
?>