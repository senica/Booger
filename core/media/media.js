// JavaScript Document
//This page contains a lot of duplicate information.  Can we tidy up?
var core_media = {};

//Whatever elements is assigned here will be dispatched the core-media-insert trigger when insert is pushed from the media manager
core_media.insert;

jQuery("#core-media-dialog").dialog({width:600, modal:true, autoOpen:false});
jQuery("#core-media-tabs").tabs({selected:0});

//Toolbox Media Button Press
jQuery("#core-toolbar-tv-tools-wrapper .media").live("click", function(){
	jQuery("#core-media-dialog").dialog('open');
	//Handle media manager insert
	var tv = core_toolbar.focus.tv;
	jQuery(tv).unbind('core-media-insert');  //Unbind any outstanding event watches
	core_media.insert = tv; //Assign this tv the focus for the core-media-insert trigger
	jQuery(tv).bind('core-media-insert', function(event, obj){
		var range = core_toolbar.focus.range;
		var extract = range.extractContents();
		if(obj.type == 'image/jpeg' || obj.type == 'image/png' || obj.type == 'image/gif'){
			var el = core_toolbar.focus.doc.createElement("img");
			el.src = obj.url;
			range.insertNode(el);
		}else{
			var el=core_toolbar.focus.doc.createElement("div");
			range.insertNode(el);
			jQuery(el).addClass("download-wrapper");
			jQuery(el).html('<a class="link" href="'+obj.url+'" target="_blank"><div class="image"></div><div class="title">'+obj.title+'</div><div class="description">'+obj.description+'</div></a><div class="clearfix"></div>');
		}
		//Handle other types here
	});
	
	//Get Page Gallery
	jQuery("#core-media-tab-gallery .refresh").show();
	jQuery("#core-media-tab-gallery .refresh").click(); //Always click for new pages		
});


jQuery("#core-media-tabs").bind("tabsselect", function(event, ui){
	var tab = ui.tab;
	if(tab.hash == "#core-media-tab-images"){
		if(jQuery("#core-media-images-wrapper").html() == ""){
			jQuery("#core-media-tab-images .refresh").click();			
		}
	}
	if(tab.hash == "#core-media-tab-other"){
		if(jQuery("#core-media-other-wrapper").html() == ""){
			jQuery("#core-media-tab-other .refresh").click();			
		}
	}
});



//OTHER GALLERY
jQuery("#core-media-tab-other .refresh").click(function(){
	jQuery("#core-media-other-wrapper").unbind(".media_other");
	jQuery("#core-media-other-wrapper").html("");
	jQuery.get("ajax.php?file=core/media/get_images.php&offset=0&limit=10&type=upload", function(html){
		jQuery("#core-media-other-wrapper").append(html);
		var load_images = function(offset, limit){
			var offset = offset + limit;
			jQuery("#core-media-other-wrapper").bind("scroll.media_other", function(event){
				if(jQuery(this).height() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
					jQuery("#core-media-other-wrapper").unbind(".media_other"); //Wait till request set loads before listening to scroll event again
					jQuery.get("ajax.php?file=core/media/get_images.php&offset="+offset+"&limit="+limit+"&type=upload", function(html){
						jQuery("#core-media-other-wrapper").append(html);
						load_images(offset, limit); //Rebind scrolling listener
					});	
				}												  
			});
		}; load_images(0, 10);
	});													 
});

jQuery("#core-media-other-wrapper .insert").live("click", function(){
	var el = jQuery(this).parent().parent();
	var data = {};
	data.dbid = el.attr("dbid");
	data.type = el.attr("mime");
	var file = el.attr("file");
	data.title = el.attr("title");
	data.description = el.attr("description");
	data.file = file;
	data.save = bg.site+'/media/uploads/'+file;
	data.url_path = bg.url+'/media/uploads';
	data.url = data.url_path+'/'+file;
	jQuery(core_media.insert).trigger("core-media-insert", [data]); //Notify listeners of the insert push	
});

jQuery("#core-media-other-wrapper .delete").live("click", function(){
	var button = jQuery(this);
	button.html("Deleting...");
	var el = button.parent().parent();
	var id = el.attr("dbid");
	var file = el.attr("file");
	jQuery.post("ajax.php?file=core/media/delete.php", {id:id, file:file, type:'image'}, function(data){
		if(data == "true"){
			el.remove();	
		}else{
			button.html("Error");
			setTimeout(function(){button.html("Delete")}, 2000);
		}
	});
});

