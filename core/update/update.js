// JavaScript Document
jQuery("#core-update-admin-dialog").dialog({width:500, height:550, autoOpen:false});

jQuery("#bg-admin-sidebar-top .update").click( function(){
	jQuery("#core-update-admin-dialog").dialog('open');
	jQuery("#core-update-admin-dialog").load("/ajax.php?file=core/update/check.php");
});

var core_update = {};

core_update.status = function(msg){
	jQuery("#core-update-admin-dialog .status").append(msg);	
}

core_update.done = function(){
	var status = jQuery("#core-update-admin-dialog .status");
	jQuery("iframe", status).remove();
	status = status.html();
	jQuery("#core-update-admin-dialog").load("/ajax.php?file=core/update/check.php", function(){
		jQuery(this).prepend("Your update was successful.<br />Please refresh any pages you are working on.<br /><br />");
		jQuery("#core-update-admin-dialog .status").html(status);																					 
	});
}

jQuery("#core-update-admin-dialog .update").live("click", function(){
	var status = jQuery("#core-update-admin-dialog .status");
	status.append('<iframe src="/ajax.php?file=core/update/client.php" style="display:none;"></iframe>');															   
});
