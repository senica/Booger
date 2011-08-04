// JavaScript Document

//Functions
var core_blog = {};

//Create Dialog Box from Div
jQuery("#core-blog-new-post-dialog").dialog({autoOpen:false, width:'auto'});

//Create Datepicker and Timepicker for published date
jQuery("#core-blog-publish-on-date").datepicker({dateFormat:'yy-mm-dd'});
jQuery("#core-blog-publish-on-time").timepicker();

//Get Templates
core_blog.get_templates = function(){
	jQuery.post( "ajax.php?file=core/blog/get_templates.php", {}, function(data){
		//alert(data);
		jQuery("#core-blog-templates-select").html(data);							  
	});
}
core_blog.get_templates();

//Get blog for Sidebar
core_blog.get_sidebar = function(){
	jQuery("#core-blog-sidebar-list").load("ajax.php?file=core/blog/get_posts.php", {type:'flat_list'});
}
core_blog.get_sidebar();

//Get Categories
core_blog.get_categories = function(id){
	jQuery("#core-blog-categories-list").load("ajax.php?file=core/blog/get_categories.php", {id:id});	
}

//Get Tags
core_blog.get_tags = function(id){
	jQuery("#core-blog-tag-list").load("ajax.php?file=core/blog/get_tags.php", {id:id});	
}

//Get Revisions
core_blog.get_revisions = function(id){
	jQuery("#core-blog-revisions").load("ajax.php?file=core/blog/get_revisions.php", {id:id});	
}

//Restore Revision
jQuery("#core-blog-revisions .restore").live("click", function(){
	var current = jQuery(this).attr("current");
	var restore = jQuery(this).attr("dbid");
	jQuery.post("ajax.php?file=core/blog/restore.php", {current:current, restore:restore}, function(){
		jQuery(".core-blog-fl-pg-title[rel='"+current+"'] .core-blog-sidebar-title-menu .options").click();
		jQuery(".bg-admin-tab[dbid='"+current+"'] .refresh").click(); 
	});
});

//Show New Category when add new category is clicked
jQuery("#core-blog-categories-add").click( function(){
	var listener = jQuery(this);
	core_categories.listener = listener;
	jQuery(listener).unbind("core_categories.new");
	jQuery(listener).bind("core_categories.new", function(event, obj){
		if(obj.parent == 0){
			jQuery("#core-blog-categories-list").prepend('<div style="margin-left:5px;"><input type="checkbox" value="'+obj.id+'" checked="yes" />'+obj.title+'</div>');		
		}else{
			var p = jQuery("#core-blog-categories-list input[value='"+obj.parent+"']").parent();
			var margin = parseInt(p.css('margin-left'));
			p.after('<div style="margin-left:'+(margin+5)+'px;"><input type="checkbox" value="'+obj.id+'" checked="yes" />'+obj.title+'</div>');	
		}
	});
	jQuery(".core-categories-new-category").click();													 
});

//Tags
core_blog.tags_timer = '';
core_blog.tags_set = function(){
	var tags = jQuery("#core-blog-tags").val();
	tags = tags.split(',');
	jQuery(tags).each( function(index, val){
		var tag = jQuery.trim(val);
		var inlist = false;
		jQuery("#core-blog-tag-list .tag").each( function(index, el){
			if(jQuery(el).html() == tag){
				inlist = true;	
			}
		});
		if(!inlist){
			jQuery("#core-blog-tag-list").append('<div style="font-size:small; display:inline-block; padding:3px;"><a href="#" class="clear"></a><span class="tag">'+tag+'</span></div>');
		}
	});	
	jQuery("#core-blog-tags").val("");
}
jQuery("#core-blog-tags").keyup( function(event){
	clearTimeout(core_blog.tags_timer);
	core_blog.tags_timer = setTimeout(function(){
		core_blog.tags_set();	
	}, 2000);
	if(event.keyCode == 13){
		clearTimeout(core_blog.tags_timer);
		core_blog.tags_set();
	}
});
jQuery("#core-blog-tag-list .clear").live("click", function(){
	jQuery(this).parent().remove();															 
});

//Show new post dialog when new post is clicked
jQuery(".core-blog-new-post").click( function(){
	jQuery("#core-blog-viewable-by").load("ajax.php?file=core/blog/groups.php"); //Get groups
	jQuery("#core-blog-author").load("ajax.php?file=core/blog/users.php"); //Get users
	jQuery("#core-blog-create").removeClass("update");
	jQuery("#core-blog-create").html("Create Post");
	jQuery("#core-blog-new-post-dialog").dialog('open');
	core_blog.get_templates();
	core_blog.get_categories();
});

