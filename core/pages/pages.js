// JavaScript Document

//Functions
var core_pages = {};

//Create Dialog Box from Div
jQuery("#core-pages-new-page-dialog").dialog({autoOpen:false, width:'auto'});

//Create Datepicker and Timepicker for published date
jQuery("#core-pages-publish-on-date").datepicker({dateFormat:'yy-mm-dd'});
jQuery("#core-pages-publish-on-time").timepicker();

//Get Templates
core_pages.get_templates = function(){
	jQuery.post( "ajax.php?file=core/pages/get_templates.php", {}, function(data){
		//alert(data);
		jQuery("#core-pages-templates-select").html(data);							  
	});
}
core_pages.get_templates();

//Get Parents
core_pages.get_parents = function(){
	jQuery.post( "ajax.php?file=core/pages/get_pages.php", {type:'select'}, function(data){
		jQuery("#core-pages-parent").html('<option value="0" guid="none">(none)</option>'+data);														
	});
}
core_pages.get_parents();

//Get Pages for Sidebar
core_pages.get_sidebar = function(){
	jQuery.post( "ajax.php?file=core/pages/get_pages.php", {type:'flat_list'}, function(data){
		jQuery("#core-pages-sidebar-list").html(data);														
	});
}
core_pages.get_sidebar();

//Get Categories
core_pages.get_categories = function(id){
	jQuery("#core-pages-categories-list").load("ajax.php?file=core/pages/get_categories.php", {id:id});	
}

//Get Tags
core_pages.get_tags = function(id){
	jQuery("#core-pages-tag-list").load("ajax.php?file=core/pages/get_tags.php", {id:id});	
}

//Get Revisions
core_pages.get_revisions = function(id){
	jQuery("#core-pages-revisions").load("ajax.php?file=core/pages/get_revisions.php", {id:id});	
}

//Restore Revision
jQuery("#core-pages-revisions .restore").live("click", function(){
	var current = jQuery(this).attr("current");
	var restore = jQuery(this).attr("dbid");
	jQuery.post("ajax.php?file=core/pages/restore.php", {current:current, restore:restore}, function(){
		jQuery(".core-pages-fl-pg-title[rel='"+current+"'] .core-pages-sidebar-title-menu .options").click();
		jQuery(".bg-admin-tab[dbid='"+current+"'] .refresh").click(); 
	});
});

//Show New Category when add new category is clicked
jQuery("#core-pages-categories-add").click( function(){
	var listener = jQuery(this);
	core_categories.listener = listener;
	jQuery(listener).unbind("core_categories.new");
	jQuery(listener).bind("core_categories.new", function(event, obj){
		if(obj.parent == 0){
			jQuery("#core-pages-categories-list").prepend('<div style="margin-left:5px;"><input type="checkbox" value="'+obj.id+'" checked="yes" />'+obj.title+'</div>');		
		}else{
			var p = jQuery("#core-pages-categories-list input[value='"+obj.parent+"']").parent();
			var margin = parseInt(p.css('margin-left'));
			p.after('<div style="margin-left:'+(margin+5)+'px;"><input type="checkbox" value="'+obj.id+'" checked="yes" />'+obj.title+'</div>');	
		}
	});
	jQuery(".core-categories-new-category").click();													 
});

//Tags
core_pages.tags_timer = '';
core_pages.tags_set = function(){
	var tags = jQuery("#core-pages-tags").val();
	tags = tags.split(',');
	jQuery(tags).each( function(index, val){
		var tag = jQuery.trim(val);
		var inlist = false;
		jQuery("#core-pages-tag-list .tag").each( function(index, el){
			if(jQuery(el).html() == tag){
				inlist = true;	
			}
		});
		if(!inlist){
			jQuery("#core-pages-tag-list").append('<div style="font-size:small; display:inline-block; padding:3px;"><a href="#" class="clear"></a><span class="tag">'+tag+'</span></div>');
		}
	});	
	jQuery("#core-pages-tags").val("");
}
jQuery("#core-pages-tags").keyup( function(event){
	clearTimeout(core_pages.tags_timer);
	core_pages.tags_timer = setTimeout(function(){
		core_pages.tags_set();	
	}, 2000);
	if(event.keyCode == 13){
		clearTimeout(core_pages.tags_timer);
		core_pages.tags_set();
	}
});
jQuery("#core-pages-tag-list .clear").live("click", function(){
	jQuery(this).parent().remove();															 
});

