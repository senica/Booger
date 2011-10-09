<?php
$content = file_get_contents('themes/booger/toolbar.css');
$content = preg_replace('|^\s*@.*;|', '', $content); //Remove charsets eg @charset "utf-8";
preg_match_all('/^[^{]*{[^}]*}.*?$/ims', $content, $matches); //Get CSS statements and all comments
$objects = array(); //Object Holder
foreach($matches[0] as $k => $v){
	$comments = array(); //Comment holder
	$v = preg_replace_callback('|/\*(.*?)\*/|', create_function('$m', 'global $comments; array_push($comments, $m[1]);'), $v); 
	$comment_holder = array();
	if(!empty($comments[0])){ array_push($comment_holder, trim($comments[0])); } //Only take first two comments Title
	if(!empty($comments[1])){ array_push($comment_holder, trim($comments[1])); } //Description
	$v = str_replace(array("\r\n", "\n", "\r"), ' ', $v); //Remove any line breaks as we don't need them
	$v = preg_replace('|{.*?}|', '', $v); //Remove the CSS styling
	$item = array();
	$item['tag'] = trim($v);
	if(!empty($comment_holder)){ $item['notes'] = $comment_holder; }
	array_push($objects, $item);
}
?>
<script type="text/javascript" src="./assets/js/jquery.min.js"></script>
<style>
.description{ font-size:small; }
.style{ padding:5px; border:1px solid #000; border-right:0; border-left:0; cursor:pointer; }
.tag{ display:none; }
#wrapper{ width:300px; float:left; }
#render{ width:500px; border:1px solid #000; padding:10px; margin:10px; float:left; }
</style>
<div id="wrapper"></div>
<div id="render" contenteditable="true"></div>
<script>
var css = jQuery.parseJSON('<?php echo json_encode($objects); ?>');
var el = jQuery("#wrapper");
jQuery.each(css, function(index, obj){
	var container = jQuery('<div class="style"></div>');
	el.append(container);
	if(typeof obj.notes !== 'undefined'){ //We have a comment, use that as our title
		container.append('<div class="title">'+obj.notes[0]+'</div>');
		if(typeof obj.notes[1] !== 'undefined'){ //We have a second comment, use that as our description
			container.append('<div class="description">'+obj.notes[1]+'</div>');	
		}
	}else if(obj.tag.substr(0, 1) == '.'){ //The first word is a class, use that as our title
		var c = obj.tag.split(" ");
		c = c[0].substr(1);
		c = c.replace(/-|_/g, " ");
		container.append('<div class="title">'+c+'</div><div class="description">'+obj.tag+'</div>');
	}else{
		container.append('<div class="title">'+obj.tag+'</div>');	
	}
	container.append('<div class="tag">'+obj.tag+'</div>'); //Add full tag to be parsed when clicked on						  
});

jQuery(".style").live("click", function(){
	
	//Split string and keep splitter to the right or the left
	String.prototype.split_hold = function(splitter, direction){
		var buffer = '';
		var obj = new Array();
		for(var i=0; i<this.length; i++){
			if(this[i] == splitter){
				if(typeof direction !== 'undefined' && direction == "right"){ //If the splitter stays to the right...
					if(buffer != ''){ obj.push(buffer); } //Push buffer to obj
					buffer = this[i]; //Clear buffer and start with splitter 	
				}else if(typeof direction !== 'undefined' && direction == "left"){ //If the splitter stays to the left...
					buffer = buffer + this[i]; //Add splitter to the end of the buffer
					obj.push(buffer); //Push buffer to obj
					buffer = ''; //Clear buffer
				}else{ //Just split
					if(buffer != ''){ obj.push(buffer); } //Push buffer to obj
					buffer = '';
				}
			}else{
				buffer = buffer + this[i];	
			}
		}
		if(buffer != ''){ obj.push(buffer); } //Push remainder of the buffer to the obj
		return obj;
	}
	
	//Parse attributes
	var pa = function(str){ //Convert attribute string to object Thanks voigtan http://stackoverflow.com/questions/7407905/jquery-insert-set-of-attributes-as-string-to-tag
		var temp = '';
		if(typeof str !== 'undefined' && str !== null){
			jQuery.each(str, function(index, attr){
				attr = attr.replace(/\[/, '').replace(/\]/, ''); //Remove brackest from [colspan=2][size=2]
				temp = temp+' '+attr;
			});
		}
		var el = jQuery('<span '+temp+'>')[0]; //Create temporary element and attach string attributes to it
		attr = {};
		for (var i=0, attrs=el.attributes, l=attrs.length; i<l; i++){ //Iterate over the attributes and create an object
			attr[attrs.item(i).nodeName] = attrs.item(i).value;
		}
		return attr;
	}
										
	var tag = jQuery(".tag", this).html();
	var f = jQuery("#render"); //This should the be focus element
	var last = jQuery("#render"); //Set the last element to be added.  Here we don't have one so we just use the focus
	tag = tag.replace(/\s+\[/g, '['); //Collapse space between element and attribute bracket td [colspan=2] should be td[colspan=2]
	tag = tag.replace(/&amp/g, '&').replace(/&gt;/g, ' > ').replace(/&lt;/g, ' < '); //Undo htmlentites, add space so we can split by it
	tag = tag.split(/\s+(?=[^\[\]]*\[|$)/); //Split by spaces but not when spaces are in brackets - split by a space but only when we can see a [ or the end of the line ahead and we don't see a [ or a ] first; brackets are for attributes
	jQuery.each(tag, function(index, i){ //Go through each segment of the tag split by a space
		i = i.split_hold(".", "right"); //Split classes from element identifiers td.class is now td .class
		jQuery.each(i, function(index, sw){
			if(sw == '<'){ //Set focus to focus' parent element
				f = f.parent();	
			}else if(sw == '>'){ //Set the focus as the last added element
				f = last;
			}else if(sw.substr(0, 1) == '.'){ //Add class to last added element.  last will be focus if this is the first run
				var attr = sw.match(/\[(.*?)\]/g); //Get all attributes
				sw = sw.replace(/\[.*\]/, ''); //Remove all attributes and just leave the class
				last.attr(pa(attr)); //Add attributes
				if(last.hasClass(sw.substr(1))){
					last.removeClass(sw.substr(1));						   
				}else{
					last.addClass(sw.substr(1));
				}
			}else{ //This should be an element tag
				var attr = sw.match(/\[(.*?)\]/g); //Get all attributes
				sw = sw.replace(/\[.*\]/, ''); //Remove all attributes and just get element tag
				var n = jQuery(document.createElement(sw)); //Create element
				n.attr(pa(attr)); //Add attributes
				f.append(n);
				last = n;
			}
		});			  
	});
});
</script>