jQuery("#core-media-other-wrapper .edit").live("click", function(){
	var parent = jQuery(this).parent().parent();
	var ec = jQuery(".edit-container:first", parent);
	ec.slideDown();
	var edit_button = jQuery(this);
	edit_button.hide();
	if(ec.html() != ""){ return false; } //Don't do anything else since it's all event handlers
	var data = {};
	data.save = bg.site+'/media/images/';
	data.file = parent.attr("file");
	data.dbid = parent.attr("dbid");
	data.type = parent.attr("mime");
	data.title = parent.attr("title");
	data.description = parent.attr("description");	
	//ec.slideDown();
	jQuery.post("ajax.php?file=core/media/get_edit.php", {data:data}, function(json){
		var json = jQuery.parseJSON(json);
		var lock = true;
		var orig_width = json.orig_width;
		var orig_height = json.orig_height;
		ec.html(json.edit_content);
		ec.slideDown();
		ec.find("#core-media-resize-lock").click( function(){
			if(!lock){
				jQuery(this).css({'background-position':'0px -20px'});
				lock = true;	
			}else{
				jQuery(this).css({'background-position':'0px 0px'});
				lock = false;	
			}
		});
		ec.find(".width:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_width / val;
			if(lock == true){ ec.find(".height:first").val(Math.round(orig_height/re)); }
		});
		ec.find(".height:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_height / val;
			if(lock == true){ ec.find(".width:first").val(Math.round(orig_width/re)); }
		});
		ec.find(".cancel:first").click( function(){
			ec.slideUp();
			edit_button.show();
		});
		ec.find(".save:first").click( function(){
			var save_button = jQuery(this);
			save_button.html("Saving....");
			var new_width=ec.find(".width:first").val();
			var new_height=ec.find(".height:first").val();
			var isthumb = (ec.find(".is-thumb:first").is(":checked")) ? true : false; //Check whether this image should be the page thumbnail
			var title = ec.find(".title:first").val();
			var desc = ec.find(".desc:first").val();
			jQuery.post("ajax.php?file=core/media/save_changes.php", {docid:core_toolbar.focus.dbid, title:title, desc:desc, isthumb:isthumb, lock:lock, width:new_width, height:new_height, data:data}, function(json){
				var obj = jQuery.parseJSON(json);
				if(obj.message == "noerror"){									
					if(obj.dbid != data.dbid){ //If an image is resized, add a new line, don't reassign variables
						parent.after(
							'<div dbid="'+obj.dbid+'" file="'+obj.file+'" mime="'+obj.type+'" title="'+title+'" description="'+desc+'" style="margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:10px;">'
							+'<img src="'+bg.url+'/media/images/thumbs/'+obj.thumb+'" style="max-width:30px; max-height:30px; float:left; margin-right:15px" />'
							+'<div class="display-title">&nbsp;'+title+'</div>'
							+'<div><div class="button delete">Delete</div> <div class="button edit">Edit</div> <div class="button insert">Insert</div></div>'
							+'<div style="width:100%; clear:both;"></div>'
							+'<div class="edit-container"></div>'
						+'</div>');	
						//reset original values since new db entry is made
						ec.find(".title:first").val(data.title);
						ec.find(".desc:first").val(data.description);
					}else{ //If image is the same, reassign data object
						jQuery.extend(data, obj); //Overwrite current properties of data with new values if any								
						data.title = title;
						data.description = desc;
						data.url_path =  data.save.replace(bg.site, bg.url);
						data.url = data.url_path+'/'+data.file;
						
						//Assign new url
						ec.find(".width:first").val(obj.width);
						ec.find(".height:first").val(obj.height);
						jQuery(".display-title:first", parent).html(data.title);
					}
			
					save_button.html("Saved!");
					ec.slideUp();
					edit_button.show();
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}else{
					save_button.html("Error");
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}
			});
		});
	});
});	