//Show new page dialog when new page is clicked
jQuery(".core-pages-new-page").click( function(){
	jQuery("#core-pages-viewable-by").load("ajax.php?file=core/pages/groups.php"); //Get groups
	jQuery("#core-pages-author").load("ajax.php?file=core/pages/users.php"); //Get users
	jQuery("#core-pages-create").removeClass("update");
	jQuery("#core-pages-create").html("Create Page");
	jQuery("#core-pages-new-page-dialog").dialog('open');
	core_pages.get_templates();
	core_pages.get_parents();
	core_pages.get_categories();
});

//Change permalink when title changes
jQuery("#core-pages-title").keyup( function(event){
	var str = jQuery(event.target).val();
	str = str.replace(/\s/g, '-').replace(/[^\w|-]/g, '').toLowerCase();
	
	var guid = jQuery("#core-pages-parent option[value='"+jQuery("#core-pages-parent").val()+"']").attr("guid");
	if(guid=="none"){ var l = ''; }else{ var l = guid+'/'; }
	
	jQuery("#core-pages-permalink").html(l+str);											  
});

//Change permalink when parent changes
jQuery("#core-pages-parent").change( function(){
	var guid = jQuery("#core-pages-parent option[value='"+jQuery(this).val()+"']").attr("guid");
	var l = jQuery("#core-pages-permalink").html();
	l = l.split("/");
	l = l[l.length-1];
	if(l != ""){ l = ((guid=="none")?'':guid+'/')+l; }else{ l = (guid=="none")?'':guid+'/'; }
	jQuery("#core-pages-permalink").html(l);											  
});

