// JavaScript Document

/*****************************************************
* jQuery.fn.getAttr(astext)
* get all of an elements attributes
* returns a key-value pair object of all the
* attributes of an element.  If a set, it takes
* the last element.
* if 'astext' is set, then it will return as a string.
******************************************************/
(function( $ ){
	$.fn.getAllAttr = function(astext){
		var obj = undefined;
		jQuery(this).each( function(){
			if(this.attributes.length > 0){
				var t='';
				if(!astext){ t = "{"; }
				for(var i=0; i<this.attributes.length; i++){
					var attr = this.attributes[i];
					var av = attr.nodeValue.replace(/"/igm, "'");
					if(astext){ t=t+attr.nodeName+'="'+av+'" '; }
					else{
						t = t+'"'+attr.nodeName+'":"'+av+'",';
					}
				}
				if(t.length > 1){
					t = t.substring(0,t.length-1); //remove last comma or space
				}
				if(!astext){
					t = t+"}";
					obj = jQuery.parseJSON(t);
				}else{ obj = t };
			}						
		});
		return obj;
	}//End getAllAttr
})(jQuery);
