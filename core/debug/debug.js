// JavaScript Document
jQuery("#core-debug-wrapper").dialog({autoOpen:false, width:900, height:450});

jQuery("#bg-admin-sidebar-top .debug").click( function(){
	jQuery("#core-debug-wrapper").load("/ajax.php?file=core/debug/compile.php", function(){
																							
	});
	jQuery("#core-debug-wrapper").dialog('open');															  
});

jQuery(".bug-form").live("submit", function(){
	jQuery(".bug-message", this).html("Sending Bug Information.");
	jQuery(".bug-debug-info", this).val(jQuery(".debug-info").html());
	jQuery("*", this).attr("disabled", "true");
	var obj = jQuery(this).formobj();
	jQuery.post("/ajax.php?file=core/debug/post.php", {obj:obj}, function(data){
		jQuery(".bug-form *").removeAttr("disabled");
		var json = jQuery.parseJSON(data);
		jQuery(".bug-form .bug-message").html(json.message);
		if(json.error == false){
			//Reload Bugs and Refresh Status		
		}
		console.log(data);								 
	});
	
	return false;
});