//Allow manual change to permalink
jQuery("#core-pages-permalink").click( function(event){
	if(!jQuery("input", this).get(0)){
		var guid = jQuery("#core-pages-parent option[value='"+jQuery("#core-pages-parent").val()+"']").attr("guid");
		var l = jQuery("#core-pages-permalink").html();
		l = l.split("/");
		l = l[l.length-1];
		if(l != ""){ l = ((guid=="none")?'':guid+'/')+'<input type="text" value="'+l+'" />'; }else{ l = (guid=="none")?'':guid+'/'; }
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

//When Options/Settings is Clicked, get info
jQuery(".core-pages-sidebar-title-menu .options").live("click", function(event){
	var id = jQuery(this).parents(".core-pages-fl-pg-title:first").attr("rel");
	core_pages.get_categories(id);
	core_pages.get_tags(id);
	core_pages.get_revisions(id);
	jQuery.post("ajax.php?file=core/pages/get_info.php", {id:id}, function(data){
		if(data == "false"){ return true; }
		var d = jQuery.parseJSON(data);
		jQuery("#core-pages-title").val(d.title);
		jQuery("#core-pages-permalink").html(d.guid);
		jQuery("#core-pages-templates-select").load("ajax.php?file=core/pages/get_templates.php", function(){
			jQuery("#core-pages-templates-select select").val(d.template);																									   
		});
		jQuery("#core-pages-viewable-by").load("ajax.php?file=core/pages/groups.php", function(){
			jQuery("#core-pages-viewable-by").val(d.viewable_by);																					   
		});
		if(d.allow_comments == 0){ jQuery("#core-pages-comments").removeAttr("checked"); }else{ jQuery("#core-pages-comments").attr("checked", "true"); }
		if(d.allow_pingbacks == 0){ jQuery("#core-pages-pingbacks").removeAttr("checked"); }else{ jQuery("#core-pages-pingbacks").attr("checked", "true"); }
		var publish = d.publish_on.split(" ");
		jQuery("#core-pages-publish-on-date").val(publish[0]);
		jQuery("#core-pages-publish-on-time").val(publish[1]);
		jQuery("#core-pages-status").val(d.status);
		jQuery.post( "ajax.php?file=core/pages/get_pages.php", {type:'select'}, function(data){
			jQuery("#core-pages-parent").html('<option value="0" guid="none">(none)</option>'+data);
			jQuery("#core-pages-parent").val(d.parent_id);
		});
		jQuery("#core-pages-sort-order").val(d.menu_order);
		jQuery("#core-pages-author").load("ajax.php?file=core/pages/users.php", function(){
			jQuery("#core-pages-author").val(d.author);																				 
		});
		jQuery("#core-pages-page-id").val(id);
		jQuery("#core-pages-create").html("Update Page");
		jQuery("#core-pages-create").addClass("update");
		jQuery("#core-pages-new-page-dialog").dialog('open');
	});
});

//When create page is clicked check for valid fields
jQuery(".core-pages-create").click( function(){
	var title = jQuery("#core-pages-title").val();
	var permalink = jQuery("#core-pages-permalink").html();
	var template = jQuery("#core-pages-template").val();
	var viewable = jQuery("#core-pages-viewable-by").val();
	var comments = jQuery("#core-pages-comments").attr("checked");
	var pingbacks = jQuery("#core-pages-pingbacks").attr("checked");
	var publishDate = jQuery("#core-pages-publish-on-date").val();
	var publishTime = jQuery("#core-pages-publish-on-time").val();
	var status = jQuery("#core-pages-status").val();
	var parent = jQuery("#core-pages-parent").val();
	var order = jQuery("#core-pages-sort-order").val();
	var author = jQuery("#core-pages-author").val();
	var id = jQuery("#core-pages-page-id").val();
	
	var categories = [];
	jQuery("#core-pages-categories-list input").each( function(index,el){
		if(jQuery(el).is(":checked")){
			categories.push(jQuery(el).val());
		}
	});
	
	var tags = [];
	jQuery("#core-pages-tag-list .tag").each( function(index, el){
		tags.push(jQuery(el).html());
	});
	
	var error = false;
	if(title == ''){ jQuery("#core-pages-title").addClass('input-error'); error = true; }
	if(publishDate == ''){ jQuery("#core-pages-publish-on-date").addClass('input-error'); error = true; }
	if(publishTime == ''){ jQuery("#core-pages-publish-on-time").addClass('input-error'); error = true; }
	if(error === false){
		var update = false;
		if(jQuery("#core-pages-create").hasClass("update")){ update = true; }
		jQuery(".core-pages-create").html((!update)?"Creating Page":"Updating Page");
		jQuery("#core-pages-title").removeClass('input-error');
		jQuery("#core-pages-publish-on-date").removeClass('input-error');
		jQuery("#core-pages-publish-on-time").removeClass('input-error');
		
		jQuery.post( "ajax.php?file=core/pages/create_page.php", {id:id, tags:tags, categories:categories, update:update, title:title, permalink:permalink, template:template, viewable:viewable, comments:comments, pingbacks:pingbacks, publishDate:publishDate, publishTime:publishTime, status:status, parent:parent, order:order, author:author}, function(data){
			if(data == "false"){
				jQuery(".core-pages-create").html((!update)?"Could Not Create Page":"Could Not Update Page");
				jQuery(".core-pages-create").addClass('input-error');
				setTimeout(function(){
					jQuery(".core-pages-create").html((!update)?"Create Page":"Update Page");
					jQuery(".core-pages-create").removeClass('input-error');						
				}, 2000);
			}else{
				jQuery("#core-pages-permalink").html(data);
				jQuery(".core-pages-create").html((!update)?"Page Created":"Page Updated");
				setTimeout(function(){
					jQuery(".core-pages-create").html((!update)?"Create Page":"Update Page");						
				}, 1000);
				jQuery.post( "ajax.php?file=core/pages/get_pages.php", {type:'select'}, function(data){
					jQuery("#core-pages-parent").html('<option value="0" guid="none">(none)</option>'+data);
					jQuery("#core-pages-parent").val(parent);
				});
				//core_pages.get_templates();
				core_pages.get_sidebar();
			}
		});
	}
});

//Handle Page Delete
jQuery(".core-pages-sidebar-title-menu .delete").live("click", function(event){
	var id = jQuery(this).parents(".core-pages-fl-pg-title:first").attr("rel");
	jQuery.yesNo({
		message:	'Are you sure you want to delete this item and all it\'s children?',
		yes:		function(){
						jQuery.post("ajax.php?file=core/pages/delete.php", {id:id}, function(data){
							core_pages.get_sidebar();	
						});
					}
	});
});

//Sidebar hover menu. Move menu to location of title even if scrolled
jQuery(".core-pages-fl-pg-title").live("mousemove", function(event){
	jQuery(".core-pages-sidebar-title-menu", this).offset({top:jQuery(this).offset().top, left:jQuery(this).offset().left-120});
});


//Handle Sidebar page menu
jQuery(".core-pages-sidebar-title-menu .edit").live("click", function(){
	var p = jQuery(this).parents(".core-pages-fl-pg-title:first");
	var t = Date.now();
	var alt = p.attr("alt");
	var z = false;
	jQuery(".bg-admin-tab").each( function(){
		if(jQuery(this).attr("alt") == alt){
			z = true;	
		}
	});	
	if(z){
		jQuery(".bg-admin-tab[alt='"+alt+"']").click();	
	}else{
		var loc = bg.url+"/"+alt;
		jQuery(bg.pages).append('<div class="bg-admin-page" rel="'+t+'"><iframe rel="'+t+'" dbid="'+p.attr("rel")+'" src="'+loc+'?noparse" onLoad="jQuery.iframeResize(this); core_pages.events(this);"></iframe></div>');
		jQuery("#bg-admin-bottom-bar-col-one").append('<div id="" class="bg-admin-tab" rel="'+t+'" alt="'+alt+'" dbid="'+p.attr("rel")+'">'+p.attr("dtitle")+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+alt+'</div><hr /><div class="refresh">Refresh</div><div class="close">Close</div><div class="save">Save</div></div></div></div>');
		
		//Handle Saving of file
		jQuery(".bg-admin-tab[rel='"+t+"'] .save").live("click", function(){
			var save = jQuery(this);
			save.html("Saving...");
			var t = jQuery(this).parents(".bg-admin-tab:first");
			var rel = t.attr("rel");
			var id = t.attr("dbid");
			var tv = jQuery("iframe[rel='"+rel+"']").get(0);
			var context = tv.contentWindow.document;
			var data = new Object;
			var globals = new Object;
			jQuery(".template-variable", context).each( function(index, el){
				//Convert rendered shortcodes back to code form before saving
				var clone = jQuery(this).clone();
				jQuery(".template-variable-noparse", clone).each( function(index, el){
					jQuery(this).replaceWith(jQuery(".template-variable-noparse-code",this).html());														  
				});
				
				//Chrome (and maybe others) won't allow the script tag to be posted back to the html document, so tools that edit the html should
				//regex the html to replace <script tag with <!--script and <\/script> with </script-->
				//Likewise, saving should reverse the process
				var val = clone.html();
				val = val.replace(/<!--script/g, '<script').replace(/<\/script-->/g, '</script>');
				
				if(jQuery(el).hasClass("global")){
					var tvt = jQuery(el).attr("rel");
					globals[tvt] = val;//jQuery(el).html();
				}else if(jQuery(el).hasClass("editable")){
					var tvt = jQuery(el).attr("rel");
					data[tvt] = val; //jQuery(el).html();
				}
				clone.remove();
			});
			jQuery.post("ajax.php?file=core/pages/save.php", {globals:globals, data:data, id:id}, function(data){
				if(data == "true"){
					save.html("Saved");
					setTimeout(function(){
						save.html("Save");						
					}, 1000);		
				}else{
					save.html("Error");
					setTimeout(function(){
						save.html("Save");						
					}, 1000);	
				}
			});
		}); //Save Files
		
		//Handle Page Tab Close
		jQuery(".bg-admin-tab[rel='"+t+"'] .close").live("click", function(){
			var rel = jQuery(this).parents(".bg-admin-tab:first").attr("rel");
			var p = jQuery(this).parents(".bg-admin-tab:first");
			var prev = p.prevAll(".bg-admin-tab:first");
			if(!prev.get(0)){ p.next().click(); }
			else{ prev.click(); }
			jQuery(this).parents(".bg-admin-tab:first").remove();
			jQuery(".bg-admin-page[rel='"+rel+"']").remove();
		});
		
		//Handle Refreshing of a Page
		jQuery(".bg-admin-tab[rel='"+t+"'] .refresh").live("click", function(){
			var t = jQuery(this).parents(".bg-admin-tab:first");
			var rel = t.attr("rel");
			var tv = jQuery("iframe[rel='"+rel+"']").get(0);
			tv.contentWindow.location.reload(true);
		});
		
		//Handle File Tab Click
		jQuery(".bg-admin-tab[rel='"+t+"']").live("click", function(event){
			if( jQuery(event.target).hasClass("close") ){ return true; }
			jQuery(".bg-admin-tab").removeClass("active");
			jQuery(this).addClass("active");
			jQuery(".bg-admin-page").removeClass("active");
			jQuery(".bg-admin-page[rel='"+t+"']").addClass("active");
		});
		
		//Init
		jQuery(".bg-admin-tab[rel='"+t+"']").click();

	}//Else
});

//Function to trigger events when the page loads
core_pages.events = function(frame){
	jQuery(document).trigger('core_pages.loaded', [frame]);
	var context = frame.contentWindow.document;
	jQuery(".template-variable.editable, .template-variable.global", context).attr("contenteditable", "true");
}

//Function to make template variables editable and other stuff
/*
core_pages.tv_alter = function(frame){
	
	
	Rethink the drag and drop thing.  I don't like it at this point with the media manager in place
	//Handle Drag and Drop Uploads
	jQuery(".template-variable", context).attr("save", bg.site+"/media/images");
	jQuery(".template-variable", context).dragDropUpload({
		processor:	"assets/js/dragDrop/upload.php",
		ondrop:		function(){
						jQuery(".core-files-storage").attr("title", "Uploading").html('Your file is being uploaded.  Please wait...').dialog({width:500});	
					},
		complete: function(data){
						if(data.msg == "true"){
							if(context.getSelection().rangeCount != 0){
								var range = context.getSelection().getRangeAt(0);
								var parent = range.startContainer.parentNode;
								var selectionContents = range.extractContents();
								var isRange = true;
							}else{
								var isRange = false;
							}
				
							if(data.mime == "text/plain"){
								var append = '<div><a href="'+data.url+'"><img src="'+bg.theme_url+'/images/text.png" />'+data.name+'</a></div>';
							}
							if(data.mime == "application/msword"){
								var append = '<div><a href="'+data.url+'"><img src="'+bg.theme_url+'/images/word.png" />'+data.name+'</a></div>';
							}
							if(data.mime == "application/pdf"){
								var append = '<div><a href="'+data.url+'"><img src="'+bg.theme_url+'/images/pdf.png" />'+data.name+'</a></div>';
							}
							if(data.mime == "image/png" || data.mime == "image/jpeg" || data.mime == "image/gif"){
								var append = '<div><img src="'+data.url+'" /></div>';
							}
							if(isRange){ var el = context.createElement("div"); el.innerHTML=append; range.insertNode(el); }
							else{jQuery(this).append(append);}
				
							jQuery(".core-files-storage").attr("title", "Your file has been uploaded").html('<a href="'+data.url+'" target="_blank">'+data.name+'</a>').dialog({width:500});																													
						}else{
							jQuery(".core-files-storage").attr("title", "Error").html('There was a problem uploading your file.').dialog({width:500});	
						}
					}
	});
		
};*/



