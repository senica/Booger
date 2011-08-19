// JavaScript Document

//Functions
var core_files = {};

//Load files on File click only
jQuery("#core-files-sidebar-title").one("click", function(){
	jQuery("#core-files-child").progressLoad("/ajax.php?file=core/files/get_files.php", function(data){
		jQuery(".core-files-sidebar-loading progress").remove();
		core_files.drag(jQuery(".core-files-title-text[label='dir']"));	//Add drag and drop to file list
	}, function(percent){
		jQuery(".core-files-sidebar-loading progress").val(percent);	
	});
});

core_files.rename = function(el){
	var p = jQuery(el).parents(".core-files-title-text:first");
	p.append('<input type="text" class="core-files-rename-input" value="'+p.attr("title")+'" />');
	jQuery(".core-files-rename-input:first").focus().css({width:p.width()+'px', height:p.height()+'px'}).bind("keydown", function(event){
		if(event.keyCode != 13){ return true; }
		var el = jQuery(this);
		var val = el.val();
		var file = el.parent().attr("file");
		var title = el.parent().attr("title");
		jQuery.post("/ajax.php?file=core/files/rename.php", {val:val, file:file}, function(data){
			if(data == 'true'){
				//Make changes in element attributes
				el.parent().attr("file", file.replace(title, val) );
				el.parent().attr("save", file.replace(title, val) );
				var loc = el.parent().attr("loc");
				el.parent().attr("loc", loc.replace(title, val) );
				var alt = el.parent().attr("alt");
				el.parent().attr("alt", alt.replace(title, val) );
				el.parent().attr("title", val);				
				el.parent().find("span:first").html(val);
				el.unbind(); //remove keydown
				el.parent().dragDropUpload({kill:true}, function(){
					core_files.drag(el.parent()); //assign drag and drop												 
				});				
				
				el.remove(); //remove input
			}else{
				el.unbind();
				el.remove();
				alert("Could not rename file.");
			}
		});																												  
	});		
}

//Hide and Show Children files when a directory is clicked
jQuery(".core-files-parent").live("click", function(event){
	var cls = jQuery(event.target).attr("class");
	if(cls != "core-files-rename" && cls != "core-files-new-folder" && cls != "core-files-new-file" && cls != "core-files-delete"){
		event.stopPropagation();
		jQuery(".core-files-child:first", this).toggle();
	}
});

//Sidebar hover menu. Move menu to location of title even if scrolled
jQuery(".core-files-title-text").live("mousemove", function(event){
	jQuery(".core-files-sidebar-title-menu", this).offset({top:jQuery(this).offset().top-5, left:jQuery(this).offset().left-145});
});

//Handle Drag and Drop Uploads
core_files.drag = function(el){
	el.dragDropUpload({
			processor:"assets/js/dragDrop/upload.php",
			ondrop:	function(){
						jQuery(".core-files-storage").attr("title", "Uploading").html('Uploading your file.  Please wait.').dialog();  
					},
			complete: function(data){
						if(data.msg == "true"){
							jQuery(".core-files-storage").attr("title", "Your file has been uploaded").html('<a href="'+data.url+'" target="_blank">'+data.name+'</a>').dialog({width:500});																													
							var t = Date.now();
							var el = jQuery(this);
							var p =  el.parents(".core-files-title-text:first");
							var type = p.attr("label");
							var dir = p.attr("file");
							var menu = '<div class="core-files-sidebar-title-menu"><div class="core-files-sidebar-title-menu-content"><div class="arrow"></div>'+((type=='file')?'<div class="open">Open</div>':'')+'<div class="folder">New Folder</div><div class="file">New File</div><div class="rename">Rename</div><hr /><div class="delete">Delete</div><hr /><div style="font-size:small; white-space:normal">Double-click to open</div></div></div>';
							el.parents(".core-files-parent:first").find(".core-files-child:first").append('<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="./core/files/images/file.png" /></span><span class="core-files-title-text" rel="'+t+'" label="file" title="'+data.name+'" loc="'+data.url+'" alt="'+data.url+'" file="'+data.loc+'"><span>'+data.name+'</span>'+menu+'</span></div><div class="core-files-child"></div></div>');	
							el.parents(".core-files-parent:first").find(".core-files-child:first").show(); //Expand parent folder
						}else{
							jQuery(".core-files-storage").attr("title", "Error!").html('There was a problem uploading your file');	
						}
					}
		});
}

