// JavaScript Document
/*************************************************************************************
* Adapted from
	http://code.google.com/p/html5uploader/
	http://stackoverflow.com/questions/2657653/drag-and-drop-file-upload-in-google-chrome-chromium
* <div id="el" save="/var/www/">
* jQuery("#el").dragDropUpload({types:["image/png","image/jpeg"], processor:"dir/file.php", complete:func(data) });
* element should have an attribute named "save" that shows the save location
* callback function returns an object with msg, url, data.  msg will be "false" or "true"
**************************************************************************************/
(function( $ ){
	$.fn.dragDropUpload = function(opt) {
		
		var dragEnter = function(event){ event.preventDefault(); }
		var dragExit =	function(event){}
		var dragOver =	function(event){ event.preventDefault(); }
			
		jQuery(this).each( function(index, el){
			if(opt !== undefined && opt.kill !== undefined){
				//This doesn't work!  Need to fix
				jQuery(el).unbind();
				if(opt !== undefined && opt.complete !== undefined){
					opt.complete.call(el, jQuery);
				}
				return true;
			}
			if(!opt || !opt.processor){ console.log("You must specify a processor."); return false; }
			var save = jQuery(this).attr("save");
			//if(!opt || !opt.save){ console.log("You must specify a location to save uploads."); return false; }
		
			var el = jQuery(el).get(0);
			el.addEventListener("dragenter", dragEnter, false);
			el.addEventListener("dragexit", dragExit, false);
			el.addEventListener("dragover", dragOver, false);
			el.addEventListener("drop", function(event){ 
				event.preventDefault();
				var data = event.dataTransfer;
				
				//call drop function 
				if(opt !== undefined && opt.ondrop !== undefined){
					opt.ondrop.call(el, jQuery);	
				}
				
				/* For each dropped file. */
				for (var i = 0; i < data.files.length; i++) {
					var file = data.files[i];
					//console.log(file); return false;
	
					var xhr = new XMLHttpRequest;
					xhr.open('post', "ajax.php?file="+opt.processor+"&X-File-Name="+file.fileName+"&X-File-Size="+file.fileSize+"&X-Save-Location="+save, true);
					xhr.onreadystatechange = function () {
						if (this.readyState != 4)
							return;
							var json = jQuery.parseJSON(this.responseText);
							json.orig_size = file.fileSize;
							json.mime = file.type;
							if(opt !== undefined && opt.complete !== undefined){
								opt.complete.call(el, json); //Get callback function
							}
							//document.body.innerHTML += '<pre>' + this.responseText + '</pre>';
					}
					xhr.setRequestHeader('Content-Type', 'multipart/form-data');
					xhr.setRequestHeader('X-File-Name', file.fileName);
					xhr.setRequestHeader('X-File-Size', file.fileSize);
					xhr.setRequestHeader('X-Save-Location', save);
					xhr.send(file); // For some reason sending the actual File object in Chrome works?
					return true;
				}									 
			}, false);		
			return this;
		});
	};
})(jQuery);