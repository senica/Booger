// JavaScript Document
core_toolbar = {};
core_toolbar.focus = {};

/*Template Variable Tools - May want to reconcile toolbox and tools at some point*/

//Columns
jQuery("#core-toolbar-columns-dialog").dialog({autoOpen:false, width:500, modal:true});
jQuery("#core-toolbar-tv-tools-wrapper .columns").live("click", function(){
	jQuery("#core-toolbar-columns-dialog").dialog('open');																			 
});
jQuery("#core-toolbar-columns-dialog .insert").live("click", function(){
	var p = jQuery(this).parents("#core-toolbar-columns-dialog:first");
	var id = p.find(".id").val();
	var num = p.find(".cols").val();
	var owidth = p.find(".owidth").val();
	var tpadding = p.find(".tpadding").val();
	var bpadding = p.find(".bpadding").val();
	var spadding = p.find(".spadding").val();
	var tmargin = p.find(".tmargin").val();
	var bmargin = p.find(".bmargin").val();
	var smargin = p.find(".smargin").val();
	
	var range = core_toolbar.focus.range;
	range.extractContents();
	var el = core_toolbar.focus.doc.createElement("div");
	range.insertNode(el);
	
	jQuery(el).attr("class", "core-toolbar-column-wrapper");
	jQuery(el).css({'width':owidth});
	for(var i=0; i<num; i++){
		jQuery(el).append('<div class="core-toolbar-column" style="float:left; margin:'+tmargin+' '+smargin+' '+bmargin+'; padding:'+tpadding+' '+spadding+' '+bpadding+';"></div>');	
	}
	jQuery(el).append('<div style="width:100%; clear:both; height:0px;"><br /></div>');
	
	var row = jQuery(el).outerWidth(); //rendered outer width
	var rcw = jQuery(".core-toolbar-column:first", el).outerWidth(true); //rendered column width
	//What column width should be without any padding or margin
	var cow = row / num;
	jQuery(".core-toolbar-column", el).css({'width':(cow-rcw)+'px'});
	jQuery(".core-toolbar-column", el).html('Column');
});

//Insert Fresh line BEFORE Current Line
jQuery("#core-toolbar-tv-tools-wrapper .previous-line").live("click", function(){	
	var el = core_toolbar.focus.focal;
	while(!jQuery(el).hasClass("template-variable")){
		var prev = el;
		el = el.parentNode;
	}
	jQuery(prev).before('<div><br /></div>');
});

//Insert Fresh line AFTER Current Line
jQuery("#core-toolbar-tv-tools-wrapper .next-line").live("click", function(){
	var el = core_toolbar.focus.focal;
	while(!jQuery(el).hasClass("template-variable")){
		var prev = el;
		el = el.parentNode;
	}
	jQuery(prev).after('<div><br /></div>');
});

//Hold Formatting Containers like <div class="clearfix"> and don't remove containers without text in them
jQuery("#core-toolbar-tv-tools-wrapper .hold").live("click", function(){
	jQuery(this).toggleClass('active');
	jQuery(core_toolbar.focus.tv).toggleClass('holding');
	if(jQuery(core_toolbar.focus.tv).hasClass('holding')){
		jQuery(core_toolbar.focus.tv).bind("keydown.holding", function(event){
			var o = event.target.ownerDocument;
			var p = event.target.ownerDocument.getSelection().getRangeAt(0).startContainer;
			if(p.nodeType == 3){ p = p.parentNode; }
			//If delete key is pressed and we are at the end of a container, don't delete the next container
			if(event.keyCode == 46){
				var s = o.getSelection();
				if(s.anchorOffset == s.anchorNode.length || s.anchorNode.length === undefined){
					return false;	
				}
			}
			//If backspace is pressed and we are at the beginning of a container, don't destroy current container.
			if(event.keyCode == 8){
				var s = o.getSelection();
				if(s.anchorOffset == 0){
					return false;	
				}
			}
			//If Backspace or Delete is pushed and the element has no more text, fill it with a <br> and set the focus here
			if(event.keyCode == 8 || event.keyCode == 46){
				if(p.innerHTML.length <= 1){
					p.innerHTML = '<br />';
					//From Tim Down : http://stackoverflow.com/questions/2871081/jquery-setting-cursor-position-in-contenteditable-div
					var sel, range;
					range = o.createRange();
					range.selectNodeContents(p);
					range.collapse(true);
					sel = o.getSelection();
					sel.removeAllRanges();
					sel.addRange(range);
					return false;
				}	
			}
		});
	}else{
		jQuery(core_toolbar.focus.tv).unbind(".holding");	
	}
});

