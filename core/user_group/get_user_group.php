<?php require(ASSETS.'/no_direct.php'); ?>
<?php
	function get_user_group_loop($id=0){
		global $bdb;
		$results = $bdb->get_results("SELECT id,parent_id,alias,name,email,type FROM ".PREFIX."_acl WHERE parent_id='$id' ORDER BY type");
		foreach($results as $result){
			echo '<div class="core-user-group-parent"><div class="core-user-group-title"><span class="core-user-group-img"><img src="core/user_group/images/'.(($result->type=='user')?'user':'group').'.png" /></span><span class="core-user-group-title-text" rel="'.$result->id.'" pid="'.$result->parent_id.'" alias="'.$result->alias.'" name="'.$result->name.'" email="'.$result->email.'" type="'.$result->type.'"><span>'.$result->name.'</span>';
				echo '<div class="core-user-group-sidebar-title-menu"><div class="core-user-group-sidebar-title-menu-content"><div class="arrow"></div>';
				if($result->type != 'user'){ echo '<div class="group">New Group</div><div class="user">New User</div>'; }
				echo '<div class="edit">Edit</div><hr />'.(($result->alias!='admin')?'<div class="delete">Delete</div>':'').'<div style="font-size:small">id:'.$result->id.'</div></div></div>';
			echo '</span></div><div class="core-user-group-child" style="display:block">';
				get_user_group_loop($result->id);
			echo '</div></div>';	
		}
	}
	get_user_group_loop();
?>