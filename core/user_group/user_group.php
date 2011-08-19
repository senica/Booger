<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-sidebar', 'core_user_group_sidebar');
$bg->add_hook('admin-head', 'core_user_group_head');
$bg->add_hook('admin-foot', 'core_user_group_js');
$bg->add_hook('admin-storage', 'core_user_group_storage');

function core_user_group_head(){
	global $bg;
	$bg->add_css(URL.'/core/user_group/user_group.css');
}

function core_user_group_sidebar(){
	echo '<h3><a href="#">Users & Groups</a></h3>';
	echo '<div id="core-user-group-list" class="content">';
		echo '<div class="core-user-group-parent"><div class="core-user-group-title"><span class="core-user-group-img"><img src="core/user_group/images/group.png" /></span><span class="core-user-group-title-text" rel="0" type="group"><span>Public</span>';
			echo '<div class="core-user-group-sidebar-title-menu"><div class="core-user-group-sidebar-title-menu-content"><div class="arrow"></div><div class="group">New Group</div><div class="user">New User</div><hr /><div style="font-size:small">id:0</div></div></div>';
		echo '</span></div><div id="core-user-group-load" class="core-user-group-child" style="display:block">';
		echo '</div></div>';
	echo '</div>';	
}

function core_user_group_js(){
	global $bg;
	$bg->add_js(URL."/core/user_group/user_group.js");	
}

function core_user_group_storage(){
	?><div id="core-user-group-dialog" title="Users & Groups">
		<div><span>Name</span><input class="name" type="text" /></div>
		<div><span>Alias</span><input class="alias" type="text" /></div>
		<div style="font-size:small"><span></span>Alias must be unique to type.</div>
		<div><span>Email</span><input class="email" type="text" /></div>
		<div><span>Password</span><input class="password" type="password" /></div>
		<div><span>Confirm</span><input class="confirm" type="password" /></div>
		<div style="font-size:small"><span></span><span class="ps"></span></div>
		<div class="parent_id"></div><div class="type"></div>
		<div style="font-size:small" class="info"></div>
		<div class="id"></div>
		<div><div class="button create">Create</div></div>
	</div><?php
}
?>