// For Site Settings to get template variables of a page
jQuery("select[name='core_search_page_id']").die(".core_search");
jQuery("select[name='core_search_page_id']").live("change.core_search", function(){
	var val = jQuery(this).val();
	jQuery.post("/ajax.php?file=core/search/admin-get-tvs.php", {id:val}, function(json){
		var obj = jQuery.parseJSON(json);
		jQuery("select[name='core_search_page_tv']").html();
		jQuery.each(obj, function(index, val){
			jQuery("select[name='core_search_page_tv']").append('<option value="'+val+'">'+val+'</option>');						  
		});
	});
});