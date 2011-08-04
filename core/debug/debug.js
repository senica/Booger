// JavaScript Document
jQuery("#core-debug-wrapper").dialog({autoOpen:false, width:900, height:450});

jQuery("#bg-admin-sidebar-top .debug").click( function(){
	jQuery("#core-debug-wrapper").load("ajax.php?file=core/debug/compile.php", function(){
																							
	});
	jQuery("#core-debug-wrapper").dialog('open');															  
});

