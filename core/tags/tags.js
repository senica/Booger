// JavaScript Document
var core_tags = {};

jQuery("#core-tags-dialog-wrapper").dialog({width:500, autoOpen:false});

core_tags.get_sidebar = function(){
	jQuery("#core-tags-sidebar-list").load("/ajax.php?file=core/tags/get_tags.php");
}
core_tags.get_sidebar();

jQuery("#core-tags-form").submit( function(){
	var orig_submit = jQuery("#core-tags-form .core-tags-submit").val();
	jQuery("*", this).removeClass("error");
	var obj = {};
	obj.update = (jQuery("#core-tags-form .core-tags-submit").hasClass("update")) ? true : false;
	obj.id = (jQuery("#core-tags-form .core-tags-submit").hasClass("update")) ? jQuery("#core-tags-form .core-tags-submit").attr("dbid") : false;
	obj.name = jQuery(".core-tags-name", this).val();
	obj.permalink = jQuery(".core-tags-permalink", this).html();
	obj.description = jQuery(".core-tags-description", this).val();
	var error = false;
	if(obj.name == ""){ error = true; jQuery(".core-tags-name", this).addClass("error"); }
	if(obj.permalink == ""){ error = true; jQuery(".core-tags-permalink", this).addClass("error"); }
	
	if(!error){
		jQuery("#core-tags-form .core-tags-submit").val("Saving");
		jQuery.post("/ajax.php?file=core/tags/save_tag.php", {obj:obj}, function(data){
			var json = jQuery.parseJSON(data);
			if(json.error == "false"){
				jQuery("#core-tags-form .core-tags-permalink").html(json.permalink);
				jQuery("#core-tags-form .core-tags-submit").val("Saved");
				setTimeout(function(){ jQuery("#core-tags-form .core-tags-submit").val(orig_submit); }, 2000);
				core_tags.get_sidebar();
			}else{
				jQuery("#core-tags-form .core-tags-submit").val("Error");
				setTimeout(function(){ jQuery("#core-tags-form .core-tags-submit").val(orig_submit); }, 2000);		
			}
		});
	}
	
	return false;
});

//Change permalink and slug when title changes
jQuery("#core-tags-form .core-tags-name").keyup( function(event){
	var str = jQuery(event.target).val();
	str = str.replace(/\s/g, '-').replace(/[^\w|-]/g, '').toLowerCase();		
	jQuery("#core-tags-form .core-tags-permalink").html(str);											  
});

//Allow manual change to permalink
jQuery("#core-tags-form .core-tags-permalink").click( function(event){
	if(!jQuery("input", this).get(0)){
		var l = jQuery("#core-tags-form .core-tags-permalink").html();
		l = l.split("/");
		l = l[l.length-1];
		if(l != ""){ l = '<input type="text" value="'+l+'" />'; }else{ l = ''; }
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


//New tag
jQuery(".core-tags-new-tag").click( function(){
	jQuery("#core-tags-dialog-wrapper").dialog('open');
	jQuery("#core-tags-form .core-tags-submit").removeClass("update");
});

//Sidebar hover menu. Move menu to lotgion of title even if scrolled
jQuery(".core-tags-fl-tg-title").live("mousemove", function(event){
	jQuery(".core-tags-sidebar-title-menu", this).offset({top:jQuery(this).offset().top, left:jQuery(this).offset().left-120});
});

//Handle Delete
jQuery(".core-tags-sidebar-title-menu .delete").live("click", function(event){
	var id = jQuery(this).parents(".core-tags-fl-tg-title:first").attr("rel");
	jQuery.yesNo({
		message:	'Are you sure you want to delete this item and all it\'s children?',
		yes:		function(){
						jQuery.post("/ajax.php?file=core/tags/delete_tag.php", {id:id}, function(data){
							core_tags.get_sidebar();
							core_tags.get_parents();
						});
					}
	});
});

//Handle Edit
jQuery(".core-tags-sidebar-title-menu .edit").live("click", function(event){
	jQuery("#core-tags-dialog-wrapper").dialog('open');
	var id = jQuery(this).parents(".core-tags-fl-tg-title:first").attr("rel");
	jQuery.post("/ajax.php?file=core/tags/get_info.php", {id:id}, function(data){
		var obj = jQuery.parseJSON(data);
		jQuery("#core-tags-form .core-tags-name").val(obj.title);
		jQuery("#core-tags-form .core-tags-permalink").html(obj.guid);
		jQuery("#core-tags-form .core-tags-description").val(obj.description);
		jQuery("#core-tags-form .core-tags-submit").addClass("update");
		jQuery("#core-tags-form .core-tags-submit").attr("dbid", obj.id);
	});
});

