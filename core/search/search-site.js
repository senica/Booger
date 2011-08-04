jQuery(".core-search-form-wrapper").each( function(index, el){
	var input = jQuery(".core-search-input", this);
	var de = jQuery(".core-search-default", this);
	var iv = input.val();
	var dev = de.html();
	//assign default value
	if(jQuery.trim(iv) == ''){
		input.val(dev);	
	}
	//remove default when clicked
	input.focusin( function(){
		if(input.val() == dev){
			input.val("");	
		}
	});
	//assign default when focus leaves
	input.focusout( function(){
		if(jQuery.trim(input.val()) == ""){
			input.val(dev);	
		}
	});
	//Select * when clicked
	input.click( function(){
		jQuery(this).select();					  
	});
});