//PAGE GALLERY
jQuery("#core-media-tab-gallery .refresh").click(function(){
	jQuery("#core-media-gallery-wrapper").unbind(".media_images");
	if(core_toolbar.focus.dbid === undefined){ jQuery("#core-media-gallery-wrapper").html('<h2>No Page Is Opened.</h2>'); return false; } 
	jQuery("#core-media-gallery-wrapper").html("");
	jQuery.get("ajax.php?file=core/media/get_images.php&offset=0&limit=10&id="+core_toolbar.focus.dbid, function(html){
		jQuery("#core-media-gallery-wrapper").append(html);
		var load_images = function(offset, limit){
			var offset = offset + limit;
			jQuery("#core-media-gallery-wrapper").bind("scroll.media_images", function(event){
				if(jQuery(this).height() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
					jQuery("#core-media-gallery-wrapper").unbind(".media_images"); //Wait till request set loads before listening to scroll event again
					jQuery.get("ajax.php?file=core/media/get_images.php&offset="+offset+"&limit="+limit+"&id="+core_toolbar.focus.dbid, function(html){
						jQuery("#core-media-gallery-wrapper").append(html);
						load_images(offset, limit); //Rebind scrolling listener
					});	
				}												  
			});
		}; load_images(0, 10);
	});													 
});

jQuery("#core-media-gallery-wrapper .insert").live("click", function(){
	var el = jQuery(this).parent().parent();
	var data = {};
	data.dbid = el.attr("dbid");
	data.type = el.attr("mime");
	var file = el.attr("file");
	data.title = el.attr("title");
	data.description = el.attr("description");
	data.file = file;
	data.save = bg.site+'/media/images/'+file;
	data.url = bg.url+'/media/images/'+file;
	jQuery(core_media.insert).trigger("core-media-insert", [data]); //Notify listeners of the insert push	
});

jQuery("#core-media-gallery-wrapper .delete").live("click", function(){
	var button = jQuery(this);
	button.html("Deleting...");
	var el = button.parent().parent();
	var id = el.attr("dbid");
	var file = el.attr("file");
	jQuery.post("ajax.php?file=core/media/delete.php", {id:id, file:file, type:'image'}, function(data){
		if(data == "true"){
			el.remove();	
		}else{
			button.html("Error");
			setTimeout(function(){button.html("Delete")}, 2000);
		}
	});
});

jQuery("#core-media-gallery-wrapper .edit").live("click", function(){
	var parent = jQuery(this).parent().parent();
	var ec = jQuery(".edit-container:first", parent);
	ec.slideDown();
	var edit_button = jQuery(this);
	edit_button.hide();
	if(ec.html() != ""){ return false; } //Don't do anything else since it's all event handlers
	var data = {};
	data.save = bg.site+'/media/images/';
	data.file = parent.attr("file");
	data.dbid = parent.attr("dbid");
	data.type = parent.attr("mime");
	data.title = parent.attr("title");
	data.description = parent.attr("description");	
	//ec.slideDown();
	jQuery.post("ajax.php?file=core/media/get_edit.php", {data:data}, function(json){
		var json = jQuery.parseJSON(json);
		var lock = true;
		var orig_width = json.orig_width;
		var orig_height = json.orig_height;
		ec.html(json.edit_content);
		ec.slideDown();
		ec.find("#core-media-resize-lock").click( function(){
			if(!lock){
				jQuery(this).css({'background-position':'0px -20px'});
				lock = true;	
			}else{
				jQuery(this).css({'background-position':'0px 0px'});
				lock = false;	
			}
		});
		ec.find(".width:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_width / val;
			if(lock == true){ ec.find(".height:first").val(Math.round(orig_height/re)); }
		});
		ec.find(".height:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_height / val;
			if(lock == true){ ec.find(".width:first").val(Math.round(orig_width/re)); }
		});
		ec.find(".cancel:first").click( function(){
			ec.slideUp();
			edit_button.show();
		});
		ec.find(".save:first").click( function(){
			var save_button = jQuery(this);
			save_button.html("Saving....");
			var new_width=ec.find(".width:first").val();
			var new_height=ec.find(".height:first").val();
			var isthumb = (ec.find(".is-thumb:first").is(":checked")) ? true : false; //Check whether this image should be the page thumbnail
			var title = ec.find(".title:first").val();
			var desc = ec.find(".desc:first").val();
			jQuery.post("ajax.php?file=core/media/save_changes.php", {docid:core_toolbar.focus.dbid, title:title, desc:desc, isthumb:isthumb, lock:lock, width:new_width, height:new_height, data:data}, function(json){
				var obj = jQuery.parseJSON(json);
				if(obj.message == "noerror"){									
					if(obj.dbid != data.dbid){ //If an image is resized, add a new line, don't reassign variables
						parent.after(
							'<div dbid="'+obj.dbid+'" file="'+obj.file+'" mime="'+obj.type+'" title="'+title+'" description="'+desc+'" style="margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:10px;">'
							+'<img src="'+bg.url+'/media/images/thumbs/'+obj.thumb+'" style="max-width:30px; max-height:30px; float:left; margin-right:15px" />'
							+'<div class="display-title">&nbsp;'+title+'</div>'
							+'<div><div class="button delete">Delete</div> <div class="button edit">Edit</div> <div class="button insert">Insert</div></div>'
							+'<div style="width:100%; clear:both;"></div>'
							+'<div class="edit-container"></div>'
						+'</div>');	
						//reset original values since new db entry is made
						ec.find(".title:first").val(data.title);
						ec.find(".desc:first").val(data.description);
					}else{ //If image is the same, reassign data object
						jQuery.extend(data, obj); //Overwrite current properties of data with new values if any								
						data.title = title;
						data.description = desc;
						data.url_path =  data.save.replace(bg.site, bg.url);
						data.url = data.url_path+'/'+data.file;
						
						//Assign new url
						ec.find(".width:first").val(obj.width);
						ec.find(".height:first").val(obj.height);
						jQuery(".display-title:first", parent).html(data.title);
					}
			
					save_button.html("Saved!");
					ec.slideUp();
					edit_button.show();
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}else{
					save_button.html("Error");
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}
			});
		});
	});
});








