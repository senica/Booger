// JavaScript Document
// NOTE: All of these functions requires that the requested file send a Content-Length header.
/*************************************************************************************************************************
* jQuery.progressLoad( file, complete(data), progress(downloadedPercent, totalBytes, downloadedBytes, downloadedKiloBytes) )
**************************************************************************************************************************/
(function( $ ){
	$.fn.progressLoad = function(file, complete, progress) {
		
		var clock;
		var obj = jQuery(this);
		
		jQuery.ajax ({
			type		: 'GET',
			dataType	: 'html',
			url			: file,
			beforeSend	: function (xhr){
				clock = setInterval (function (){
					if (xhr.readyState > 2){
						var totalBytes  = xhr.getResponseHeader('Content-Length');
						var dlBytes = xhr.responseText.length;						
						progress.call(document, Math.round((dlBytes / totalBytes)*100), totalBytes, dlBytes, Math.round(dlBytes/1024) );
					}
				}, 11);
			},
			complete	: function (){
				clearInterval (clock);
			},
			success		: function (response){
				//Assing response to each element in progressLoad call
				obj.each( function(){
					jQuery(this).html(response);
					return this;
				});
				//call function
				complete.call(document, response);
			}
		});
	};
})(jQuery);