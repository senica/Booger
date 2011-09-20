// JavaScript Document
jQuery("#core-actions-wrapper").dialog({autoOpen:false, width:650, height:450});

//Open dialog, build site, and display actions
jQuery("#bg-admin-sidebar-top .actions").click( function(){
	jQuery("#core-actions-wrapper").load("/ajax.php?build-plugins=true&file=core/actions/get_actions.php", function(){																						
	});
	jQuery("#core-actions-wrapper").dialog('open');															  
});