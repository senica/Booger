<?php require(ASSETS.'/no_direct.php'); ?>
<?php
ob_start();
//Build entire site to get information
require_once(SITE.'/index.php');
ob_end_clean();

$buffer = array();

echo '<div style="font-size:small; margin-bottom:15px;">This is some very general purpose information that may aid you in resolving a problem.<br />Most problems will be solved by using the browser\'s built-in Developer Tools.  (right-click > inspect element)</div>';

ob_start();
echo '<table>';
	echo '<tr><td>Booger User</td><td>'.$bg->user->name.'</td></tr>';
	echo '<tr><td>Booger Group</td><td>'.$bg->user->group_name.'</td></tr>';
	echo '<tr><td>Booger User Permissions</td><td>'; foreach($bg->user->permissions as $group){ echo $group->name.'<br />'; } echo '</td></tr>';
	echo '<tr><td>Defined Constants</td><td>';
		$constants = get_defined_constants(true);
		echo '<table>';
		foreach($constants['user'] as $k=>$v){
			echo '<tr><td>'.$k.'</td><td>'.$v.'</td></tr>';
		}
		echo '</table>';
	echo '</td></tr>';
	echo '<tr><td>Included Files</td><td>'; $files = get_included_files(); foreach($files as $file){ echo $file.'<br />'; } echo '</td></tr>';
	
	echo '<tr><td>Server IP</td><td>'.$bg->server_ip.'</td></tr>';
	echo '<tr><td>Your IP</td><td>'.$bg->remote_ip.'</td></tr>';
	
	echo '<tr><td>System Owner</td><td>'.get_current_user().'</td></tr>';
	echo '<tr><td>System Owner ID</td><td>'.getmyuid().'</td></tr>';
	echo '<tr><td>PHP Process ID</td><td>'.getmypid().'</td></tr>';
	echo '<tr><td>Current iNode</td><td>'.getmyinode().'</td></tr>';
	echo '<tr><td>Last Page Modification</td><td>'.date("F d Y H:i:s.", getlastmod()).'</td></tr>';
	echo '<tr><td>Memory Peak</td><td>'.memory_get_peak_usage().' bytes</td></tr>';
	echo '<tr><td>Memory Usage</td><td>'.memory_get_usage().' bytes</td></tr>';
	echo '<tr><td>Loaded .ini File</td><td>'.php_ini_loaded_file().' bytes</td></tr>';
	echo '<tr><td>PHP Connection</td><td>'.PHP_SAPI.'</td></tr>';
	echo '<tr><td>Operating System</td><td>'.php_uname('s').'</td></tr>';
	echo '<tr><td>Host Name</td><td>'.php_uname('n').'</td></tr>';
	echo '<tr><td>Release Name</td><td>'.php_uname('r').'</td></tr>';
	echo '<tr><td>Version</td><td>'.php_uname('v').'</td></tr>';
	echo '<tr><td>Machine Type</td><td>'.php_uname('m').'</td></tr>';
	
	echo '<tr><td>PHP Version</td><td>'.PHP_VERSION.'</td></tr>';
echo '</table>';
$buffer['content'] = ob_get_contents();
ob_end_clean();

echo '<div>';
	echo '<form action="#" name="bug-form" class="bug-form">';
		echo '<input type="hidden" name="bug-debug-info" class="bug-debug-info" value=""/>';
		echo '<div>Submit a Bug: <input type="text" name="bug" class="bug" /> <input class="bug-submit" type="submit" value="Send Bug & Debug Info" /></div>';
		echo '<div class="bug-info">Note: the following debug information will be sent with your bug submission.</div>';
		echo '<div class="bug-message"></div>';
	echo '</form>';
echo '</div>';

echo '<div class="debug-info">';
echo $buffer['content'];
echo '</div>';

?>