//Handle Rename
jQuery(".core-files-sidebar-title-menu-content .rename").live("click", function(event){
	core_files.rename(this);
});
		
//Handle New Folder	
//When new folder is clicked from the context menu, create new folder, add new div, input box, remove context, resize input, add drag and drop capabilities
jQuery(".core-files-sidebar-title-menu-content .folder").live("click", function(event){
		var t = Date.now();
		var el = jQuery(event.target);
		var p = el.parents(".core-files-title-text:first");
		var type = p.attr("label");
		var dir = p.attr("file");
		jQuery.post("/ajax.php?file=core/files/new.php", {dir:dir, type:'folder'}, function(data){
			if(data == "false"){
				alert("Could not create new directory");	
			}else{
				var menu = '<div class="core-files-sidebar-title-menu"><div class="core-files-sidebar-title-menu-content"><div class="arrow"></div>'+((type=='file')?'<div class="open">Open</div>':'')+'<div class="folder">New Folder</div><div class="file">New File</div><div class="rename">Rename</div><hr /><div class="delete">Delete</div><hr /><div style="font-size:small; white-space:normal">Single-click to expand</div></div></div>';
				if(type == "dir"){
					el.parents(".core-files-parent:first").find(".core-files-child:first").append('<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="./core/files/images/folder.png" /></span><span class="core-files-title-text" rel="'+t+'" label="dir" title="New Folder" loc="" alt="" file=""><span>New Folder</span>'+menu+'</span></div><div class="core-files-child"></div></div>');	
					el.parents(".core-files-parent:first").find(".core-files-child:first").show(); //Expand parent folder
				}else if(type == "file"){
					el.parents(".core-files-parent:first").parents(".core-files-parent:first").find(".core-files-child:first").append('<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="./core/files/images/folder.png" /></span><span class="core-files-title-text" rel="'+t+'" label="dir" title="New Folder" loc="" alt="" file=""><span>New Folder</span>'+menu+'</span></div><div class="core-files-child"></div></div>');	
				}				
				//assign alt and loc attributes
				var new_el = jQuery(".core-files-title-text[rel='"+t+"']");
				new_el.attr("file", data); //Assign file location for renaming
				new_el.attr("save", data); //for drag and drop
				var parent_loc = new_el.parents(".core-files-parent:first").parents(".core-files-parent:first").find(".core-files-title-text:first").attr("loc");
				var new_title = new_el.attr("title");
				new_el.attr("loc", parent_loc+'/'+new_title); //Assign url location for renaming
				new_el.attr("alt", parent_loc+'/'+new_title); //Assign url location for renaming
				//core_files.drag(new_el); //Rename since files will get a new save property on rename
				core_files.rename(jQuery(".core-files-title-text[rel='"+t+"']:first span:first")); //Perform Rename
			}
		});
});

//Handle New File
//When new file is clicked from the context menu, create new file or folder, add new div, input box, remove context, resize input
jQuery(".core-files-sidebar-title-menu-content .file").live("click", function(event){
	var t = Date.now();
	var el = jQuery(event.target);
	var p =  el.parents(".core-files-title-text:first");
	var type = p.attr("label");
	var dir = p.attr("file");
	jQuery.post("/ajax.php?file=core/files/new.php", {dir:dir, type:'file'}, function(data){
		if(data == "false"){
			alert("Could not create new file.\r\nThere could be another file already named untitled.php.");	
		}else{
			var menu = '<div class="core-files-sidebar-title-menu"><div class="core-files-sidebar-title-menu-content"><div class="arrow"></div><div class="open">Open</div><div class="folder">New Folder</div><div class="file">New File</div><div class="rename">Rename</div><hr /><div class="delete">Delete</div><hr /><div style="font-size:small; white-space:normal">Double-click to open</div></div></div>';
			if(type == "dir"){
				el.parents(".core-files-parent:first").find(".core-files-child:first").append('<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="./core/files/images/file.png" /></span><span class="core-files-title-text" rel="'+t+'" label="file" title="untitled.php" loc="" alt="" file=""><span>untitled.php</span>'+menu+'</span></div><div class="core-files-child"></div></div>');	
				el.parents(".core-files-parent:first").find(".core-files-child:first").show(); //Expand parent folder
			}else if(type == "file"){
				el.parents(".core-files-parent:first").parents(".core-files-parent:first").find(".core-files-child:first").append('<div class="core-files-parent"><div class="core-files-title"><span class="core-files-img"><img src="./core/files/images/file.png" /></span><span class="core-files-title-text" rel="'+t+'" label="file" title="untitled.php" loc="" alt="" file=""><span>untitled.php</span>'+menu+'</span></div><div class="core-files-child"></div></div>');	
				//el.parents(".core-files-parent:first").parents(".core-files-parent:first").find(".core-files-child:first").show(); //Expand parent folder		
			}
			jQuery(".core-files-title-text[rel='"+t+"']").attr("file", data); //Assign file location for renaming
			
			//assign alt and loc attributes
			var parent_loc = jQuery(".core-files-title-text[rel='"+t+"']").parents(".core-files-parent:first").parents(".core-files-parent:first").find(".core-files-title-text:first").attr("loc");
			var new_title = jQuery(".core-files-title-text[rel='"+t+"']").attr("title");
			jQuery(".core-files-title-text[rel='"+t+"']").attr("loc", parent_loc+'/'+new_title); //Assign url location for renaming
			jQuery(".core-files-title-text[rel='"+t+"']").attr("alt", parent_loc+'/'+new_title); //Assign url location for renaming
			
			core_files.rename(jQuery(".core-files-title-text[rel='"+t+"']:first span:first")); //Perform Rename
		}
	});
});

