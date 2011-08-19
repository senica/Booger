<?php
	header("Content-Type: text/javascript");
	$path = dirname($_SERVER[PHP_SELF]);
	$save = explode('\\', dirname(__FILE__));
	$save = implode('/', $save);
?>
// JavaScript Document
/*************************************************************************************
* jQuery.upload({chunk:2048, save:'path/to/save', progress:function(bytes,size,data), complete:function(size,object), data:function(object), before:function(file) });
* chunk: size to break file up into
* requires a real path for save
* progress bytes is the number of bytes being written and size if the original size of the file. this is the parent container.
*   progress data object will be sent in progress also.  Note that the processor may change the filename, so it might be good to check the current
*   write file in the progress callback. data.file
* data is a dump of what is happening on the server. this is the parent container
* complete gets called when the server says it's complete. this is the parent, size is the file size, object is a dump of the last
*   response from the server.
* before gets called before an upload begins. this is parent. returns file object
**************************************************************************************/
(function( $ ){
	$.fn.upload = function(opt) {
		jQuery(this).each( function(){
			var t = Date.now();
			var parent = jQuery(this);
			parent.html('<input rel="'+t+'" type="file" />');
			jQuery("input[rel='"+t+"']").change( function(){
				var file = jQuery(this).get(0).files[0];
				
				//Call before function
				if(opt !== undefined && opt.before !== undefined){ opt.before.call(parent, file); }
				
				jQuery(this).hide();
				parent.append('<div rel="'+t+'"><span class="upload-title" rel="title'+t+'">'+file.name+'</span> <progress max="100" rel="'+t+'"></progress> <div rel="pause'+t+'" style="display:inline-block; width:18px; height:18px; position:relative; top:3px; background:url(<?php echo $path; ?>/play-pause.png); background-repeat:no-repeat; background-position:0px -18px"></div></div>');
				
				var cancel = false;
				
				var size = file.size;
				var name = file.name;
				var type = file.type;
				var save = (opt && opt.save)?opt.save:'<?php echo $save; ?>/media';
				var chunk = (opt && opt.chunk)?opt.chunk:51200;
				var processor = (opt && opt.processor)?opt.processor:'<?php echo $path; ?>/upload.php';
				
				if(type == 'image/jpeg' || type == 'image/jpg' || type == 'image/png' || type == 'image/gif'){ save = save+'/images'; }
				else{ save = save+'/uploads'; }
				
				//Handle Play/Pause
				jQuery("div[rel='pause"+t+"']").click( function(){
					if(cancel == false){
						cancel = true;
						jQuery(this).css({'background-position':'0px 0px'});
					}else if(cancel == true){
						cancel = false;
						jQuery(this).css({'background-position':'0px -18px'});
						read();
					}
				});
														
				var loop = Math.floor(size / chunk);
				var mod = size%chunk;				
				count = loop;
				if(mod > 0){ count = count + 1; }
								
				var start_byte = 0;
				
				var sequence = 1;
				
				function read(){
					
					/*Old code.  
					if(start_byte+chunk > size){
						var blob = file.slice(start_byte, size-start_byte);
					}else{
						var blob = file.slice(start_byte, chunk);
						start_byte = start_byte+chunk;
					}*/
					
					if(start_byte+chunk > size){
						var blob = file.webkitSlice(start_byte, size);
					}else{
						var blob = file.webkitSlice(start_byte, start_byte+chunk);
						start_byte = start_byte+chunk;
					}				
					
					var request = new XMLHttpRequest();
					request.open("POST",  processor+"?save="+save+"&size="+size+"&type="+type+"&file="+name+"&sequence="+sequence+"&chunk="+chunk+"&count="+count, true); // open asynchronous post request		
					request.onreadystatechange = function () {
						if (this.readyState != 4){ return true; }
						var data = jQuery.parseJSON(this.responseText);
						if(opt !== undefined && opt.data !== undefined){
							opt.data.call(parent, data);
						}
						if(data.current_size == size){ //We got the whole thing
							jQuery("progress[rel='"+t+"']").remove(); //get rid or progress bar
							jQuery("div[rel='pause"+t+"']").remove(); //get rid of play-pause
							if(opt !== undefined && opt.complete !== undefined){
								opt.complete.call(parent, size, data);
							}
						}
						if(data.error == 'noerror'){
							name = data.file; //Processor may change name if file already exists
							jQuery("span[rel='title"+t+"']").html(data.file);
							jQuery("progress[rel='"+t+"']").val(Math.round((data.current_size / size)*100));
							if(opt !== undefined && opt.progress !== undefined){
								opt.progress.call(parent, data.current_size, size, data);
							}
							if(data.current_size < size && !cancel){ setTimeout( function(){ read(); }, 11); } //No error and not finished reading
						}
						if(data.error == 'nowrite'){
							request.abort();
							jQuery("progress[rel='"+t+"']").remove(); //get rid or progress bar
							jQuery("div[rel='pause"+t+"']").remove(); //get rid of play-pause
							if(opt !== undefined && opt.complete !== undefined){
								opt.complete.call(parent, size, data);
							}
						}
					}		
					request.setRequestHeader('Content-Type', 'multipart/form-data'); // make sure to set a boundary
					request.send(blob);//Works in Chrome....otherwise we need to send event.target.result from a fileReader
					
					sequence = sequence + 1;
				}
				
				read();
				
			});
			return this;
		});
	};
})(jQuery);