// JavaScript Document
/*************************************************************************************
* jQuery(selector).htmlEdit()
* Adds simple html editing capabilities like tabs, auto-indent
**************************************************************************************/
(function( $ ){
	$.fn.htmlEdit = function() {
		jQuery(this).each( function(index, el){
			jQuery(el).unbind(".htmlEdit");
			jQuery(el).bind("keydown.htmlEdit", function(event){
				//When Enter is pushed, keep beginning tabs on next line.
				if(event.keyCode == 13){
					event.preventDefault();
					var val = jQuery(el).val();
					var start = jQuery(el).get(0).selectionStart;
					var tosearch = val.substring(0, start);
					var hold = tosearch;
					var tab = "";
					while(hold.length != 0){
						var last = hold.substring((hold.length-1));
						if(last == "\t"){ tab = tab+"\t"; }
						if(last == "\n"){ break; }
						hold = hold.substring(0, hold.length-1);
					}
					//If last line contained an element, but not the end of an element, auto-indent
					if(val.substring(start-1, start) == '>'){
						var hold = tosearch;
						var prev = '';
						while(hold.length != 0){
							var last = hold.substring((hold.length-1));
							if(last == '<'){ if(!prev.match('/')){tab=tab+"\t";} break; }
							prev = prev+last;
							hold = hold.substring(0, hold.length-1);
						}		
					}
					var newval = tosearch+"\n"+tab+val.substring(start, val.length);
					jQuery(el).val(newval);
					jQuery(el).get(0).focus();
					var caret = start+1+tab.length;
					jQuery(el).get(0).setSelectionRange(caret, caret);
					return true;
				}
				//When tab is pressed enter a new tab
				if(event.keyCode == 9){
					event.preventDefault();	
					var val = jQuery(el).val();
					var start = jQuery(el).get(0).selectionStart;
					var begin = val.substring(0, start);
					var newval = begin+"\t"+val.substring(start, val.length);
					jQuery(el).val(newval);
					jQuery(el).get(0).focus();
					var caret = start+1;
					jQuery(el).get(0).setSelectionRange(caret, caret);
					return true;
				}
			});
			
			return this;
		});
	};
})(jQuery);