// JavaScript Document
/*************************************************************************************
* jQuery.yesNo({ title:'title', message:'message', yes:function(){}, no:function(){} })
* Create an simple query box of yes, no option.
* requires jquery-ui
**************************************************************************************/
(function( $ ){
	$.yesNo = function(opts) {
		if(!opts.title){ opts.title = ''; }
		jQuery(document.body).append('<div class="jquery-yes-no-dialog" title="'+opts.title+'"><div style="text-align:center">'+opts.message+'</div><div style="text-align:center; margin-top:10px;"><div class="yes" style="margin-right:15px;">YES</div><div class="no">NO</div></div></div>');
		var el = jQuery(".jquery-yes-no-dialog:last");
		jQuery(".yes, .no", el).button();
		el.dialog();
		jQuery(".yes", el).click( function(){ if(opts.yes){opts.yes.call(document, jQuery);} el.remove(); });
		jQuery(".no", el).click( function(){ if(opts.no){opts.no.call(document, jQuery);} el.remove(); });
		return this;
	};
})(jQuery);