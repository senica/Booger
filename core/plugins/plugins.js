// JavaScript Document
jQuery("#core-plugins-wrapper").dialog({autoOpen:false, width:650, height:450});

jQuery("#bg-admin-sidebar-top .plugins").click( function(){
	jQuery("#core-plugins-wrapper").load("ajax.php?file=core/plugins/get_plugins.php", function(){
		jQuery("#core-plugins-accordion").accordion({active:false, collapsible:true, autoHeight:false});																						
	});
	jQuery("#core-plugins-wrapper").dialog('open');															  
});

jQuery("#core-plugins-form .more").live("click", function(){
	jQuery(this).parents("tr:first").next(".children").toggle();
	return false;
});

jQuery("#core-plugins-form .dir").live("click", function(){
	jQuery(this).parent().next(".child").toggle();
	return false;
});

jQuery("#core-plugins-form").live("submit", function(){
	var button = jQuery("button", this);
	var orig = button.html();
	button.html("Saving");
	
	var core = [];
	jQuery(".core", this).each( function(index, el){
		var plugin = {};
		plugin.name = jQuery(".isactive", el).val();
		plugin.active = (jQuery(".isactive", el).is(":checked")) ? 1 : 0;
		plugin.permissions = jQuery(".group", el).val();
		core.push(plugin);
	});
	
	var user = [];
	jQuery(".user", this).each( function(index, el){
		var plugin = {};
		plugin.name = jQuery(".isactive", el).val();
		plugin.active = (jQuery(".isactive", el).is(":checked")) ? 1 : 0;
		plugin.permissions = jQuery(".group", el).val();
		user.push(plugin);
	});
	
	var functions = [];
	jQuery(".functions", this).each( function(index, el){
		var plugin = {};
		plugin.name = jQuery(".isactive", el).val();
		plugin.active = (jQuery(".isactive", el).is(":checked")) ? 1 : 0;
		plugin.permissions = jQuery(".group", el).val();
		functions.push(plugin);
	});
	
	var files = [];
	jQuery(".file", this).each( function(index, el){
		var plugin = {};
		plugin.name = jQuery(".name", el).val();
		plugin.permissions = jQuery(".group", el).val();
		if(plugin.permissions != 0){ files.push(plugin); } //For files, we are only concerned if permissions other than public are set
	});
	
	jQuery.post("ajax.php?file=core/plugins/save.php", {core:core, user:user, functions:functions, files:files}, function(data){
		if(data == 'true'){
			button.html("Saved");
		}else{
			button.html("Error");	
		}
		setTimeout(function(){ button.html(orig); }, 2000);
	});
	
	return false;
});