//Margins
jQuery("#core-toolbar-tv-tools-wrapper .margin").live("click", function(event){
	if(jQuery(event.target).parents(".margin-dropdown").get(0)){ return false; }
	var dd = jQuery(".margin-dropdown", this);
	dd.toggleClass('active');
	var focus = core_toolbar.focus;
	if(dd.hasClass('active')){
		//Check for positioning of dropdown
		if(jQuery(dd).offset().left+jQuery(dd).width() > jQuery(focus.doc).width()){ jQuery(dd).css({'left':'auto', 'right':'0px'}); }
		else{ jQuery(dd).css({'left':'0px', 'right':'auto'}); }
		
		//When the element changes, set sliders to element value
		jQuery(focus.tv).bind("core_toolbar.focus", function(event, focus){
			var el = focus.focal;
			var margintop = jQuery(el).css('margin-top');
			jQuery(".top", dd).val(parseInt(margintop));
			jQuery(".topt", dd).val(parseInt(margintop));
			var mt = (margintop.substr(-1,1) == '%') ? margintop.substr(-1,1) : margintop.substr(-2,2);
			jQuery(".topm", dd).val(mt);
			var marginright = jQuery(el).css('margin-right');
			jQuery(".right", dd).val(parseInt(marginright));
			jQuery(".rightt", dd).val(parseInt(marginright));
			var mr = (marginright.substr(-1,1) == '%') ? marginright.substr(-1,1) : marginright.substr(-2,2);
			jQuery(".rightm", dd).val(mr);
			var marginbottom = jQuery(el).css('margin-bottom');
			jQuery(".bottom", dd).val(parseInt(marginbottom));
			jQuery(".bottomt", dd).val(parseInt(marginbottom));
			var mb = (marginbottom.substr(-1,1) == '%') ? marginbottom.substr(-1,1) : marginbottom.substr(-2,2);
			jQuery(".bottomm", dd).val(mb);
			var marginleft = jQuery(el).css('margin-left');
			jQuery(".left", dd).val(parseInt(marginleft));
			jQuery(".leftt", dd).val(parseInt(marginleft));
			var ml = (marginleft.substr(-1,1) == '%') ? marginleft.substr(-1,1) : marginleft.substr(-2,2);
			jQuery(".leftm", dd).val(ml);
		});
		
		//When slider changes
		jQuery(".top", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-top':jQuery(this).val()+jQuery(".topm", dd).val()}); jQuery(".topt", dd).val(jQuery(this).val()); });
		jQuery(".right", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-right':jQuery(this).val()+jQuery(".rightm", dd).val()}); jQuery(".rightt", dd).val(jQuery(this).val()); });
		jQuery(".bottom", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-bottom':jQuery(this).val()+jQuery(".bottomm", dd).val()}); jQuery(".bottomt", dd).val(jQuery(this).val()); });
		jQuery(".left", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-left':jQuery(this).val()+jQuery(".leftm", dd).val()}); jQuery(".leftt", dd).val(jQuery(this).val()); });
		
		//When text input changes
		jQuery(".topt", dd).bind("keyup.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-top':jQuery(this).val()+jQuery(".topm", dd).val()}); jQuery(".top", dd).val(jQuery(this).val()); });
		jQuery(".rightt", dd).bind("keyup.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-right':jQuery(this).val()+jQuery(".rightm", dd).val()}); jQuery(".right", dd).val(jQuery(this).val()); });
		jQuery(".bottomt", dd).bind("keyup.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-bottom':jQuery(this).val()+jQuery(".bottomm", dd).val()}); jQuery(".bottom", dd).val(jQuery(this).val()); });
		jQuery(".leftt", dd).bind("keyup.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-left':jQuery(this).val()+jQuery(".leftm", dd).val()}); jQuery(".left", dd).val(jQuery(this).val()); });
		
		//When measurement changes
		jQuery(".topm", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-top':jQuery(".topt",dd).val()+jQuery(this).val()}); });
		jQuery(".rightm", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-right':jQuery(".rightt",dd).val()+jQuery(this).val()}); });
		jQuery(".bottomm", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-bottom':jQuery(".bottomt",dd).val()+jQuery(this).val()}); });
		jQuery(".leftm", dd).bind("change.margin", function(){ jQuery(core_toolbar.focus.focal).css({'margin-left':jQuery(".leftt",dd).val()+jQuery(this).val()}); });
	}else{
		jQuery("*", dd).unbind(".margin");
		jQuery(focus.tv).unbind("core_toolbar.focus");
	}
});

//Padding
jQuery("#core-toolbar-tv-tools-wrapper .padding").live("click", function(event){
	if(jQuery(event.target).parents(".padding-dropdown").get(0)){ return false; }
	var dd = jQuery(".padding-dropdown", this);
	dd.toggleClass('active');
	var focus = core_toolbar.focus;
	if(dd.hasClass('active')){
		//Check for positioning of dropdown
		if(jQuery(dd).offset().left+jQuery(dd).width() > jQuery(focus.doc).width()){ jQuery(dd).css({'left':'auto', 'right':'0px'}); }
		else{ jQuery(dd).css({'left':'0px', 'right':'auto'}); }
		
		//When the element changes, set sliders to element value
		jQuery(focus.tv).bind("core_toolbar.focus", function(event, focus){
			var el = focus.focal;
			var paddingtop = jQuery(el).css('padding-top');
			jQuery(".top", dd).val(parseInt(paddingtop));
			jQuery(".topt", dd).val(parseInt(paddingtop));
			var pt = (paddingtop.substr(-1,1) == '%') ? paddingtop.substr(-1,1) : paddingtop.substr(-2,2);
			jQuery(".topm", dd).val(pt);
			var paddingright = jQuery(el).css('padding-right');
			jQuery(".right", dd).val(parseInt(paddingright));
			jQuery(".rightt", dd).val(parseInt(paddingright));
			var pr = (paddingright.substr(-1,1) == '%') ? paddingright.substr(-1,1) : paddingright.substr(-2,2);
			jQuery(".rightm", dd).val(pr);
			var paddingbottom = jQuery(el).css('padding-bottom');
			jQuery(".bottom", dd).val(parseInt(paddingbottom));
			jQuery(".bottomt", dd).val(parseInt(paddingbottom));
			var pb = (paddingbottom.substr(-1,1) == '%') ? paddingbottom.substr(-1,1) : paddingbottom.substr(-2,2);
			jQuery(".bottomm", dd).val(pb);
			var paddingleft = jQuery(el).css('padding-left');
			jQuery(".left", dd).val(parseInt(paddingleft));
			jQuery(".leftt", dd).val(parseInt(paddingleft));
			var pl = (paddingleft.substr(-1,1) == '%') ? paddingleft.substr(-1,1) : paddingleft.substr(-2,2);
			jQuery(".leftm", dd).val(pl);
		});
		
		//When slider changes
		jQuery(".top", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-top':jQuery(this).val()+jQuery(".topm", dd).val()}); jQuery(".topt", dd).val(jQuery(this).val()); });
		jQuery(".right", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-right':jQuery(this).val()+jQuery(".rightm", dd).val()}); jQuery(".rightt", dd).val(jQuery(this).val()); });
		jQuery(".bottom", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-bottom':jQuery(this).val()+jQuery(".bottomm", dd).val()}); jQuery(".bottomt", dd).val(jQuery(this).val()); });
		jQuery(".left", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-left':jQuery(this).val()+jQuery(".leftm", dd).val()}); jQuery(".leftt", dd).val(jQuery(this).val()); });
		
		//When text input changes
		jQuery(".topt", dd).bind("keyup.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-top':jQuery(this).val()+jQuery(".topm", dd).val()}); jQuery(".top", dd).val(jQuery(this).val()); });
		jQuery(".rightt", dd).bind("keyup.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-right':jQuery(this).val()+jQuery(".rightm", dd).val()}); jQuery(".right", dd).val(jQuery(this).val()); });
		jQuery(".bottomt", dd).bind("keyup.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-bottom':jQuery(this).val()+jQuery(".bottomm", dd).val()}); jQuery(".bottom", dd).val(jQuery(this).val()); });
		jQuery(".leftt", dd).bind("keyup.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-left':jQuery(this).val()+jQuery(".leftm", dd).val()}); jQuery(".left", dd).val(jQuery(this).val()); });
		
		//When measurement changes
		jQuery(".topm", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-top':jQuery(".topt",dd).val()+jQuery(this).val()}); });
		jQuery(".rightm", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-right':jQuery(".rightt",dd).val()+jQuery(this).val()}); });
		jQuery(".bottomm", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-bottom':jQuery(".bottomt",dd).val()+jQuery(this).val()}); });
		jQuery(".leftm", dd).bind("change.padding", function(){ jQuery(core_toolbar.focus.focal).css({'padding-left':jQuery(".leftt",dd).val()+jQuery(this).val()}); });
	}else{
		jQuery("*", dd).unbind(".padding");
		jQuery(focus.tv).unbind("core_toolbar.focus");
	}
});

//Width & Height
jQuery("#core-toolbar-tv-tools-wrapper .width-height").live("click", function(event){
	if(jQuery(event.target).parents(".width-height-dropdown").get(0)){ return false; }
	var dd = jQuery(".width-height-dropdown", this);
	dd.toggleClass('active');
	var focus = core_toolbar.focus;
	if(dd.hasClass('active')){
		//Check for positioning of dropdown
		if(jQuery(dd).offset().left+jQuery(dd).width() > jQuery(focus.doc).width()){ jQuery(dd).css({'left':'auto', 'right':'0px'}); }
		else{ jQuery(dd).css({'left':'0px', 'right':'auto'}); }
		
		//When the element changes, set sliders to element value
		jQuery(focus.tv).bind("core_toolbar.focus", function(event, focus){
			var el = focus.focal;
			var width = jQuery(el).css('width');
			jQuery(".width", dd).val(parseInt(width));
			jQuery(".widtht", dd).val(parseInt(width));
			var w = (width.substr(-1,1) == '%') ? width.substr(-1,1) : width.substr(-2,2);
			jQuery(".widthm", dd).val(w);
			var height = jQuery(el).css('height');
			jQuery(".height", dd).val(parseInt(height));
			jQuery(".heightt", dd).val(parseInt(height));
			var h = (height.substr(-1,1) == '%') ? height.substr(-1,1) : height.substr(-2,2);
			jQuery(".heightm", dd).val(h);
		});
		
		//When slider changes
		jQuery(".width", dd).bind("change.wh", function(){ jQuery(core_toolbar.focus.focal).css({'width':jQuery(this).val()+jQuery(".widthm", dd).val()}); jQuery(".widtht", dd).val(jQuery(this).val()); });
		jQuery(".height", dd).bind("change.wh", function(){ jQuery(core_toolbar.focus.focal).css({'height':jQuery(this).val()+jQuery(".heightm", dd).val()}); jQuery(".heightt", dd).val(jQuery(this).val()); });

		//When text input changes
		jQuery(".widtht", dd).bind("keyup.wh", function(){ jQuery(core_toolbar.focus.focal).css({'width':jQuery(this).val()+jQuery(".widthm", dd).val()}); jQuery(".width", dd).val(jQuery(this).val()); });
		jQuery(".heightt", dd).bind("keyup.wh", function(){ jQuery(core_toolbar.focus.focal).css({'height':jQuery(this).val()+jQuery(".heightm", dd).val()}); jQuery(".height", dd).val(jQuery(this).val()); });

		//When measurement changes
		jQuery(".widthm", dd).bind("change.wh", function(){ jQuery(core_toolbar.focus.focal).css({'width':jQuery(".widtht",dd).val()+jQuery(this).val()}); });
		jQuery(".heightm", dd).bind("change.wh", function(){ jQuery(core_toolbar.focus.focal).css({'height':jQuery(".heightt",dd).val()+jQuery(this).val()}); });
	}else{
		jQuery("*", dd).unbind(".wh");
		jQuery(focus.tv).unbind("core_toolbar.focus");
	}
});

//Unwrap - When pushing enter, Chrome will keep previous line element wrappers.  This will remove all wrappers and place text in own div
jQuery("#core-toolbar-tv-tools-wrapper .unwrap").live("click", function(){	
	var el = core_toolbar.focus.focal;
	var orig = el;
	while(!jQuery(el).hasClass("template-variable")){
		var prev = el;
		el = el.parentNode;
	}
	jQuery(prev).after('<div>'+jQuery(orig).html()+'</div>');
	jQuery(orig).remove();
});

//Styles List
jQuery("#core-toolbar-tv-tools-wrapper .style").click( function(){ //Turn Styles into dropdown
	var wrapper = this;
	var dd = jQuery(".styles-dropdown", wrapper);
	if(dd.is(":hidden")){
		//Update styles list
		jQuery.get("/ajax.php?file=core/toolbar/parse_styles.php", {}, function(json){
			var css = jQuery.parseJSON(json);
			if(typeof css.err !== 'undefined'){ dd.html(css.err); }
			else{
				dd.html(""); //Clear dropdown before appending styles
				jQuery.each(css, function(index, obj){
					var container = jQuery('<div class="core-toolbar-css-item"></div>');
					dd.append(container);
					if(typeof obj.notes !== 'undefined'){ //We have a comment, use that as our title
						container.append('<div class="core-toolbar-css-title">'+obj.notes[0]+'</div>');
						if(typeof obj.notes[1] !== 'undefined'){ //We have a second comment, use that as our description
							container.append('<div class="core-toolbar-css-desc">'+obj.notes[1]+'</div>');	
						}
					}else if(obj.tag.substr(0, 1) == '.'){ //The first word is a class, use that as our title
						var c = obj.tag.split(" ");
						c = c[0].substr(1);
						c = c.replace(/-|_/g, " ");
						container.append('<div class="core-toolbar-css-title">'+c+'</div><div class="core-toolbar-css-desc">'+obj.tag+'</div>');
					}else{
						container.append('<div class="core-toolbar-css-title">'+obj.tag+'</div>');	
					}
					container.append('<div class="core-toolbar-css-tag">'+obj.tag+'</div>'); //Add full tag to be parsed when clicked on						  
				});		
			}
		});
		//Reload page styles
		var doc = core_toolbar.focus.doc;
		var queryString = '?reload=' + new Date().getTime();
		jQuery('link[rel="stylesheet"]', doc).each(function () {
			this.href = this.href.replace(/\?.*|$/, queryString);
		});
	}
	dd.toggle(); //Toogle open close state																
});
jQuery(".core-toolbar-css-item").live("click", function(){
	var focal = core_toolbar.focus.focal;
	var size = -1; //default is not selection
	var range = core_toolbar.focus.range;
	if(typeof range !== 'undefined' && range != null){ size = range.toString().length; }
	var psize = focal.innerText.length; //Get elements text size

	//Split string and keep splitter to the right or the left
	var split_hold = function(splitter, direction){
		var buffer = '';
		var obj = new Array();
		var inbrkt = false
		for(var i=0; i<this.length; i++){
			if(this[i] == '['){
				inbrkt = true;
			}else if(this[i] == ']'){
				inbrkt = false;	
			}
			
			if(this[i] == splitter && inbrkt == false){ //Only split if not in a bracket
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
										
	var tag = jQuery(".core-toolbar-css-tag", this).html();
	var f = jQuery(focal); //This should the be focus element
	var last = f; //Set the last element to be added.  Here we don't have one so we just use the focus
	tag = tag.replace(/\s+\[/g, '['); //Collapse space between element and attribute bracket td [colspan=2] should be td[colspan=2]
	tag = tag.replace(/&amp/g, '&').replace(/&gt;/g, ' > ').replace(/&lt;/g, ' < '); //Undo htmlentites, add space so we can split by it
	//tag = tag.split(/\s+(?=[^\[\]]*\[|$)/); //Split by spaces but not when spaces are in brackets - split by a space but only when we can see a [ or the end of the line ahead and we don't see a [ or a ] first; brackets are for attributes
	//Had problems with regex split and brackets, so I just spelled it out...
	var hold = new Array();
	var buffer = '';
	var isbkt = false;
	for(var i=0; i<tag.length; i++){
		if(tag[i] == '['){ isbkt = true; }
		if(tag[i] == ']'){ isbkt = false; }
		if(tag[i] == " " && isbkt === false){
			if(jQuery.trim(buffer) != ''){
				hold.push(buffer);
			}
			buffer = '';
		}else{
			buffer = buffer+tag[i];	
		}
	}
	hold.push(buffer); //Add last chunk
	tag = hold;
	jQuery.each(tag, function(index, i){ //Go through each segment of the tag split by a space
		i = split_hold.call(i, ".", "right"); //Split classes from element identifiers td.class is now td .class
		jQuery.each(i, function(index, sw){
			if(sw == '<'){ //Set focus to focus' parent element
				f = f.parent();	
			}else if(sw == '>'){ //Set the focus as the last added element
				f = last;
			}else if(sw.substr(0, 1) == '.'){ //Add class to last added element.  last will be focus if this is the first run
				var attr = sw.match(/\[(.*?)\]/g); //Get all attributes
				sw = sw.replace(/\[.*\]/, ''); //Remove all attributes and just leave the class
				
				if(size <= 0 || size == psize){ //Either no text is selected, all the text of an element is selected, or an image is selected; add/remove classes from last element
					last.attr(pa(attr)); //Add attributes
					if(last.hasClass(sw.substr(1))){
						last.removeClass(sw.substr(1));						   
					}else{
						last.addClass(sw.substr(1));
					}
					if(last.html() == ''){ last.html(last.attr('data-default')); } //Assign default-data attribute value as html if html is blank
				}else{ //Only a partial range is selected, so create a span for the range
					var extract = range.extractContents();
					var span = core_toolbar.focus.doc.createElement("span");
					span.appendChild(extract);
					range.insertNode(span);
					last = jQuery(span); //Assign span as last inserted node
					last.attr(pa(attr)); //Add attributes
					if(last.html() == ''){ last.html(last.attr('data-default')); } //Assign default-data attribute value as html if html is blank
					last.addClass(sw.substr(1)); //Since we are creating the element, we will only be ADDING classes to it
				}				
			}else{ //This should be an element tag
				var attr = sw.match(/\[(.*?)\]/g); //Get all attributes
				sw = sw.replace(/\[.*\]/, ''); //Remove all attributes and just get element tag
				var n = jQuery(core_toolbar.focus.doc.createElement(sw)); //Create element
				n.attr(pa(attr)); //Add attributes
				if(size >= 0){ //Replace the range selection with this element as long as the element has more than 0 text in it or the range is greater than 0
					var extract = range.extractContents();
					range.insertNode(n[0]);
					size = -2; //Arbitrary number to signify that we are on an element basis from now on.
					last = n;
					if(last.html() == ''){ last.html(last.attr('data-default')); } //Assign default-data attribute value as html if html is blank
				}else if(size != -1){ //Items like images have a -1 size, so we can't append to it
					f.append(n);
					last = n;
					if(last.html() == ''){ last.html(last.attr('data-default')); } //Assign default-data attribute value as html if html is blank
				}else{ } //Don't append anything for items like images; size == -1
			}
		});			  
	});	
});

//Lorem Ipsum - Filler Text
jQuery("#core-toolbar-tv-tools-wrapper .lorem").live("click", function(event){
	if(jQuery(event.target).parents(".lorem-dropdown").get(0)){ return false; }
	var dd = jQuery(".lorem-dropdown", this);
	dd.toggleClass('active');
	jQuery(".insert", dd).click( function(){
		var data = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ac lorem erat, eu posuere lectus. Cras rutrum, dui id pretium ultricies, est libero facilisis lorem, eget porttitor tellus est in justo. Duis molestie volutpat arcu in placerat. Vestibulum massa libero, laoreet quis imperdiet eget, gravida eget sem. Suspendisse potenti. Aenean faucibus nibh lectus. Praesent luctus volutpat est id mattis. Nulla ultricies, leo sed molestie eleifend, purus nisl interdum quam, eu consequat odio justo sit amet ante. Vestibulum bibendum bibendum tristique. Donec sed tellus ut turpis tempor suscipit eget quis est. Sed nibh nibh, tincidunt eget vulputate ut, ullamcorper in lorem. Nunc fringilla, justo id suscipit laoreet, dui elit auctor erat, quis convallis augue odio at purus. Quisque urna justo, rutrum sit amet interdum eu, venenatis vel leo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nullam in turpis nunc, sed tristique erat. Vestibulum luctus elit				eget lectus consequat nec interdum tortor mollis. Sed velit mauris, hendrerit eget posuere id, vestibulum vel odio. Nulla facilisi. Fusce mattis urna ac mauris vestibulum iaculis. Nunc dui erat, tempor et placerat quis, ultricies id nisl. Aliquam scelerisque lorem aliquam tellus vehicula ultrices. In nisl lorem, rutrum sed dapibus vel, mollis in tortor. Curabitur condimentum convallis tempor. Aenean sollicitudin leo eget ligula posuere nec egestas purus porta. Quisque non arcu libero. Fusce quis tincidunt tellus. Aenean tincidunt tellus a elit bibendum vestibulum. Aenean aliquet consequat lacus, placerat dapibus eros varius vel. Sed nisl ipsum, sollicitudin in condimentum et, pharetra ut nulla. Curabitur vitae dolor ipsum. Donec aliquam pretium dui, vitae sodales ipsum suscipit et. Donec posuere, sapien sed tincidunt fringilla, libero sapien sagittis neque, ac pulvinar nisi enim quis nibh. Aliquam malesuada, velit at placerat adipiscing, neque lectus viverra est, nec blandit magna velit eu quam. Ut laci				nia mauris nec quam congue egestas. Nulla sapien odio, venenatis at mollis et, ornare vel lacus. Vestibulum vehicula commodo felis a tincidunt. Pellentesque lacinia eleifend sollicitudin. Curabitur ultrices suscipit gravida. Vivamus leo tortor, placerat ut ullamcorper non, accumsan eget neque. Sed non orci nec ipsum consectetur commodo. Aliquam ac interdum enim. Donec odio ipsum, feugiat quis tincidunt in, rhoncus eget ipsum. Nullam nunc ipsum, sollicitudin et sagittis vel, porta sed magna. Vestibulum nisl nulla, mattis ut eleifend sit amet, blandit quis elit';														
		var length = jQuery(".characters", dd).val();
		while(data.length < length){
			data = data+data;	
		}
		data = data.substring(0, length);
		jQuery(core_toolbar.focus.focal).append(data);
	});
});

//Anchors
jQuery("#core-toolbar-tv-tools-wrapper .anchor").live("click", function(event){
	if(jQuery(event.target).parents(".anchor-dropdown").get(0)){ return false; }
	var dd = jQuery(".anchor-dropdown", this);
	dd.toggleClass('active');
	var focus = false;
	if(dd.hasClass('active')){
		var get = function(el){
			focus = el; //Update local focus point
			el = el.focal;
			if(el.tagName.toLowerCase() == 'a'){ //if element is a link, get name
				var name = jQuery(el).attr("name");	
			}else{ //otherwise set name to ''
				var name = '';	
			}
			jQuery(".aname", dd).val(name);
			
		}
		
		get(core_toolbar.focus); //Get anchor name when anchor tool is clicked
		
		//When the element changes, get anchor name
		jQuery(focus.tv).bind("core_toolbar.focus", function(event, focus){
			get(focus);
		});		
		
		//Check for positioning of dropdown
		if(jQuery(dd).offset().left+jQuery(dd).width() > jQuery(focus.doc).width()){ jQuery(dd).css({'left':'auto', 'right':'0px'}); }
		else{ jQuery(dd).css({'left':'0px', 'right':'auto'}); }
		
		//Apply Anchor
		jQuery(".apply", dd).bind("click.anchor", function(){
			var name = jQuery.trim(jQuery(".aname", dd).val());
			name = name.replace(/\s/g, '_');
			if(name == ''){ jQuery(".remove", dd).click(); } //If name is blank, remove any anchors
			
			if(focus.focal.tagName.toLowerCase() == 'a'){ //if element is an anchor, add anchor name.
				jQuery(focus.focal).attr("name", name);	
			}else if(focus.range && focus.range.toString().length > 0){ //if text is selected, wrap that in an anchor
				var text = focus.range.extractContents();
				var a = focus.doc.createElement("a");
				focus.range.insertNode(a);
				jQuery(a).attr("name", name).html(text);	
			}else if(focus.focal.parentNode.tagName.toLowerCase() == 'a'){  //If element is already wrapped in an <a> add anchor name to that
				jQuery(focus.focal.parentNode).attr("name", name);
			}else if( jQuery(focus.focal).hasClass("template-variable") ){ //Inner wrap template variable in anchor
				jQuery(focus.focal).wrapInner('<a name="'+name+'" />');
			}else{ //wrap element in anchor
				jQuery(focus.focal).wrap('<a name="'+name+'" />');
			}												   
		});
		
		//Remove Anchor
		jQuery(".remove", dd).bind("click.anchor", function(){
			if(focus.focal.tagName.toLowerCase() == 'a'){
				var href = jQuery(focus.focal).attr("href");
				if (typeof href !== 'undefined' && href !== false){ //If anchor is also a link, just remove the anchor
					jQuery(focus.focal).removeAttr("name");
				}else{ //Otherwise remove the entire <a> tag
					jQuery(focus.focal).replaceWith(jQuery(focus.focal).html());
				}
			}else if(focus.focal.parentNode.tagName.toLowerCase() == 'a'){
				var href = jQuery(focus.focal.parentNode).attr("href");
				if (typeof href !== 'undefined' && href !== false){ //If anchor is also a link, just remove the anchor
					jQuery(focus.focal.parentNode).removeAttr("name");
				}else{ //Otherwise remove the entire <a> tag
					jQuery(focus.focal).unwrap();
				}	
			}														
		});
	}else{
		jQuery("*", dd).unbind(".anchor");
		jQuery(focus.tv).unbind("core_toolbar.focus");
	}
});

//Links
jQuery("#core_toolbar_link").dialog({width:400, autoOpen:false, modal:true});
jQuery("#core_toolbar_link_wrapper").accordion({active:false, autoHeight:false});
jQuery("#core_toolbar_link_wrapper h3.page").click( function(){ //get pages
	jQuery("#core_toolbar_link_wrapper select.page").load("/ajax.php?file=core/toolbar/link_get_pages.php&type=page");													  
});
jQuery("#core_toolbar_link_wrapper h3.post").click( function(){ //get posts
	jQuery("#core_toolbar_link_wrapper select.post").load("/ajax.php?file=core/toolbar/link_get_pages.php&type=post");													  
});
jQuery("#core_toolbar_link_wrapper h3.image").click( function(){ //get images
	jQuery("#core_toolbar_link_wrapper select.image").load("/ajax.php?file=core/toolbar/link_get_pages.php&type=image");													  
});
jQuery("#core_toolbar_link_wrapper h3.upload").click( function(){ //get uploads
	jQuery("#core_toolbar_link_wrapper select.upload").load("/ajax.php?file=core/toolbar/link_get_pages.php&type=upload");													  
});
jQuery("#core_toolbar_link_wrapper h3.anchor").click( function(){ //get page anchors
	var html = '';
	jQuery("a[name]", core_toolbar.focus.doc).each( function(index, el){
		var title = jQuery(el).html();
		title = title.substr(0, 8);
		html = html+'<option value="#'+jQuery(el).attr("name")+'">'+title+' - '+jQuery(el).attr("name")+'</option>';													   
	});
	jQuery("#core_toolbar_link_wrapper select.anchor").html(html);													  
});
jQuery("#core-toolbar-tv-tools-wrapper .link").live("click", function(){
	jQuery("#core_toolbar_link").dialog('open'); //Show Link box
	if(core_toolbar.focus.focal.tagName.toLowerCase() == 'a'){
		jQuery("#core_toolbar_link input.text").val(core_toolbar.focus.focal.innerHTML);		
	}else{
		var range = core_toolbar.focus.range;
		if(range.toString().length > 0){ jQuery("#core_toolbar_link input.text").val(range); }
	}
});
jQuery("#core_toolbar_link .cancel").click( function(){ jQuery("#core_toolbar_link").dialog('close'); } );
jQuery("#core_toolbar_link .ok").click( function(){
	var doc = core_toolbar.focus.doc;
	var range = core_toolbar.focus.range;
	
	//Get selected accordion
	var selected = jQuery("#core_toolbar_link h3[aria-expanded='true']");
	if(selected.length == 0){ jQuery("#core_toolbar_link_wrapper").addClass("error"); return true; }
	
	if(selected.hasClass("web")){
		if(jQuery("#core_toolbar_link input.web").val() == ""){ jQuery("#core_toolbar_link input.web").addClass("error"); return true; }
		var json = jQuery("#core_toolbar_link input.web").val();
	}else if(selected.hasClass("page")){
		var json = '[core_url {\'pgid\':\''+jQuery("#core_toolbar_link select.page").val()+'\'}]';
	}else if(selected.hasClass("post")){
		var json = '[core_url {\'pgid\':\''+jQuery("#core_toolbar_link select.post").val()+'\'}]';		
	}else if(selected.hasClass("image")){
		var json = '[core_url {\'pgid\':\''+jQuery("#core_toolbar_link select.image").val()+'\'}]';	
	}else if(selected.hasClass("upload")){
		var json = jQuery("#core_toolbar_link select.upload").val();	
	}else if(selected.hasClass("anchor")){
		var json = jQuery("#core_toolbar_link select.anchor").val();	
	}
	
	//assign target
	if(jQuery("#core_toolbar_link input.target").val() == ""){ jQuery("#core_toolbar_link input.target").val("_self"); }
	//assign link text
	var text = (jQuery("#core_toolbar_link input.text").val() == "") ? jQuery(core_toolbar.focus.focal).html() : jQuery("#core_toolbar_link input.text").val();
	
	//link composition
	var alt = jQuery("#core_toolbar_link input.alt").val();
	var target = jQuery("#core_toolbar_link input.target").val();
	var extra = jQuery("#core_toolbar_link input.extra").val();
	var l = '<a href="'+json+'" alt="'+alt+'" target="'+target+'" '+extra+'>';
	
	if(core_toolbar.focus.focal.tagName.toLowerCase() == 'a'){ //if element is a link, replace with new link.
		jQuery(core_toolbar.focus.focal).replaceWith(l+text+'</a>');	
	}else if(jQuery("#core_toolbar_link input.text").val() == ""){ //if text is specified, insert link in place
		if(core_toolbar.focus.focal.parentNode.tagName.toLowerCase() == 'a'){ jQuery(core_toolbar.focus.focal).unwrap(); } //If element is already wrapped in an <a> remove that first
		jQuery(core_toolbar.focus.focal).wrap(l+'</a>');
	}else{
		range.extractContents();
		var a = doc.createElement("a");
		range.insertNode(a);
		jQuery(a).wrap(l+'</a>').replaceWith(jQuery("#core_toolbar_link input.text").val());
	}
});

//Unlink
jQuery("#core-toolbar-tv-tools-wrapper .unlink").live("click", function(){
	if(core_toolbar.focus.focal.tagName.toLowerCase() == 'a'){
		jQuery(core_toolbar.focus.focal).replaceWith(jQuery(core_toolbar.focus.focal).html());	
	}else if(core_toolbar.focus.focal.parentNode.tagName.toLowerCase() == 'a'){
		jQuery(core_toolbar.focus.focal).unwrap();	
	}
});

//Move
jQuery("#core-toolbar-tv-tools-wrapper .move").click( function(){
	jQuery(this).toggleClass("active");
	if(jQuery(this).hasClass("active")){
		var focal = core_toolbar.focus.focal;
		
		jQuery(core_toolbar.focus.doc).bind("keydown.move", function(event){
			if(jQuery(focal).parents(".template-variable").length == 0 || jQuery(focal).hasClass("template-variable") ){ return true; }
			//Allow other commands to be given.
			if(event.keyCode == 37 || event.keyCode == 38 || event.keyCode == 39 || event.keyCode == 40){ event.preventDefault(); }
			var move = 1;
			if(event.shiftKey){ move = 10; }
			switch(event.keyCode){
				case 37:
					jQuery(focal).offset({left:jQuery(focal).offset().left-move});
					break;
				case 38:
					jQuery(focal).offset({top:jQuery(focal).offset().top-move});
					break;
				case 39:
					jQuery(focal).offset({left:jQuery(focal).offset().left+move});
					break;
				case 40:
					jQuery(focal).offset({top:jQuery(focal).offset().top+move});
					break;
			}								   
		});
		
		//Handle mouse drag moves
		jQuery(core_toolbar.focus.doc).bind("mousedown.move", function(event){
			if(jQuery(event.target).parents(".template-variable").length == 0 || jQuery(event.target).hasClass("template-variable") ){ return true; }
			event.preventDefault();
			focal = event.target;
			var setpointX = event.pageX-jQuery(focal).offset().left;
			var setpointY = event.pageY-jQuery(focal).offset().top-jQuery(core_toolbar.focus.doc).scrollTop();
			jQuery("body").overlay(function(o){
				over = o;
				over.bind("mousemove", function(event){
					jQuery(focal).offset({top:event.pageY-setpointY, left:event.pageX-setpointX});					  
				});
				over.bind("mouseup", function(event){
					over.unbind();
					jQuery("body").overlay({remove:true});
					jQuery(focal).parents(".template-variable:first").get(0).focus(); //Cannot focus doc when on image.  So focus on tv first
					jQuery(focal).get(0).focus(); //Give focus back to clicked element
				});
			});
		});
	}else{
		jQuery(core_toolbar.focus.doc).unbind(".move");	
	}
});

//Delete
jQuery("#core-toolbar-tv-tools-wrapper .delete").click( function(){
	jQuery(core_toolbar.focus.focal).remove();															   
});

//Html
jQuery("#core-toolbar-html").dialog({width:500, height:500, autoOpen:false, modal:true,
	close:function(){
		jQuery("#core-toolbar-html .html").unbind(".html");
	}
});
jQuery("#core-toolbar-tv-tools-wrapper .html").click( function(){
	jQuery("#core-toolbar-html .html").val(jQuery(core_toolbar.focus.tv).beautify({
		indent_char:"\t",
		indent_size:1,
		unformatted:['a', 'code', 'pre'],
		html_edit:true}) );
	jQuery("#core-toolbar-html .html").htmlEdit();
	jQuery("#core-toolbar-html").dialog('open');
	jQuery("#core-toolbar-html .html").bind("keyup.html", function(event){
		//Auto Update
		if(jQuery("#core-toolbar-html .auto").is(":checked")){
			var val = jQuery(this).val();
			//Chrome (maybe others) wont' let us post a <script> or <?php tag back to the html.  So we'll handle this on the save. For now we make them comments
			val = val.replace(/<script/g, '<!--script').replace(/<\/script>/ig, '</script-->').replace(/<\?php([\s\S]*?)\?>/igm, '<!--?php'+"$1"+'?-->');
			jQuery(core_toolbar.focus.tv).html(val);	
		}															   
	});
	
});
jQuery("#core-toolbar-html .update").click( function(){
	var val = jQuery("#core-toolbar-html .html").val();
	//Chrome (maybe others) wont' let us post a <script> or <?php tag back to the html.  So we'll handle this on the save. For now we make them comments
	val = val.replace(/<script/g, '<!--script').replace(/<\/script>/ig, '</script-->').replace(/<\?php([\s\S]*?)\?>/igm, '<!--?php'+"$1"+'?-->');
	jQuery(core_toolbar.focus.tv).html(val);													 
});

//Parent Selector
jQuery("#core-toolbar-parent").dialog({width:500, height:500, autoOpen:false,
	close:function(){
		jQuery("#core-toolbar-tv-tools-wrapper .parent").removeClass("active");
		jQuery(core_toolbar.focus.tv).unbind('.parent');
	} 
});
core_toolbar.parents = [];
jQuery("#core-toolbar-tv-tools-wrapper .parent").click( function(){
	jQuery(this).toggleClass("active");
	if(jQuery(this).hasClass("active")){
		jQuery("#core-toolbar-parent").dialog('open');
		jQuery(core_toolbar.focus.tv).bind('core_toolbar.focus.parent', function(event, focal){
			var parent = jQuery("#core-toolbar-parent .parents");
			core_toolbar.parents = [];
			parent.html("");
			var el = core_toolbar.focus.focal;
			var margin = 0;
			while(!jQuery(el).hasClass("template-variable")){
				core_toolbar.parents.push(el);
				parent.prepend("<div style='margin-left:"+margin+"px' rel='"+(core_toolbar.parents.length-1)+"'>&lt;"+el.nodeName+" "+jQuery(el).getAllAttr(true)+"></div>");
				margin = margin+10;
				el = el.parentNode;
			}
		});
		jQuery(core_toolbar.focus.tv).trigger('core_toolbar.focus', [core_toolbar.focus]); //trigger once to get current values
	}else{
		jQuery(core_toolbar.focus.tv).unbind('.parent');	
	}
});
jQuery("#core-toolbar-parent .parents div").live("click.parents", function(){ //Set focus to selected parent
	var num = jQuery(this).attr("rel");
	var doc = core_toolbar.focus.doc;
	var el = core_toolbar.parents[num];	
	el.focus();	
	var s = doc.getSelection();
	if(s.rangeCount > 0) s.removeAllRanges();
	var range = doc.createRange();
  	range.selectNode(el);
  	s.addRange(range);	
	core_toolbar.handleFocus(core_toolbar.focus.tv, el, doc, core_toolbar.focus.frame, false);
});

//Background Image
jQuery("#core-toolbar-background").dialog({width:500, height:500, autoOpen:false, modal:true, 
	close:function(){
		jQuery("#core-toolbar-background .update").unbind(".background");
		jQuery("#core-toolbar-tv-tools-wrapper .background").unbind('core-media-insert');
		jQuery("#core-toolbar-tv-tools-wrapper .background").removeClass("active");
	}
});
jQuery("#core-toolbar-tv-tools-wrapper .background").click( function(){
	jQuery(this).toggleClass("active");
	if(jQuery(this).hasClass("active")){
		var background = jQuery(this);
		var bgobj = false;
		background.unbind('core-media-insert');
		core_media.insert = background;
		background.bind('core-media-insert', function(event, obj){
			bgobj = obj;
			jQuery("#core-toolbar-background .image").html('<img src="'+bg.url+'/media/images/thumbs/'+obj.file+'" width="100" />'+obj.url);											 
		});
		jQuery("#core-toolbar-background").dialog('open');
		jQuery("#core-toolbar-background .update").bind("click.background", function(){
			var va = jQuery("#core-toolbar-background .va").val();
			var ha = jQuery("#core-toolbar-background .ha").val();
			var repeat = jQuery("#core-toolbar-background .repeat").val();
			jQuery(core_toolbar.focus.focal).css({'background':'url('+bgobj.url+') '+repeat+' '+va+' '+ha});
		});
	}else{
		jQuery("#core-toolbar-background .update").unbind(".background");
		background.unbind('core-media-insert');
	}
});
jQuery("#core-toolbar-background .select-image").click( function(){
	jQuery("#core-media-dialog").dialog('open');																						   
});

//Shortcode Handling
jQuery("#core-toolbar-shortcode-options .flatten").click( function(){
	jQuery.yesNo({
		title:"Flatten Shortcode",
		message:"Are you sure you want to flatten the shortcode?<br/>This will allow you to edit the rendered html, but it will no longer be dynamically generated.",
		yes:function(){
			var content = jQuery(".template-variable-noparse-content", core_toolbar.shortcodefocus).html();
			jQuery(core_toolbar.shortcodefocus).replaceWith(content);
			jQuery("#core-toolbar-shortcode-options").hide();
		}
	});																	
});
jQuery("#core-toolbar-shortcode-options .delete").click( function(){
	jQuery.yesNo({
		title:"Delete Shortcode",
		message:"Are you sure you want to remove this shortcode?",
		yes:function(){
			jQuery(core_toolbar.shortcodefocus).remove();
			jQuery("#core-toolbar-shortcode-options").hide();
		}
	});																	   
});
jQuery("#core-toolbar-shortcode-options .edit").click( function(){
	var code = jQuery(".template-variable-noparse-code", core_toolbar.shortcodefocus).html();
	jQuery(core_toolbar.shortcodefocus).replaceWith(code);
	jQuery("#core-toolbar-shortcode-options").hide();
});

//Show all Shortcodes
jQuery("#core-toolbar-tv-tools-wrapper .shortcode").live("click", function(){
	jQuery(".template-variable-noparse-code", core_toolbar.focus.tv).each( function(index, el){
		var code = jQuery(el).html();
		jQuery(el).parent().replaceWith(code);
	});
	jQuery("#core-toolbar-shortcode-options").hide();
});





//moved here so it could be triggered from parents
//range should be false if you are passing in an element
core_toolbar.handleFocus = function(tv, el, f, frame, range){
	core_toolbar.focus.doc = f;
	core_toolbar.focus.frame = frame;
	core_toolbar.focus.dbid = jQuery(frame).attr("dbid");
	if(!range || el.nodeName.toLowerCase() == "img"){
		core_toolbar.focus.range = null;
		core_toolbar.focus.focal = el;
		core_toolbar.focus.parent  = core_toolbar.focus.focal.parentNode;
		if(jQuery(core_toolbar.focus.focal).hasClass('template-variable')){
			core_toolbar.focus.tv = core_toolbar.focus.focal;
		}else{
			core_toolbar.focus.tv = jQuery(core_toolbar.focus.focal).parents(".template-variable:first").get(0);	
		}
	}else{
		core_toolbar.focus.range = core_toolbar.focus.doc.getSelection().getRangeAt(0);
		core_toolbar.focus.focal = core_toolbar.focus.range.startContainer;
		core_toolbar.focus.parent = core_toolbar.focus.focal.parentNode;
		if(core_toolbar.focus.focal.nodeType == 3){ //If TextNode, go up one level
			core_toolbar.focus.focal = core_toolbar.focus.parent;
			core_toolbar.focus.parent = core_toolbar.focus.parent.parentNode;
		}
		if(jQuery(core_toolbar.focus.focal).hasClass('template-variable')){
			core_toolbar.focus.tv = core_toolbar.focus.focal;
		}else{
			core_toolbar.focus.tv = jQuery(core_toolbar.focus.focal).parents(".template-variable:first").get(0);	
		}
	}
	jQuery(tv).trigger('core_toolbar.focus', [core_toolbar.focus]); //Notify Listeners of update	
}

//When page loads, add event handlers core_pages.loaded is defined in pages.js core_blog.loaded in blog.js editAsPage.loaded in assets/js/editAsPage/jquery-editAsPage.js
jQuery(document).bind("core_pages.loaded core_blog.loaded editAsPage.loaded", function(event, frame){
	var f = frame.contentWindow.document;
	var over;
	
	//Template Variable NoParse - Edit shortcodes
	jQuery(".template-variable-noparse", f).live("click", function(event){
			event.stopPropagation();
			core_toolbar.shortcodefocus = this;
			return false;
	}).live("mouseover", function(event){
		core_toolbar.shortcodefocus = this;
		var code = jQuery(".template-variable-noparse-code", this).html();
		var box = jQuery("#core-toolbar-shortcode-options");
		box.show();
		//var oT = jQuery(".template-variable-noparse-content", this).offset().top;
		var oT = event.pageY-5;
		var scr = jQuery(f).scrollTop();
		var top = oT-scr-5;		
		//var oL = jQuery(".template-variable-noparse-content", this).offset().left;
		var oL = event.pageX;
		var oW = box.outerWidth();
		var left = (oL+oW > jQuery(f).width()) ? oL-(oL+oW-jQuery(f).width()) : oL;
		box.css({'top':top+'px', 'left':left+'px', 'position':'fixed', 'display':'inline-block'});
	}).live("mouseout", function(){
		jQuery("#core-toolbar-shortcode-options").hide();	
	});
	
	//KEYUP functions
	jQuery(f).bind("keydown.ctrls", function(event){
		//CTRL+S to Save
		if(event.keyCode == 83 && event.ctrlKey){
			var rel = jQuery(frame).attr("rel");
			jQuery("#bg-admin-bottom-bar-col-one .bg-admin-tab[rel='"+rel+"'] .save").click();
			return false;
		}
		
		//CTRL+Enter
		if(event.keyCode == 13 && event.ctrlKey){
			//If the focus is inside a table row (TR), then duplicate the TR and insert it after the current TR
			if(jQuery(core_toolbar.focus.focal).parents("tr:first")[0]){
				var tr = jQuery(core_toolbar.focus.focal).parents("tr:first");
				tr.clone(true).insertAfter(tr);
				
			}
		}
		
		//TAB Move to next or prev element
		if(event.keyCode == 9){
			var tabprev = function(el){
				var t = el.parent().prev();
				if(t.children(":last-child")[0]){
					return t.children(":last-child")[0];
				}else if(!t[0] && el.parent()[0].tagName != 'body'){
					return tabnext(el.parent());
				}else{
					return false;	
				}
			}
			var tabnext = function(el){
				var t = el.parent().next();
				if(t.children(":first-child")[0]){
					return t.children(":first-child")[0];
				}else if(!t[0] && el.parent()[0].tagName != 'body'){
					return tabnext(el.parent());
				}else{
					return false;	
				}
			}
			var el = jQuery(core_toolbar.focus.focal);
			if(event.shiftKey){
				if(el.prev()[0]){
					el = el.prev()[0];	
				}else{
					el = tabprev(el);
				}
			}else{
				if(el.next()[0]){
					el = el.next()[0];	
				}else{
					el = tabnext(el);
				}
			}
			if(el === false){ return false; }
			var doc = core_toolbar.focus.doc;
			el.focus();	
			var s = doc.getSelection();
			if(s.rangeCount > 0) s.removeAllRanges();
			var range = doc.createRange();
			range.selectNodeContents(el);
			s.addRange(range);	
			core_toolbar.handleFocus(core_toolbar.focus.tv, el, doc, core_toolbar.focus.frame, false);
			return false;
		}
	});
	
	//CTRL+V paste images
	//Thanks Nick Retallack http://stackoverflow.com/questions/6333814/how-does-the-paste-image-from-clipboard-functionality-work-in-gmail-and-google-ch
	//Future release should save image instead of putting information in src tag
	jQuery(f).bind("paste.paste", function(event){
		var items = event.originalEvent.clipboardData.items; //Use original event because we are inside a jQuery event
		var item = items[0];
		//Only handle images for now.
		if(item.type == "image/png" || item.type == "image/jpeg" || item.type == "image/jpg" || item.type == "image/bmp"){
			var blob = item.getAsFile();
			var reader = new FileReader();
			reader.onload = function(event){
				var range = core_toolbar.focus.range;
				range.extractContents();
				var el = core_toolbar.focus.doc.createElement("img");
				range.insertNode(el);
				jQuery(el).attr("src", event.target.result);
			};
			reader.readAsDataURL(blob);		
		}
	});
	
	//Give focus to an element inside a TV
	jQuery(".template-variable", f).bind("mouseup.toolbar", function(event){ //On click
		core_toolbar.handleFocus(jQuery(this).get(0), event.target, f, frame, true);
	});
	jQuery(".template-variable", f).bind("keyup.toolbar", function(event){ //When the arrows or keys are pushed
		core_toolbar.focus.range = core_toolbar.focus.doc.getSelection().getRangeAt(0);
		core_toolbar.focus.focal = core_toolbar.focus.range.startContainer;
		core_toolbar.focus.parent = core_toolbar.focus.focal.parentNode;
		if(core_toolbar.focus.focal.nodeType == 3){ //If TextNode, go up one level
			core_toolbar.focus.focal = core_toolbar.focus.parent;
			core_toolbar.focus.parent = core_toolbar.focus.parent.parentNode;
		}
		if(jQuery(core_toolbar.focus.focal).hasClass('template-variable')){
			core_toolbar.focus.tv = core_toolbar.focus.focal;
		}else{
			core_toolbar.focus.tv = jQuery(core_toolbar.focus.focal).parents(".template-variable:first").get(0);	
		}
		jQuery(this).trigger('core_toolbar.focus', [core_toolbar.focus]); //Notify Listeners of update
	});
	
	//Move toolbar to location of editing tv
	var moveTools = function(el){
		if(typeof(el) == 'undefined'){ return false; }
		var tools = jQuery("#core-toolbar-tv-tools-wrapper");
		tools.show();
		var oT = jQuery(el).offset().top;	var oH = tools.outerHeight(); var scr = jQuery(f).scrollTop();
		var top = ( (oT-oH-scr) < 0 ) ? oH : (oT-oH-scr);
		if(top < 0){ top = top+100; }
		
		var oL = jQuery(el).offset().left; var oW = tools.outerWidth();
		var left = (oL+oW > jQuery(f).width()) ? oL-(oL+oW-jQuery(f).width()) : oL;
		
		tools.css({'top':top+'px', 'left':left+'px', 'position':'fixed', 'display':'inline-block'});
	};
	jQuery(".template-variable", f).focusin( function(event){
		moveTools(jQuery(this)[0]);
	});	
	jQuery(f).scroll( function(event){
		moveTools(core_toolbar.focus.tv);					   
	});
});

jQuery("#core-toolbar-close").click( function(){
	jQuery("#core-toolbar-tv-tools-wrapper").hide();										  
});