// JavaScript Document
(function( $ ){
	$.fn.coreTwitter = function(obj) {
		
		var isfetchset = false;
		var cycle = function(isfetch){
			//Don't allow more than one cycle call
			if(isfetch == true){
				if(isfetchset == true){ return false; }
				else{ isfetchset = true; }
			}
			jQuery("."+obj.uid+" .tweet-wrapper:last").prependTo("."+obj.uid).css({display:'none'}).slideDown('200', function(){
				setTimeout(function(){ cycle(false); }, obj.wait);																												   
			});
		}
		
		var fetch = function(){
			//Get results from Twitter
			
			//If we have reached the max results to return, then cycle the results we have.
			if(obj.page > obj.max){
				cycle(true);
				return true;
			}
			
			jQuery.post("ajax.php?file=core/twitter/get-twitter.php", {obj:obj}, function(json){
				var j = jQuery.parseJSON(json);
				//If there is not error, grab next results set
				if(j.error == false){
					jQuery(j.message).prependTo("."+obj.uid).css({display:'none'}).slideDown('200', function(){
						obj.page = obj.page+1; //Get next set of results
						setTimeout(function(){ fetch(); }, obj.wait);																						
					});
				//If Twitter returns nothing, then we have reached the end of the list, cycle the results we have
				}else if(j.error == "end"){
					cycle(true);
				//There was an error of some sort, cycle the results we have
				}else{
					cycle(true);	
				}
			});
		}
		
		jQuery(this).each( function(){
			fetch();
			return this;
		});
	};
})(jQuery);