//IMAGES GALLERY
jQuery("#core-media-tab-images .refresh").click(function(){
	jQuery("#core-media-images-wrapper").unbind(".media_images");
	jQuery("#core-media-images-wrapper").html("");
	jQuery.get("ajax.php?file=core/media/get_images.php&offset=0&limit=10", function(html){
		jQuery("#core-media-images-wrapper").append(html);
		var load_images = function(offset, limit){
			var offset = offset + limit;
			jQuery("#core-media-images-wrapper").bind("scroll.media_images", function(event){
				if(jQuery(this).height() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
					jQuery("#core-media-images-wrapper").unbind(".media_images"); //Wait till request set loads before listening to scroll event again
					jQuery.get("ajax.php?file=core/media/get_images.php&offset="+offset+"&limit="+limit, function(html){
						jQuery("#core-media-images-wrapper").append(html);
						load_images(offset, limit); //Rebind scrolling listener
					});	
				}												  
			});
		}; load_images(0, 10);
	});													 
});

jQuery("#core-media-images-wrapper .insert").live("click", function(){
	var el = jQuery(this).parent().parent();
	var data = {};
	data.dbid = el.attr("dbid");
	data.type = el.attr("mime");
	var file = el.attr("file");
	data.title = el.attr("title");
	data.description = el.attr("description");
	data.file = file;
	data.save = bg.site+'/media/images/'+file;
	data.url_path = bg.url+'/media/images';
	data.url = data.url_path+'/'+file;
	jQuery(core_media.insert).trigger("core-media-insert", [data]); //Notify listeners of the insert push	
});

jQuery("#core-media-images-wrapper .delete").live("click", function(){
	var button = jQuery(this);
	button.html("Deleting...");
	var el = button.parent().parent();
	var id = el.attr("dbid");
	var file = el.attr("file");
	jQuery.post("ajax.php?file=core/media/delete.php", {id:id, file:file, type:'image'}, function(data){
		if(data == "true"){
			el.remove();	
		}else{
			button.html("Error");
			setTimeout(function(){button.html("Delete")}, 2000);
		}
	});
});

