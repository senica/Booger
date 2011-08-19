// JavaScript Document
var core_user_group = {};

//Sidebar hover menu. Move menu to location of title even if scrolled
jQuery(".core-user-group-title-text").live("mousemove", function(event){
	jQuery(".core-user-group-sidebar-title-menu", this).offset({top:jQuery(this).offset().top-5, left:jQuery(this).offset().left-145});
});

//Load sidebar Users and Groups
core_user_group.load = function(){
	jQuery("#core-user-group-load").load("/ajax.php?file=core/user_group/get_user_group.php");
}
core_user_group.load();

//Make Dialog Box
jQuery("#core-user-group-dialog").dialog({autoOpen:false,width:360});

//Show Dialog on New Click
jQuery("#core-user-group-list .group, #core-user-group-list .user").live("click", function(){
	jQuery("#core-user-group-dialog input").val("");
	var elp = jQuery(this).parents(".core-user-group-title-text:first");
	if(jQuery(this).hasClass("group")){ var type = 'group'; }else{ var type = 'user'; }
	
	jQuery("#core-user-group-dialog div.parent_id").html('<input class="parent_id" type="hidden" value="'+elp.attr("rel")+'" />');
	jQuery("#core-user-group-dialog div.type").html('<input class="type" type="hidden" value="'+type+'" />');
	
	jQuery("#core-user-group-dialog div.id").html("");
	jQuery("#core-user-group-dialog .ps").html("");
	jQuery("#core-user-group-dialog .create").html('Create');
	
	jQuery("#core-user-group-dialog .info").html('<b>Parent:</b> '+jQuery("span:first", elp).html()+'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>New:</b> '+type);
	jQuery("#core-user-group-dialog").dialog('open');																	  
});

//Create New
jQuery("#core-user-group-dialog .create").live("click", function(){
	var update = false;
	if(jQuery("#core-user-group-dialog .create").html() == "Update"){ update = true; }

	jQuery("#core-user-group-dialog *").removeClass("error");
	var name = jQuery("#core-user-group-dialog .name").val();
	var alias = jQuery("#core-user-group-dialog .alias").val();
	var email = jQuery("#core-user-group-dialog .email").val();
	var password = jQuery("#core-user-group-dialog .password").val();
	var confirm = jQuery("#core-user-group-dialog .confirm").val();
	var type = jQuery("#core-user-group-dialog input.type").val();
	var id = jQuery("#core-user-group-dialog input.id").val();
	if(!update){
		var parent_id = jQuery("#core-user-group-dialog input.parent_id").val();
	}else{
		var parent_id = jQuery("#core-user-group-dialog select.parent_id").val();	
	}
	var error = false;
	if(name == ""){ jQuery("#core-user-group-dialog .name").addClass("error"); error = true; }
	if(alias == ""){ jQuery("#core-user-group-dialog .alias").addClass("error"); error = true; }
	if(email == ""){ jQuery("#core-user-group-dialog .email").addClass("error"); error = true; }
	//allow blank password
	if(password != confirm){ jQuery("#core-user-group-dialog .password, #core-user-group-dialog .confirm").addClass("error"); error = true; }
	if(error){ return true; }
	
	jQuery("#core-user-group-dialog .create").html((update)?'Updating':'Creating');
	
	if(password != ""){	password = sha1(password); }

	jQuery.post("/ajax.php?file=core/user_group/add_edit.php", {update:update, id:id, name:name, alias:alias, email:email, type:type, parent_id:parent_id, password:password}, function(data){
		if(data != "true"){
			jQuery("#core-user-group-dialog .create").addClass("error");
			jQuery("#core-user-group-dialog .create").html("Error");
			setTimeout(function(){jQuery("#core-user-group-dialog .create").html((update)?'Update':'Create');}, 1000);
		}else{
			jQuery("#core-user-group-dialog .create").html((update)?'Updated':'Created');
			setTimeout(function(){jQuery("#core-user-group-dialog .create").html((update)?'Update':'Create');}, 1000);
			core_user_group.load();			
		}
	});
});

//Edit
jQuery("#core-user-group-list .edit").live("click", function(){
	jQuery("#core-user-group-dialog input").val(""); //Set all fields empty
	var elp = jQuery(this).parents(".core-user-group-title-text:first"); //Get parent
	if(jQuery(this).hasClass("group")){ var type = 'group'; }else{ var type = 'user'; }
	
	jQuery("#core-user-group-dialog div.parent_id").html('<span>Parent</span><select class="parent_id"></select>');
	jQuery("#core-user-group-dialog select.parent_id").load("/ajax.php?file=core/user_group/get_groups.php", function(){
		jQuery("#core-user-group-dialog select.parent_id").val(elp.attr("pid"));																													 
	});
	jQuery("#core-user-group-dialog div.type").html('<input class="type" type="hidden" value="'+type+'" />');
	jQuery("#core-user-group-dialog div.id").html('<input class="id" type="hidden" value="'+elp.attr("rel")+'" />');
	jQuery("#core-user-group-dialog .ps").html('Leave blank to keep current.');
	jQuery("#core-user-group-dialog .create").html('Update');
	
	jQuery("#core-user-group-dialog .name").val(elp.attr("name"));
	jQuery("#core-user-group-dialog .alias").val(elp.attr("alias"));
	jQuery("#core-user-group-dialog .email").val(elp.attr("email"));
	jQuery("#core-user-group-dialog .info").html('<b>Record Type:</b> '+type);
	jQuery("#core-user-group-dialog").dialog('open');																	  
});

//Delete
jQuery("#core-user-group-list .delete").live("click", function(){
	var elp = jQuery(this).parents(".core-user-group-title-text:first"); //Get parent	
	jQuery.yesNo({
		message:	'Are you sure you want to delete this item AND any children?',
		yes:		function(){
						jQuery.post("/ajax.php?file=core/user_group/delete.php", {id:elp.attr("rel")}, function(data){
							core_user_group.load();		
						});																		 
					}
	});															   
});

