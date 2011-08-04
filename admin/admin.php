<?php require(ASSETS.'/no_direct.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php $bg->call_hook('admin-html-tag'); ?>>
<head>
<script>
//http://www.html5rocks.com/tutorials/appcache/beginner/
/*
window.addEventListener('load', function(e) {
  window.applicationCache.addEventListener('updateready', function(e) {
    if (window.applicationCache.status == window.applicationCache.UPDATEREADY) {
      // Browser downloaded a new app cache.
      // Swap it in and reload the page to get the new hotness.
      window.applicationCache.swapCache();
      if (confirm('A new version of this site is available. Load it?')) {
        window.location.reload();
      }
    } else {
      // Manifest didn't changed. Nothing new to server.
    }
  }, false);

}, false);
*/
</script>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-clickToggle.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-overlay.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-yesNo.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-progress.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-beautify.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-htmlEdit.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-getAllAttr.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-upload/jquery-upload.php"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/dragDrop/jquery-dragDropUpload.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/sha1.js"></script>
<script type="text/javascript" src="<?php echo URL; ?>/assets/js/editAsPage/jquery-editAsPage.js"></script>

<?php $bg->call_hook('header'); ?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Booger Administration</title>
	
	<link type="text/css" href="<?php echo URL; ?>/admin/css/skin.css" rel="stylesheet" />	
	<link type="text/css" href="<?php echo URL; ?>/assets/js/jquery-ui/css/smoothness/jquery-ui-1.8.11.custom.css" rel="stylesheet" />
	<link type="text/css" href="<?php echo URL; ?>/assets/js/jquery-ui/css/jquery-selectmenu.css" rel="stylesheet" />
	
	<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-ui/jquery-ui-1.8.11.custom.min.js"></script>		
	<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-ui/jquery-selectmenu.js"></script>
	<script type="text/javascript" src="<?php echo URL; ?>/assets/js/jquery-extensions.js"></script>
	<link type="text/css" href="<?php echo URL; ?>/assets/js/colorpicker/css/colorpicker.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo URL; ?>/assets/js/colorpicker/colorpicker.js"></script>
	
	<script type="text/javascript">
		jQuery(document).ready( function(){
			
			/*************************************************
			* Set some global variables
			*************************************************/													
			bg.bottomBar	= document.getElementById("bg-admin-bottom-bar");					//Bottom bar of admin 
			bg.popup		= document.getElementById("bg-admin-bottom-popup");					//Bottom popup of admin
			bg.toolbox		= document.getElementById("bg-admin-toolbox");						//Toolbox of admin
			bg.toolboxLogo	= document.getElementById("bg-admin-toolbox-logo");					//Toolbox logo of admin
			bg.pages		= document.getElementById("bg-admin-pages");						//Pages of admin
			bg.storage		= document.getElementById("bg-admin-storage");						//Storage for menus and such
			//bg.themes and bg.theme are defined in build.php

			//Set initial height for content area
			jQuery("#bg-admin-content").height( (window.innerHeight - jQuery("#bg-admin-panel-wrapper").height() - jQuery(bg.bottomBar).height()) + "px");
			window.addEventListener("resize", function(){
				jQuery("#bg-admin-content").height( (window.innerHeight - jQuery("#bg-admin-panel-wrapper").height() - jQuery(bg.bottomBar).height()) + "px");										 
			}, false);
			
			//Handle Logout
			jQuery("#bg-admin-logout").click( function(){
				jQuery("#bg-admin-logout").html("Getting you out...");
				jQuery.post("ajax.php?file=admin/logout.php", {}, function(data){
					if(data == true){
						jQuery.post("ajax.php?file=admin/verify_cookies.php", {}, function(data){
							if(data == false){ //cookie not set anymore
								document.location.href = "index.php";		
							}else{
								jQuery("#bg-admin-logout").html("Check connection.");	
								setTimeout( function(){ jQuery("#bg-admin-logout").html("Logout"); }, 1000);
							}
						});
					}
				});
			}); //End Logout
			
			//Make Sidebar an Accordion
			jQuery("#bg-admin-sidebar").accordion({collapsible:true, active:false});
			
			//Handle Sidebar
			jQuery("#bg-admin-sidebar-wrapper").draggable({handle:'#bg-admin-sidebar-sidetab', axis:'y', containment:[0,0,0,jQuery(window).height()-50]});
			jQuery("#bg-admin-sidebar-sidetab").mousedown( function(){
				jQuery("#bg-admin-sidebar-sidetab").bind("mousemove.sidebartab", function(){
					jQuery("#bg-admin-sidebar-sidetab").addClass("moving");																			  
				});
			}).mouseup( function(){
				jQuery("#bg-admin-sidebar-sidetab").unbind(".sidebartab");
				if(!jQuery("#bg-admin-sidebar-sidetab").hasClass("moving") && !jQuery("#bg-admin-sidebar-wrapper").hasClass("shrink")){
					jQuery("#bg-admin-sidebar-wrapper").addClass("shrink");			
				}else if(!jQuery("#bg-admin-sidebar-sidetab").hasClass("moving") && jQuery("#bg-admin-sidebar-wrapper").hasClass("shrink")){
					jQuery("#bg-admin-sidebar-wrapper").removeClass("shrink");		
				}
				jQuery("#bg-admin-sidebar-sidetab").removeClass("moving");
			});
			
			
		});//End Document Ready
	</script>
	<?php $bg->call_hook('admin-head'); ?>
</head>
<body>
	<?php $bg->call_hook('admin-body'); ?>
	<div id="bg-admin-msg"><?php echo $bg_msg->formatted_errors; ?></div>
	<div id="bg-admin-dashboard" style="width:100%; height:100%; position:relative;">
		<?php $bg->call_hook('admin-dashboard'); ?>
	</div>
	<div id="bg-admin-pages"></div>
	<div id="bg-admin-sidebar-wrapper">
		<a href="#" id="bg-admin-sidebar-sidetab"></a>
		<div id="bg-admin-sidebar-top"><?php $bg->call_hook('admin-sidebar-top'); ?></div>
		<div id="bg-admin-sidebar"><?php $bg->call_hook('admin-sidebar'); ?></div>
	</div>
	<div id="bg-admin-bottom-bar">
		<div id="bg-admin-bottom-bar-col-one"><?php $bg->call_hook('admin-bottom-bar'); ?></div>
		<div id="bg-admin-bottom-bar-col-two"><span id="bg-admin-logout" class="button">Logout</span></div>
	</div>
	<div id="bg-admin-storage"><?php $bg->call_hook('admin-storage'); ?></div> <!-- Use this for storing reusable div's and such. All div's should be hidden by default -->
	<?php $bg->call_hook('admin-foot'); ?>
</body>
</html>