jQuery("#core-media-images-wrapper .edit").live("click", function(){
	var parent = jQuery(this).parent().parent();
	var ec = jQuery(".edit-container:first", parent);
	ec.slideDown();
	var edit_button = jQuery(this);
	edit_button.hide();
	if(ec.html() != ""){ return false; } //Don't do anything else since it's all event handlers
	var data = {};
	data.save = bg.site+'/media/images/';
	data.file = parent.attr("file");
	data.dbid = parent.attr("dbid");
	data.type = parent.attr("mime");
	data.title = parent.attr("title");
	data.description = parent.attr("description");	
	//ec.slideDown();
	jQuery.post("ajax.php?file=core/media/get_edit.php", {data:data}, function(json){
		var json = jQuery.parseJSON(json);
		var lock = true;
		var orig_width = json.orig_width;
		var orig_height = json.orig_height;
		ec.html(json.edit_content);
		ec.slideDown();
		ec.find("#core-media-resize-lock").click( function(){
			if(!lock){
				jQuery(this).css({'background-position':'0px -20px'});
				lock = true;	
			}else{
				jQuery(this).css({'background-position':'0px 0px'});
				lock = false;	
			}
		});
		ec.find(".width:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_width / val;
			if(lock == true){ ec.find(".height:first").val(Math.round(orig_height/re)); }
		});
		ec.find(".height:first").keyup( function(event){
			var val = jQuery(this).val();
			val = val.replace(/[^0-9]/g, '');
			jQuery(this).val(val);
			var re = orig_height / val;
			if(lock == true){ ec.find(".width:first").val(Math.round(orig_width/re)); }
		});
		ec.find(".cancel:first").click( function(){
			ec.slideUp();
			edit_button.show();
		});
		ec.find(".save:first").click( function(){
			var save_button = jQuery(this);
			save_button.html("Saving....");
			var new_width=ec.find(".width:first").val();
			var new_height=ec.find(".height:first").val();
			var isthumb = (ec.find(".is-thumb:first").is(":checked")) ? true : false; //Check whether this image should be the page thumbnail
			var title = ec.find(".title:first").val();
			var desc = ec.find(".desc:first").val();
			jQuery.post("ajax.php?file=core/media/save_changes.php", {docid:core_toolbar.focus.dbid, title:title, desc:desc, isthumb:isthumb, lock:lock, width:new_width, height:new_height, data:data}, function(json){
				var obj = jQuery.parseJSON(json);
				if(obj.message == "noerror"){									
					if(obj.dbid != data.dbid){ //If an image is resized, add a new line, don't reassign variables
						parent.after(
							'<div dbid="'+obj.dbid+'" file="'+obj.file+'" mime="'+obj.type+'" title="'+title+'" description="'+desc+'" style="margin-bottom:10px; border-bottom:1px solid #ccc; padding-bottom:10px;">'
							+'<img src="'+bg.url+'/media/images/thumbs/'+obj.thumb+'" style="max-width:30px; max-height:30px; float:left; margin-right:15px" />'
							+'<div class="display-title">&nbsp;'+title+'</div>'
							+'<div><div class="button delete">Delete</div> <div class="button edit">Edit</div> <div class="button insert">Insert</div></div>'
							+'<div style="width:100%; clear:both;"></div>'
							+'<div class="edit-container"></div>'
						+'</div>');	
						//reset original values since new db entry is made
						ec.find(".title:first").val(data.title);
						ec.find(".desc:first").val(data.description);
					}else{ //If image is the same, reassign data object
						jQuery.extend(data, obj); //Overwrite current properties of data with new values if any								
						data.title = title;
						data.description = desc;
						data.url_path =  data.save.replace(bg.site, bg.url);
						data.url = data.url_path+'/'+data.file;
						
						//Assign new url
						ec.find(".width:first").val(obj.width);
						ec.find(".height:first").val(obj.height);
						jQuery(".display-title:first", parent).html(data.title);
					}
			
					save_button.html("Saved!");
					ec.slideUp();
					edit_button.show();
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}else{
					save_button.html("Error");
					setTimeout(function(){ save_button.html("Save"); }, 2000);
				}
			});
		});
	});
});	

