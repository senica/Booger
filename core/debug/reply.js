// JavaScript Document
jQuery(document).ready( function(){
	jQuery("form.core-bug-reply").change(function(event, response, post){
		if(typeof response != 'undefined' && typeof response.error != undefined && response.error == false){
			console.log(event);
			console.log(post);
			jQuery.post("/ajax.php?file=core/debug/reply_load.php", {id:post.id.value, template:post.template.value, class:post.class.value}, function(html){
				jQuery(".core-bug-replies-wrapper").prepend(html);																		 
			});
		}										  
	});
});
