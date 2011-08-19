// JavaScript Document
jQuery("#core-site-settings-wrapper").dialog({autoOpen:false, width:600, height:550});

jQuery("#bg-admin-sidebar-top .settings").click( function(){
	//build the plugins on this ajax call so hooks will be built
	jQuery("#core-site-settings-form").load("/ajax.php?build-plugins=true&file=core/site_settings/get_settings.php", function(){
		jQuery("#core-site-settings-tabs").tabs();																									  
	});
	jQuery("#core-site-settings-wrapper").dialog('open');															  
});

jQuery("#core-site-settings-form input[name='mail_type']").live("change", function(){
	jQuery("#core-site-settings-form input[name='mail_type']").parent().next().hide();
	jQuery(this).parent().next().show();
});

jQuery("#core-site-settings-form").submit( function(){
	var button = jQuery("#core-site-settings-wrapper button");
	var orig = button.html();
	button.html("Saving");
	var form = jQuery(this);
	var obj = {};
	
	jQuery("input[type=text],input[type=checkbox],input[type=radio]:checked,input[type=password],textarea,select", this).each( function(index,el){
		var name = jQuery(el).attr("name");
		obj[name] = {};
		obj[name].value = jQuery(el).val();
		obj[name].type = jQuery(el).attr("type");
	});
	
	jQuery.post("/ajax.php?file=core/site_settings/save.php", {obj:obj}, function(data){
		if(data == 'true'){
			button.html("Saved");
		}else{
			button.html("Error");	
		}
		setTimeout(function(){ button.html(orig); }, 2000);
	});
	
	return false;
});
