// JavaScript Document
var core_categories = {};
core_categories.listener = document;

jQuery("#core-categories-dialog-wrapper").dialog({width:500, autoOpen:false});

core_categories.get_sidebar = function(){
	jQuery("#core-categories-sidebar-list").load("/ajax.php?file=core/categories/get_categories.php", function(){
		jQuery(".core-categories-sidebar-title-menu .edit").editAsPage({
			'source':function(){ return jQuery(this).parents(".core-categories-fl-cat-title:first"); },
			'type'	:'category',
			'globals':false
		});																										  
	});
}
core_categories.get_sidebar();

jQuery("#core-categories-form").submit( function(){
	var orig_submit = jQuery("#core-categories-form .core-categories-submit").val();
	jQuery("*", this).removeClass("error");
	var obj = {};
	obj.update = (jQuery("#core-categories-form .core-categories-submit").hasClass("update")) ? true : false;
	obj.id = (jQuery("#core-categories-form .core-categories-submit").hasClass("update")) ? jQuery("#core-categories-form .core-categories-submit").attr("dbid") : false;
	obj.name = jQuery(".core-categories-name", this).val();
	obj.permalink = jQuery(".core-categories-permalink", this).html();
	obj.parent = jQuery(".core-categories-parent", this).val();
	obj.description = jQuery(".core-categories-description", this).val();
	obj.template = jQuery(".core-categories-template", this).val();
	var error = false;
	if(obj.name == ""){ error = true; jQuery(".core-categories-name", this).addClass("error"); }
	if(obj.permalink == ""){ error = true; jQuery(".core-categories-permalink", this).addClass("error"); }
	
	if(!error){
		jQuery("#core-categories-form .core-categories-submit").val("Saving");
		jQuery.post("/ajax.php?file=core/categories/save_category.php", {obj:obj}, function(data){
			var json = jQuery.parseJSON(data);
			if(json.error == "false"){
				jQuery("#core-categories-form .core-categories-permalink").html(json.permalink);
				jQuery("#core-categories-form .core-categories-submit").val("Saved");
				setTimeout(function(){ jQuery("#core-categories-form .core-categories-submit").val(orig_submit); }, 2000);
				core_categories.get_parents();
				core_categories.get_sidebar();
				jQuery(core_categories.listener).trigger("core_categories.new", [json]); //notify listeners of new category
			}else{
				jQuery("#core-categories-form .core-categories-submit").val("Error");
				setTimeout(function(){ jQuery("#core-categories-form .core-categories-submit").val(orig_submit); }, 2000);		
			}
		});
	}
	
	return false;
});

//Change permalink when parent changes
jQuery("#core-categories-form .core-categories-parent").change( function(){
	var guid = jQuery("#core-categories-form .core-categories-parent option:selected").attr("guid");
	var l = jQuery("#core-categories-form .core-categories-permalink").html();	
	l = l.split("/");
	l = l[l.length-1];
	if(l != ""){ l = ((guid=="0")?'':guid+'/')+l; }else{ l = (guid=="0")?'':guid+'/'; }
	jQuery("#core-categories-form .core-categories-permalink").html(l);											  
});

//Change permalink and slug when title changes
jQuery("#core-categories-form .core-categories-name").keyup( function(event){
	var str = jQuery(event.target).val();
	str = str.replace(/\s/g, '-').replace(/[^\w|-]/g, '').toLowerCase();	
	var guid = jQuery("#core-categories-form .core-categories-parent option:selected").attr("guid");
	if(guid=="0"){ var l = ''; }else{ var l = guid+'/'; }	
	jQuery("#core-categories-form .core-categories-permalink").html(l+str);											  
});

//Allow manual change to permalink
jQuery("#core-categories-form .core-categories-permalink").click( function(event){
	if(!jQuery("input", this).get(0)){
		var guid = jQuery("#core-categories-form .core-categories-parent option:selected").attr("guid");
		var l = jQuery("#core-categories-form .core-categories-permalink").html();
		l = l.split("/");
		l = l[l.length-1];
		if(l != ""){ l = ((guid=="0")?'':guid+'/')+'<input type="text" value="'+l+'" />'; }else{ l = (guid=="0")?'':guid+'/'; }
		jQuery(this).html(l);
		jQuery("input", this).bind("keydown", function(event){
			if(event.keyCode == 13){
				var str = jQuery(this).val();
				str = str.replace(/\s/g, '-').replace(/[^\w|-]/g, '').toLowerCase();
				jQuery(this).replaceWith(str);	
			}
		});
	}
});


core_categories.get_parents = function(id){
	jQuery (".core-categories-parent").load("/ajax.php?file=core/categories/get_parents.php", {id:id});	
};
core_categories.get_parents();

core_categories.get_templates = function(id){
	jQuery (".core-categories-template").load("/ajax.php?file=core/categories/get_templates.php", {id:id});	
};
core_categories.get_templates();

//New Category
jQuery(".core-categories-new-category").click( function(){
	jQuery("#core-categories-dialog-wrapper").dialog('open');
	jQuery("#core-categories-form .core-categories-submit").removeClass("update");
	core_categories.get_parents();
	core_categories.get_templates();
});

//Sidebar hover menu. Move menu to location of title even if scrolled
jQuery(".core-categories-fl-cat-title").live("mousemove", function(event){
	jQuery(".core-categories-sidebar-title-menu", this).offset({top:jQuery(this).offset().top, left:jQuery(this).offset().left-120});
});

//Handle Delete
jQuery(".core-categories-sidebar-title-menu .delete").live("click", function(event){
	var id = jQuery(this).parents(".core-categories-fl-cat-title:first").attr("rel");
	jQuery.yesNo({
		message:	'Are you sure you want to delete this item and all it\'s children?',
		yes:		function(){
						jQuery.post("/ajax.php?file=core/categories/delete_category.php", {id:id}, function(data){
							core_categories.get_sidebar();
							core_categories.get_parents();
						});
					}
	});
});

//Handle Settings
jQuery(".core-categories-sidebar-title-menu .settings").live("click", function(event){
	jQuery("#core-categories-dialog-wrapper").dialog('open');
	var id = jQuery(this).parents(".core-categories-fl-cat-title:first").attr("rel");
	core_categories.get_parents(id);
	core_categories.get_templates(id);
	jQuery.post("/ajax.php?file=core/categories/get_info.php", {id:id}, function(data){
		var obj = jQuery.parseJSON(data);
		jQuery("#core-categories-form .core-categories-name").val(obj.title);
		jQuery("#core-categories-form .core-categories-permalink").html(obj.guid);
		jQuery("#core-categories-form .core-categories-parent").val(obj.parent_id);
		jQuery("#core-categories-form .core-categories-template").html(obj.templates);
		jQuery("#core-categories-form .core-categories-description").val(obj.description);
		jQuery("#core-categories-form .core-categories-submit").addClass("update");
		jQuery("#core-categories-form .core-categories-submit").attr("dbid", obj.id);
	});
});

