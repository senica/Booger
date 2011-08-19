// JavaScript Document

jQuery("#core-slideshow-admin-dialog").dialog({ width:500, autoOpen:false, modal:true });

//TV Toolbox Slideshow Button Press
jQuery("#core-toolbar-tv-tools-wrapper .slideshow").live("click", function(){
	jQuery("#core-slideshow-admin-dialog").dialog('open');	
});

jQuery("#core-slideshow-admin-dialog .image").live("click", function(){
	var slideshow = jQuery(this).parent().parent();
	slideshow.unbind('core-media-insert');
	core_media.insert = slideshow;
	slideshow.bind('core-media-insert', function(event, obj){
		slideshow.find(".images").append('<div style="padding:10px; margin:5px 0;" class="line-item"><div class="image-title" style="cursor:pointer; font-weight:bold">'+obj.url+'</div> <div style="display:none; padding:5px 10px;"><div><span style="display:inline-block; width:115px">Title:</span><input class="title" type="text" value="'+obj.title+'" /></div><div><span style="display:inline-block; width:115px">Description:</span><input class="description" type="text" value="'+obj.description+'" /></div></div> </div>');											 
	});
	jQuery("#core-media-dialog").dialog('open');
});

jQuery("#core-slideshow-admin-dialog .images .image-title").live("click", function(){
	jQuery(this).next().slideToggle();																				   
});

jQuery("#core-slideshow-admin-dialog .insert").live("click", function(){
	var dialog = jQuery("#core-slideshow-admin-dialog");
	var shortcode = '[core_slideshow {';
	shortcode = shortcode + '"id":"'+dialog.find(".id").val()+'", ';
	shortcode = shortcode + '"style":"'+dialog.find(".style").val()+'", ';
	shortcode = shortcode + '"theme":"'+dialog.find(".theme").val()+'", ';
	shortcode = shortcode + '"width":"'+dialog.find(".width").val()+'", ';
	shortcode = shortcode + '"height":"'+dialog.find(".height").val()+'", ';
	var images = '';
	var titles = '';
	var descriptions = '';
	jQuery("#core-slideshow-admin-dialog .line-item").each( function(index, el){
		images = images + '"'+jQuery(el).find(".image-title").html()+'",';
		titles = titles + '"'+jQuery(el).find(".title").val()+'",';
		descriptions = descriptions + '"'+jQuery(el).find(".description").val()+'",';
	});
	images = images.substr(0, images.length-1);
	titles = titles.substr(0, titles.length-1);
	descriptions = descriptions.substr(0, descriptions.length-1);
	shortcode = shortcode + '"images":['+images+'], ';
	shortcode = shortcode + '"titles":['+titles+'], ';
	shortcode = shortcode + '"descriptions":['+descriptions+']';
	shortcode = shortcode + '}]';

	var range = core_toolbar.focus.range;
	var size = range.toString().length;
	var extract = range.extractContents();
	var el = core_toolbar.focus.doc.createTextNode(shortcode);
	range.insertNode(el);
});