//Uploads
core_media.upload = function(){
	var t = Date.now();
	jQuery("#core-media-upload-wrapper").prepend('<div style="border-bottom:1px solid #ccc; padding-bottom:7px;"><div id="core-media-upload-'+t+'"></div><div id="core-media-progress-'+t+'" style="font-size:small;"></div></div><div><br /></div>');
	jQuery("#core-media-upload-"+t).upload({
		save:bg.site+'/media',
		progress: function(bytes,size,data){
			jQuery("#core-media-progress-"+t).html(bytes+" bytes of "+size+" written");		
		},
		complete: function(bytes, data){
			if(data.error == 'nowrite'){
				jQuery("#core-media-progress-"+t).html("Error: Your file could not be written to.");
				return false;
			}
			data.url_path = data.save.replace(bg.site, bg.url);
			data.url = data.url_path+'/'+data.file;
			data.gallery_id = (!core_toolbar || !core_toolbar.focus || !core_toolbar.focus.dbid) ? 0 : core_toolbar.focus.dbid;
			data.width = 0; //init
			data.height = 0; //init
			
			var container = jQuery(this).parent();			
			container.append('<div id="core-media-edit-'+t+'" style="margin:10px 0;" class="button">Loading...</div> <div id="core-media-insert-'+t+'" style="margin:10px 0;" class="button">Insert</div><div id="core-media-edit-container-'+t+'" style="display:none;">Loading...</div>');
			var ec = jQuery("#core-media-edit-container-"+t);
			var edit_button = jQuery("#core-media-edit-"+t);
			
			//Insert into db and create thumbnail if applicable
			jQuery.post("ajax.php?file=core/media/db_insert.php", {obj:data}, function(response){
				var json = jQuery.parseJSON(response);
				data.width = json.width;
				data.height = json.height; 
				data.dbid = json.dbid; //Assign DB Insert
				if(jQuery("#core-media-edit-container-"+t).get(0)){
					jQuery("#core-media-edit-container-"+t).find(".tempthumb").attr("src", data.url_path+"/thumbs/"+data.file);
				}
				jQuery("#core-media-edit-"+t).html('Edit');
			});

			//Handle Insert
			jQuery("#core-media-insert-"+t).click( function(){
				jQuery(core_media.insert).trigger("core-media-insert", [data]); //Notify listeners of the insert push	
			});
				
			//Handle Edit
			jQuery("#core-media-edit-"+t).click( function(){
				edit_button.hide();
				ec.slideDown();												  
			}).one("click", function(){ //Only get edit once
				jQuery.post("ajax.php?file=core/media/get_edit.php", {data:data}, function(json){
					var json = jQuery.parseJSON(json);
					var lock = true;
					var orig_width = json.orig_width;
					var orig_height = json.orig_height;
					ec.html(json.edit_content);
					ec.slideDown();
					ec.find("#core-media-resize-lock").click( function(){
						if(!lock){
							jQuery(this).css({'background-position':'0px -20px'});
							lock = true;	
						}else{
							jQuery(this).css({'background-position':'0px 0px'});
							lock = false;	
						}
					});
					ec.find(".width:first").keyup( function(event){
						var val = jQuery(this).val();
						val = val.replace(/[^0-9]/g, '');
						jQuery(this).val(val);
						var re = orig_width / val;
						if(lock == true){ ec.find(".height:first").val(Math.round(orig_height/re)); }
					});
					ec.find(".height:first").keyup( function(event){
						var val = jQuery(this).val();
						val = val.replace(/[^0-9]/g, '');
						jQuery(this).val(val);
						var re = orig_height / val;
						if(lock == true){ ec.find(".width:first").val(Math.round(orig_width/re)); }
					});
					ec.find(".cancel:first").click( function(){
						ec.slideUp();
						edit_button.show();
					});
					ec.find(".save:first").click( function(){
						var save_button = jQuery(this);
						save_button.html("Saving....");
						var new_width=ec.find(".width:first").val();
						var new_height=ec.find(".height:first").val();
						var isthumb = (ec.find(".is-thumb:first").is(":checked")) ? true : false; //Check whether this image should be the page thumbnail
						var title = ec.find(".title:first").val();
						var desc = ec.find(".desc:first").val();
						jQuery.post("ajax.php?file=core/media/save_changes.php", {docid:core_toolbar.focus.dbid, title:title, desc:desc, isthumb:isthumb, lock:lock, width:new_width, height:new_height, data:data}, function(json){
							var obj = jQuery.parseJSON(json);
							if(obj.message == "noerror"){
								jQuery.extend(data, obj); //Overwrite current properties of data with new values if any
								
								data.title = title;
								data.description = desc;
								data.url = data.url_path+'/'+data.file;
								
								container.find(".upload-title:first").html(data.title);
								ec.find(".width:first").val(data.width);
								ec.find(".height:first").val(data.width);
								save_button.html("Saved!");
								ec.slideUp();
								edit_button.show();
								setTimeout(function(){ save_button.html("Save"); }, 2000);
							}else{
								save_button.html("Error");
								setTimeout(function(){ save_button.html("Save"); }, 2000);
							}
						});
					});
				});
			});
		},
		before: function(){
			core_media.upload();
		}
	});
};
core_media.upload();

