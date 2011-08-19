// JavaScript Document
/**************************************************
* When a selector is clicked, edit it as a page
* source: is the element that has the attributes
* rel, alt, dtitle that contain the id, guid, and
* title respectively.
* source is a function like so: 'source':function(){ return jQuery(this).parents(".core-categories-fl-cat-title:first"); },
* type: when the page is saved, what type to insert
* in the database
* content: whether content tvs should be made
* editable
* globals: whether globals tvs should be made
* editable
**************************************************/


(function( $ ){
	$.editAsPage = function(frame,content,globals) {
		jQuery(document).trigger('editAsPage.loaded', [frame]);
		var context = frame.contentWindow.document;
		if(content == true){ jQuery(".template-variable.editable", context).attr("contenteditable", "true"); }
		if(globals == true){ jQuery(".template-variable.global", context).attr("contenteditable", "true"); }
	};
})(jQuery);

(function( $ ){
	$.fn.editAsPage = function(options) {		
		return this.each( function(){
			var settings =	{
								'source':jQuery(this),
								'type'	:'page',
								'content':true,
								'globals':true
							};		
			if(options){ jQuery.extend(settings, options); }
			
			var el = jQuery(this);			
			el.unbind(".editAsPage");
			el.bind("click.editAsPage", function(event){
				var source	= settings.source.call(this);
				var id		= source.attr("rel");
				var guid	= source.attr("alt");
				var title	= source.attr("dtitle");
				



				var t = Date.now();
				var z = false;
				jQuery(".bg-admin-tab").each( function(){
					if(jQuery(this).attr("alt") == guid){
						z = true;	
					}
				});	
				if(z){
					jQuery(".bg-admin-tab[alt='"+guid+"']").click();	
				}else{
					var loc = bg.url+"/"+guid;
					jQuery(bg.pages).append('<div class="bg-admin-page" rel="'+t+'"><iframe rel="'+t+'" dbid="'+id+'" src="'+loc+'?noparse" onLoad="jQuery.iframeResize(this); jQuery.editAsPage(this, '+settings.content+', '+settings.globals+');"></iframe></div>');
					jQuery("#bg-admin-bottom-bar-col-one").append('<div id="" class="bg-admin-tab" rel="'+t+'" alt="'+guid+'" dbid="'+id+'">'+title+'<div class="bg-admin-tab-menu-wrapper"><div class="bg-admin-tab-menu"><div class="arrow"></div><div class="title">'+guid+'</div><hr /><div class="refresh">Refresh</div><div class="close">Close</div><div class="save">Save</div></div></div></div>');
					
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
							if(jQuery(el).hasClass("global")){
								var tvt = jQuery(el).attr("rel");
								globals[tvt] = clone.html();//jQuery(el).html();
							}else if(jQuery(el).hasClass("editable")){
								var tvt = jQuery(el).attr("rel");
								data[tvt] = clone.html(); //jQuery(el).html();
							}
							clone.remove();
						});
						jQuery.post("/ajax.php?file=assets/js/editAsPage/save.php", {globals:globals, data:data, id:id, type:settings.type}, function(data){
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
		});
	};
})(jQuery);
