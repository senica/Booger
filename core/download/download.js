// JavaScript Document
jQuery(".core-download").click( function(){
	var parent = jQuery(this).parent();
	var file = jQuery("input:checked", parent).val();
	document.location.href = 'ajax.php?file=core/download/handle.php&get='+file;
	return false;
});

jQuery(".core-download-no-radio").click( function(){
	jQuery(".core-download-no-radio .radio").removeClass("active");
	jQuery(".radio", this).addClass("active");
	var rel = jQuery(this).attr("rel");
	jQuery(this).parent().find("input").removeAttr("checked");
	jQuery(this).next().find("input").attr("checked", "true");
});

