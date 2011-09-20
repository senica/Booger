<?php require(ASSETS.'/no_direct.php'); ?>
<?php
echo '<div id="core-site-settings-tabs">';
	echo '<ul>';
		echo '<li><a href="#core-site-settings-tabs-general">General</a></li>';
		echo '<li><a href="#core-site-settings-tabs-mail">Mail</a></li>';
		echo '<li><a href="#core-site-settings-tabs-misc">Miscellaneous</a></li>';
		$bg->call_hook('site-settings-tab-title');
	echo '</ul>';
	
	echo '<div id="core-site-settings-tabs-general" style="height:330px; overflow-y:scroll">';
		echo '<div><span>Site Title</span><input type="text" name="site_name" value="'.$bg->settings->site_name.'" /></div>';
		echo '<div><span>Site Description</span><textarea name="site_description">'.$bg->settings->site_description.'</textarea></div>';
		
		echo '<div><span>Site Home Page</span><select name="site_home" size="7">';
			$home = $bdb->get_results("SELECT id,title FROM ".PREFIX."_content WHERE type='page' OR type='post' ORDER BY type ASC, title ASC");
			foreach($home as $h){
				echo '<option value="'.$h->id.'" '.(($h->id == $bg->settings->site_home)?'selected':'').'>'.$h->title.'</option>';	
			}
		echo '</select></div>';
		
		echo '<div><span>Site Theme</span><select name="site_theme">';
			$files = scandir(THEMES);
			foreach($files as $file){
				if(is_dir(THEMES.'/'.$file) && $file != '.' && $file != '..'){
					echo '<option value="'.$file.'" '.(($file == $bg->settings->site_theme)?'selected':'').'>'.$file.'</option>';	
				}
			}
		echo '</select></div>';
		echo '<div><span>Default Template</span><select name="site_default_template">';
			foreach($bg->templates as $k=>$v){
				echo '<option value="'.$k.'" '.(($k== $bg->settings->site_default_template)?'selected':'').'>'.(substr($k, 0, strlen($k)-8)).'</option>';	
			}
		echo '</select></div>';
		echo '<div><span>Site URL</span><input type="text" name="site_url" value="'.$bg->settings->site_url.'" /></div>';
		echo '<div><span>Site Secure URL</span><input type="text" name="site_secure_url" value="'.$bg->settings->site_secure_url.'" /></div>';
		echo '<div><span>Domain</span><input type="text" name="site_domain" value="'.$bg->settings->site_domain.'" /></div>';
		$bg->call_hook('site-settings');
	echo '</div>';
	
	echo '<div id="core-site-settings-tabs-mail">';
		echo '<div><span>Admin Email</span><input type="text" name="admin_email" value="'.$bg->settings->admin_email.'" /></div>';
		echo '<div><input type="radio" name="mail_type" value="sendmail" style="width:auto;" '.(($bg->settings->mail_type == 'sendmail')?'checked':'').' /> Use Sendmail</div>';
		echo '<div style="margin-left:30px; display:'.(($bg->settings->mail_type == 'sendmail')?'block':'none').'; ">';
		echo '<div><span>Sendmail Path</span><input name="mail_path" type="text" value="'.$bg->settings->mail_path.'" /></div>';
		echo '</div>';
		echo '<div><input type="radio" name="mail_type" value="smtp" style="width:auto;" '.(($bg->settings->mail_type == 'smtp')?'checked':'').'> Use SMTP</div>';
		echo '<div style="margin-left:30px; display:'.(($bg->settings->mail_type == 'smtp')?'block':'none').'; ">';
		echo '<div><input type="radio" name="mail_security" value="ssl" style="width:auto;" '.(($bg->settings->mail_security == 'ssl')?'checked':'').' /> SSL   <input type="radio" name="mail_security" value="tls" style="width:auto;" '.(($bg->settings->mail_security == 'tls')?'checked':'').' /> TLS</div>';
		echo '<div><span>SMTP Server</span><input type="text" name="mail_server" value="'.$bg->settings->mail_server.'" /></div>';
		echo '<div><span>Port Number</span><input type="text" name="mail_port" value="'.$bg->settings->mail_port.'" /></div>';
		echo '<div><span>Username</span><input type="text" name="mail_user" value="'.$bg->settings->mail_user.'" /></div>';
		echo '<div><span>Password</span><input type="password" name="mail_password" value="'.$bg->settings->mail_password.'" /></div>';
		echo '</div>';
		$bg->call_hook('mail-settings');
	echo '</div>';
	
	echo '<div id="core-site-settings-tabs-misc">';
		$bg->call_hook('site-settings-misc');
	echo '</div>';
	$bg->call_hook('site-settings-tab-content');
echo '</div>'; //End of Tabs wrapper

echo '<div><button class="button" type="submit" >Save</button></div>';
?>