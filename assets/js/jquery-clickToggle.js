// JavaScript Document
/*************************************************************************************
* jQuery.clickToggle( func1(){}, func2(){} );
* Toggles between two functions when an element is clicked
* If no functions are defined then it will toggle the click on the element
* jQuery.clickToggle();
**************************************************************************************/
(function( $ ){
	$.fn.clickToggle = function(func1, func2) {
		jQuery(this).each( function(){
			if(!func1){
				bg(this).click();
				return true;
			}
			this.clickToggle = {};
			this.clickToggle.func1 = func1;
			this.clickToggle.func2 = func2;
			jQuery(this).click( function(){
				if(!this.clickToggle.track){
					this.clickToggle.func1.call(this, jQuery);
					this.clickToggle.track = true;
				}else{
					this.clickToggle.func2.call(this, jQuery);
					this.clickToggle.track = false;
				}						 
			});
			
			return this;
		});
	};
})(jQuery);