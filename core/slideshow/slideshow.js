// JavaScript Document
jQuery(".core-slideshow").cycle({
	next:".core-slideshow-next",
	prev:".core-slideshow-prev",
	pager:".core-slideshow-nav"
});

jQuery(".core-slideshow-pause").live("click", function(){
	var rel = jQuery(this).attr("rel");
	jQuery(".core-slideshow[rel='"+rel+"']").cycle('pause');
	jQuery(".core-slideshow-play[rel='"+rel+"']").removeClass("active");
	jQuery(this).addClass("active");
});

jQuery(".core-slideshow-play").live("click", function(){
	var rel = jQuery(this).attr("rel");
	jQuery(".core-slideshow[rel='"+rel+"']").cycle('resume');
	jQuery(".core-slideshow-pause[rel='"+rel+"']").removeClass("active");
	jQuery(this).addClass("active");
});