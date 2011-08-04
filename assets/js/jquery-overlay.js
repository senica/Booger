// JavaScript Document
/*************************************************************************************
* jQuery.overlay({remove:true})
* Create an overlay for and element.  Used for Seemless dragging
**************************************************************************************/
(function( $ ){
	$.fn.overlay = function() {
		var a = arguments;
		jQuery(this).each( function(){
			var el = this;
			var opts = {};
			var func;
			jQuery.each(a, function(index, value){
				if(typeof value == "object"){
					opts = value;
				}else if(typeof value == "function"){
					func = value;
				}	
			});
			
			if(opts.remove == true){
				jQuery(".bg-admin-overlay", el).remove();	
			}else if(jQuery(".bg-admin-overlay:first", el).length == 0){
				jQuery(el).append('<div class="bg-admin-overlay" style="opacity:0; -moz-opacity:0; -webkit-opacity:0; width:100%; height:100%; background:#fff; position:fixed; top:0px; left:0px;"></div>');			
			}
			
			if(typeof func == "function"){
				func.call(el, jQuery(".bg-admin-overlay:first",el), jQuery);	
			}
		});
		return this;
	};
})(jQuery);