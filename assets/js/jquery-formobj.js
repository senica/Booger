// JavaScript Document
// JavaScript Document

/**********************************************************
* jQuery.fn.formobj()
* var obj = jQuery(#form).formobj(action);
* Takes a form and turns the fields into an object for
* ajax.
* action allows you to disable and enable the form
* fields
* Returns an object of NAME = NAME.VALUE, NAME = NAME.TYPE
* For checkboxes and radios, we only grab selected items
**********************************************************/
(function( $ ){
	$.fn.formobj = function(action){
		var obj = {};
		jQuery("input[type=hidden],input[type=text],input[type=checkbox]:checked,input[type=radio]:checked,input[type=password],textarea,select", this).each( function(index,el){
			var name = jQuery(el).attr("name");
			obj[name] = {};
			var type = jQuery(el).attr("type");
			if(jQuery.trim(type) == 'password' && jQuery.isFunction(jQuery.sha1) ) //Change passwords to sha1 before sending across network
				obj[name].value = jQuery.sha1(jQuery(el).val());
			else
				obj[name].value = jQuery(el).val();
			obj[name].type = type;
			if(action == "disable"){ jQuery(el).attr("disabled", "true"); }
			if(action == "enable"){ jQuery(el).removeAttr("disabled"); }
		});						
		return obj;
	}
})(jQuery);