//Handle Delete
//When delete is clicked from the context menu, delete file or folder, remove context
jQuery(".core-files-sidebar-title-menu-content .delete").live("click", function(event){
	var el = jQuery(event.target);
	var p = el.parents(".core-files-parent:first");
	var type = el.parents(".core-files-title-text:first").attr("label");
	var dir = el.parents(".core-files-title-text:first").attr("file");
	jQuery.yesNo({
		message:	'Are you sure you want to delete<br />'+dir+'?',
		yes:		function(){
						jQuery.post("/ajax.php?file=core/files/delete.php", {dir:dir, type:type}, function(data){
							if(data == "true"){
								p.remove(); //Remove div container				
							}else{
								alert("Could not remove file/folder.");	
							}
						});	
					}
	});
});


//When a file is clicked, check to see if the file is already open, open the file, add tab

//Need to clean up this section's comments, but left alone for now, because may want to add a view function, and it's already done in the comments.
//Wanted to see if I ever find myself actually needing to view the file.
jQuery(".core-files-sidebar-title-menu-content .open").live("click", function(){
	var t = Date.now();
	var p = jQuery(this).parents(".core-files-title-text:first");
	var file = p.attr("file");
	var z = false;
	jQuery(".bg-admin-tab").each( function(){
		if(jQuery(this).attr("file") == file){
			z = true;	
		}
	});
	if(z){
		jQuery(".bg-admin-tab[file='"+file+"']").click();
	}else{
		//var loc = p.attr("file");
		//jQuery(bg.pages).append('<div class="bg-admin-page" rel="'+t+'"><iframe rel="'+t+'" src="?guid='+loc+'" onLoad="iframeResize(this);"></iframe></div>');
		//jQuery("#bg-admin-bottom-bar-col-one").append('<div id="" class="bg-admin-tab" rel="'+t+'" alt="'+p.attr("alt")+'" file="'+p.attr("file")+'" title="'+p.attr("title")+'">'+p.attr("title")+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+p.attr("file")+'</div><hr /><div class="refresh">Refresh</div><div class="close">Close</div><div class="edit">Edit</div></div></div></div>');
		
		//When the edit button is clicked, add new tab with save, open new iframe with code
		//jQuery(".bg-admin-tab[rel='"+t+"'] .edit").live("click", function(){
			var t = Date.now();
			var content = '';
			//var p = jQuery(this).parents(".bg-admin-tab:first");
			//jQuery(".bg-admin-tab-save").hide(); //hide all saves
			jQuery(bg.pages).append('<div class="bg-admin-page" rel="'+t+'"><textarea id="edit'+t+'" rel="'+t+'">Loading...</textarea></div>');
			jQuery("#bg-admin-bottom-bar-col-one").append('<div class="bg-admin-tab bg-admin-tab-code-edit" rel="'+t+'" file="'+p.attr("file")+'">'+p.attr("title")+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+p.attr("file")+'</div><hr /><div class="close">Close</div><div class="save">Save</div></div></div></div>');
			//p.after('<div class="bg-admin-tab bg-admin-tab-code-edit" rel="'+t+'" file="'+p.attr("file")+'">'+p.attr("title")+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+p.attr("file")+'</div><hr /><div class="close">Close</div><div class="save">Save</div></div></div></div>');
			
			jQuery.post("/ajax.php?file=core/files/get_code.php", {file:p.attr("file")}, function(data){
				jQuery(".bg-admin-page[rel='"+t+"'] textarea").text(data);
				editAreaLoader.init({
					id : "edit"+t		// textarea id
					,syntax: "php"			// syntax to be uses for highgliting
					,start_highlight: true		// to display with highlight mode on start-up
					,min_width:document.width
					,min_height:document.height-28
					,allow_toggle: false
					,language: "en"
					,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, word_wrap"
					,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql"
					,word_wrap: true
					,is_multi_files: false
					,show_line_colors: true
					,replace_tab_by_spaces: false
					,plugins:"bgsave"
					//When CTRL+s is clicked, then save the file.
					,bg_ctrl_save:function(id, content){
						core_files.save(t);	
					}
				});
			});
			
			//Handle Edit Page Close
			jQuery(".bg-admin-tab[rel='"+t+"'] .close").live("click", function(){
				var rel = jQuery(this).parents(".bg-admin-tab:first").attr("rel");
				var p = jQuery(this).parents(".bg-admin-tab:first");
				var prev = p.prevAll(".bg-admin-tab:first");
				if(!prev.get(0)){ p.next().click(); }
				else{ prev.click(); }
				jQuery(this).parents(".bg-admin-tab:first").remove();
				jQuery(".bg-admin-page[rel='"+rel+"']").remove();
			});
			
			//When the save button is clicked...save the file, maybe ask to reload the file?
			jQuery(".bg-admin-tab[rel='"+t+"'] .save").live("click", function(){
				core_files.save(t);
			});
			
			//Handle Edit Tab Click
			jQuery(".bg-admin-tab[rel='"+t+"']").live("click", function(event){
				if(jQuery(event.target).hasClass("close")){ return true; }
				jQuery(".bg-admin-tab").removeClass("active");
				jQuery(this).addClass("active");
				jQuery(".bg-admin-page").removeClass("active");
				jQuery(".bg-admin-page[rel='"+t+"']").addClass("active");
			});
			
			//Init
			jQuery(".bg-admin-tab[rel='"+t+"']").click();
		//});//Edit Click
		
		//Handle File Tab Close
		/*jQuery(".bg-admin-tab[rel='"+t+"'] .close").live("click", function(){
			var rel = jQuery(this).parents(".bg-admin-tab:first").attr("rel");
			var p = jQuery(this).parents(".bg-admin-tab:first");
			var prev = p.prevAll(".bg-admin-tab:first");
			if(!prev.get(0)){ p.next().click(); }
			else{ prev.click(); }
			jQuery(this).parents(".bg-admin-tab:first").remove();
			jQuery(".bg-admin-page[rel='"+rel+"']").remove();
		});
		
		//Handle File Tab Refreshing of a Page
		jQuery(".bg-admin-tab[rel='"+t+"'] .refresh").live("click", function(){
			var t = jQuery(this).parents(".bg-admin-tab:first");
			var rel = t.attr("rel");
			var tv = jQuery("iframe[rel='"+rel+"']").get(0);
			tv.contentWindow.location.reload(true);
		});
		
		//Handle File Tab Click
		jQuery(".bg-admin-tab[rel='"+t+"']").live("click", function(event){
			if( jQuery(event.target).hasClass("close") || jQuery(event.target).hasClass("edit") ){ return true; }
			jQuery(".bg-admin-tab").removeClass("active");
			jQuery(this).addClass("active");
			jQuery(".bg-admin-page").removeClass("active");
			jQuery(".bg-admin-page[rel='"+t+"']").addClass("active");
		});
		
		//Init
		jQuery(".bg-admin-tab[rel='"+t+"']").click();*/
	}//else
});

//Function for saving files
core_files.save = function(time){
	var el = jQuery(".bg-admin-tab[rel='"+time+"']");
	var button = jQuery(".save", el);
	var file = el.attr("file");
	var rel = el.attr("rel");
	var code = editAreaLoader.getValue("edit"+time);
	button.html("Saving");
	jQuery.post("/ajax.php?file=core/files/save_code.php", {code:code, file:file}, function(data){
		if(data == "true"){
			button.html("Saved");			
		}else{
			button.html("Error Saving");
		}
		setTimeout( function(){
			button.html("Save");					 
		}, 1000);
	});	
};	



