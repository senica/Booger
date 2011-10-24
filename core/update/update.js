//Check for updates after the window loads
jQuery(window).load( function(){							  
	jQuery.get("/ajax.php?file=core/update/check.php", {}, function(json){
		var obj = jQuery.parseJSON(json);
		if(obj.update == true){
			jQuery("#bg-admin-sidebar-top .update").css({'background':'url(core/update/images/update-new.png)'}).effect('bounce', {times:3}, 300);	
		}
	});
});

//create dialog
jQuery("#core-update-admin-dialog").dialog({width:500, height:550, autoOpen:false, modal:true});

//Get update information when update button from sidebar is clicked
jQuery("#bg-admin-sidebar-top .update").click( function(){
	jQuery("#core-update-admin-dialog").html("Loading update information...");													
	jQuery("#core-update-admin-dialog").dialog('open');
	jQuery("#core-update-admin-dialog").load("/ajax.php?file=core/update/update-list.php", function(){
		jQuery("#core-update-admin-dialog .accordion").accordion({fillSpace:true, collapsible:true, active:false});																								
	});
});

var core_update = {};

core_update.status = function(msg){
	jQuery("#core-update-admin-dialog .status").append(msg);	
}

core_update.action = function(action){
	jQuery("#core-update-admin-dialog h3[data-action="+action+"]").click();	
}

core_update.cross = function(sha){
	jQuery("#core-update-admin-dialog .files[data-sha="+sha+"]").addClass("strikeout");	
}

core_update.done = function(){
	jQuery("#core-update-admin-dialog .iframe").remove();
	jQuery("#core-update-admin-dialog .update").remove();																					 
}

jQuery("#core-update-admin-dialog .update").live("click", function(){
	var iframe = jQuery("#core-update-admin-dialog .iframe");
	iframe.append('<iframe src="/ajax.php?file=core/update/client.php" style="display:none;"></iframe>');															   
});
