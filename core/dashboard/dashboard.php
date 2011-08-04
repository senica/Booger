<?php
$bg->add_hook('admin-dashboard', 'core_dashboard_intro');
$bg->add_hook('admin-dashboard', 'core_dashboard_notes');
$bg->add_hook('admin-head', 'core_dashboard_head');

function core_dashboard_head(){
	global $bg;
	$bg->add_css(URL.'/core/dashboard/dashboard.css');
}

function core_dashboard_notes(){
	?>
	<script>
	jQuery(document).one("click", function(){
		jQuery("#core-dashboard-minimize, #core-dashboard-start, #core-dashboard-tabs, #core-dashboard-click").fadeOut( function(){
			jQuery(this).remove();																								  
		} );									   
	});
	
	jQuery("#bg-admin-dashboard").click( function(event){
		if( !jQuery(event.target).hasClass("core-dashboard-notes") && !jQuery(event.target).parents(".core-dashboard-notes").get(0) ){
			var t = Date.now();
			jQuery(this).append('<div contentEditable="true" class="core-dashboard-notes core-dashboard-'+t+'"></div>');
			var input = jQuery(".core-dashboard-"+t);
			input.css({'position':'absolute', 'left':event.pageX+'px', 'top':event.pageY+'px'});
			input.focus();
		}
	});
	
	jQuery("#bg-admin-dashboard .core-dashboard-notes").live("keyup", function(event){
		var obj = [];
		jQuery("#bg-admin-dashboard .core-dashboard-notes").each( function(index, el){
			if(jQuery.trim(jQuery(el).html()) != "" && jQuery.trim(jQuery(el).html()) != "<br>" && jQuery.trim(jQuery(el).html()) != "<br />"){
				var i = obj.length;
				obj[i] = {};
				obj[i].left = jQuery(el).css("left");
				obj[i].top = jQuery(el).css("top");
				obj[i].data = jQuery(el).html();
			}
		});
		localStorage.core_dashboard_notes = JSON.stringify(obj);
	});
	
	jQuery(document).ready( function(){
		var obj = jQuery.parseJSON(localStorage.core_dashboard_notes);
		jQuery(obj).each( function(index,json){
			if(jQuery.trim(json.data) != ""){
				jQuery("#bg-admin-dashboard").append('<div class="core-dashboard-notes" contentEditable="true" style="position:absolute; top:'+json.top+'; left:'+json.left+';">'+json.data+'</div>');						   
			}
		});
	});
	
	</script>
<?php }

function core_dashboard_intro(){
	?>
	<div id="core-dashboard-postit"></div>
	<div id="core-dashboard-tabs"></div>
	<div id="core-dashboard-minimize"></div>
	<div id="core-dashboard-start"></div>
	<div id="core-dashboard-logo"></div>
	<div id="core-dashboard-click"></div>
<?php } ?>