//Change permalink when title changes
jQuery("#core-blog-title").keyup( function(event){
	var str = jQuery(event.target).val();
	str = str.replace(/\s/g, '-').replace(/[^\w|-]/g, '').toLowerCase();
	
	//var guid = jQuery("#core-blog-parent option[value='"+jQuery("#core-blog-parent").val()+"']").attr("guid");
	//if(guid=="none"){ var l = ''; }else{ var l = guid+'/'; }
	
	//Need to implement Categories or permalink structure Here
	
	jQuery("#core-blog-permalink").html(str);											  
});

//Allow manual change to permalink
jQuery("#core-blog-permalink").click( function(event){
	if(!jQuery("input", this).get(0)){
		var l = jQuery("#core-blog-permalink").html();
		l = l.split("/");
		l = l[l.length-1];
		l = '<input type="text" value="'+l+'" />';
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

//When create post is clicked check for valid fields
jQuery(".core-blog-create").click( function(){
	var title = jQuery("#core-blog-title").val();
	var permalink = jQuery("#core-blog-permalink").html();
	var template = jQuery("#core-blog-template").val();
	var viewable = jQuery("#core-blog-viewable-by").val();
	var comments = jQuery("#core-blog-comments").attr("checked");
	var pingbacks = jQuery("#core-blog-pingbacks").attr("checked");
	var publishDate = jQuery("#core-blog-publish-on-date").val();
	var publishTime = jQuery("#core-blog-publish-on-time").val();
	var status = jQuery("#core-blog-status").val();
	var author = jQuery("#core-blog-author").val();
	var id = jQuery("#core-blog-post-id").val();
	
	var categories = [];
	jQuery("#core-blog-categories-list input").each( function(index,el){
		if(jQuery(el).is(":checked")){
			categories.push(jQuery(el).val());
		}
	});
	
	var tags = [];
	jQuery("#core-blog-tag-list .tag").each( function(index, el){
		tags.push(jQuery(el).html());
	});
	
	var error = false;
	if(title == ''){ jQuery("#core-blog-title").addClass('input-error'); error = true; }
	if(publishDate == ''){ jQuery("#core-blog-publish-on-date").addClass('input-error'); error = true; }
	if(publishTime == ''){ jQuery("#core-blog-publish-on-time").addClass('input-error'); error = true; }
	if(error === false){
		var update = false;
		if(jQuery("#core-blog-create").hasClass("update")){ update = true; }
		jQuery(".core-blog-create").html((!update)?"Creating Post":"Updating Post");
		jQuery("#core-blog-title").removeClass('input-error');
		jQuery("#core-blog-publish-on-date").removeClass('input-error');
		jQuery("#core-blog-publish-on-time").removeClass('input-error');
		
		jQuery.post( "ajax.php?file=core/blog/create_post.php", {id:id, tags:tags, categories:categories, update:update, title:title, permalink:permalink, template:template, viewable:viewable, comments:comments, pingbacks:pingbacks, publishDate:publishDate, publishTime:publishTime, status:status, author:author}, function(data){
			if(data == "false"){
				jQuery(".core-blog-create").html((!update)?"Could Not Create Post":"Could Not Update Post");
				jQuery(".core-blog-create").addClass('input-error');
				setTimeout(function(){
					jQuery(".core-blog-create").html((!update)?"Create Post":"Update Post");
					jQuery(".core-blog-create").removeClass('input-error');						
				}, 2000);
			}else{
				jQuery("#core-blog-permalink").html(data);
				jQuery(".core-blog-create").html((!update)?"Post Created":"Post Updated");
				setTimeout(function(){
					jQuery(".core-blog-create").html((!update)?"Create Post":"Update Post");						
				}, 1000);
				//core_blog.get_templates();
				core_blog.get_sidebar();
			}
		});
	}
});

//When Options/Settings is Clicked, get info
jQuery(".core-blog-sidebar-title-menu .options").live("click", function(event){
	var id = jQuery(this).parents(".core-blog-fl-pg-title:first").attr("rel");
	core_blog.get_categories(id);
	core_blog.get_tags(id);
	core_blog.get_revisions(id);
	jQuery.post("ajax.php?file=core/blog/get_info.php", {id:id}, function(data){
		if(data == "false"){ return true; }
		var d = jQuery.parseJSON(data);
		jQuery("#core-blog-title").val(d.title);
		jQuery("#core-blog-permalink").html(d.guid);
		jQuery("#core-blog-templates-select").load("ajax.php?file=core/blog/get_templates.php", function(){
			jQuery("#core-blog-templates-select select").val(d.template);																									   
		});
		jQuery("#core-blog-viewable-by").load("ajax.php?file=core/blog/groups.php", function(){
			jQuery("#core-blog-viewable-by").val(d.viewable_by);																					   
		});
		if(d.allow_comments == 0){ jQuery("#core-blog-comments").removeAttr("checked"); }else{ jQuery("#core-blog-comments").attr("checked", "true"); }
		if(d.allow_pingbacks == 0){ jQuery("#core-blog-pingbacks").removeAttr("checked"); }else{ jQuery("#core-blog-pingbacks").attr("checked", "true"); }
		var publish = d.publish_on.split(" ");
		jQuery("#core-blog-publish-on-date").val(publish[0]);
		jQuery("#core-blog-publish-on-time").val(publish[1]);
		jQuery("#core-blog-status").val(d.status);
		jQuery("#core-blog-author").load("ajax.php?file=core/blog/users.php", function(){
			jQuery("#core-blog-author").val(d.author);																				 
		});
		jQuery("#core-blog-post-id").val(id);
		jQuery("#core-blog-create").html("Update Post");
		jQuery("#core-blog-create").addClass("update");
		jQuery("#core-blog-new-post-dialog").dialog('open');
	});
});

//Handle post Delete
jQuery(".core-blog-sidebar-title-menu .delete").live("click", function(event){
	var id = jQuery(this).parents(".core-blog-fl-pg-title:first").attr("rel");
	jQuery.yesNo({
		message:	'Are you sure you want to delete this item and all it\'s children?',
		yes:		function(){
						jQuery.post("ajax.php?file=core/blog/delete.php", {id:id}, function(data){
							core_blog.get_sidebar();	
						});
					}
	});
});

//Sidebar hover menu. Move menu to location of title even if scrolled
jQuery(".core-blog-fl-pg-title").live("mousemove", function(event){
	jQuery(".core-blog-sidebar-title-menu", this).offset({top:jQuery(this).offset().top, left:jQuery(this).offset().left-120});
});


//Handle Sidebar post menu
jQuery(".core-blog-sidebar-title-menu .edit").live("click", function(){
	var p = jQuery(this).parents(".core-blog-fl-pg-title:first");
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
		jQuery(bg.pages).append('<div class="bg-admin-page" rel="'+t+'"><iframe rel="'+t+'" dbid="'+p.attr("rel")+'" src="'+loc+'?noparse" onLoad="jQuery.iframeResize(this); core_blog.events(this);"></iframe></div>');
		jQuery("#bg-admin-bottom-bar-col-one").append('<div id="" class="bg-admin-tab bg-admin-post" rel="'+t+'" alt="'+alt+'" dbid="'+p.attr("rel")+'">'+p.attr("dtitle")+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+alt+'</div><hr /><div class="refresh">Refresh</div><div class="close">Close</div><div class="save">Save</div></div></div></div>');
		
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
			//Don't allow gloabl objects to be changed from a post page
			jQuery(".template-variable.editable", context).each( function(index, el){
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
				
				var tvt = jQuery(el).attr("rel");
				data[tvt] = val;//jQuery(el).html();
			});
			jQuery.post("ajax.php?file=core/blog/save.php", {data:data, id:id}, function(data){
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
		
		//Handle post Tab Close
		jQuery(".bg-admin-tab[rel='"+t+"'] .close").live("click", function(){
			var rel = jQuery(this).parents(".bg-admin-tab:first").attr("rel");
			var p = jQuery(this).parents(".bg-admin-tab:first");
			var prev = p.prevAll(".bg-admin-tab:first");
			if(!prev.get(0)){ p.next().click(); }
			else{ prev.click(); }
			jQuery(this).parents(".bg-admin-tab:first").remove();
			jQuery(".bg-admin-page[rel='"+rel+"']").remove();
		});
		
		//Handle Refreshing of a post
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

//Function to trigger events when the post loads
core_blog.events = function(frame){
	jQuery(document).trigger('core_blog.loaded', [frame]);
	var context = frame.contentWindow.document;
	jQuery(".template-variable.editable", context).attr("contenteditable", "true");
}

/*Rethink drag and drop upload
//Function to make template variables editable on load in the admin
core_blog.tv_editable = function(frame){
	
	//Handle Drag and Drop Uploads
	jQuery(".template-variable", context).attr("save", bg.site+"/assets/media");
	jQuery(".template-variable", context).dragDropUpload({processor:"assets/js/dragDrop/upload.php" }, function(data){
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
	});
};*/



