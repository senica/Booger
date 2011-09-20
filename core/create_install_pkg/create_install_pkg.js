// JavaScript Document
jQuery("#core-create-install-pkg-content .load-files").live("click", function(){
	jQuery("#core-create-install-pkg-content .files").html('Loading... <progress max="100" style="width:100px;"></progress>');
	//jQuery("#core-create-install-pkg-content .files").load("/ajax.php?file=core/create_install_pkg/get_files.php");
	jQuery("#core-create-install-pkg-content .files").progressLoad("/ajax.php?file=core/create_install_pkg/get_files.php", function(data){
		jQuery("#core-create-install-pkg-content .form").append('<div class="meta"><div>Package name: <input type="text" name="pkg_name" value="booger.pkg" /></div><div><input type="checkbox" name="clear_config" /> Clear Config file of database and password information?</div><div><input type="submit" class="button" value="Create Package" /></div></div>');
	}, function(percent){
		jQuery("#core-create-install-pkg-content .files progress").val(percent);	
	});
});

jQuery("#core-create-install-pkg-content .form").live("submit", function(){
	jQuery("#core-create-install-pkg-content .files *").hide();
	jQuery("#core-create-install-pkg-content .form .meta").hide();
	jQuery("#core-create-install-pkg-content .files").append("<div>Creating Package...</div>");
});

var core_create_install_pkg = {};
core_create_install_pkg.comet = function(msg){
	jQuery("#core-create-install-pkg-content .files").prepend(msg);		
}

//Hide and Show Children files when a directory is clicked
jQuery(".core-create-install-pkg-parent").live("click", function(event){
	var cls = jQuery(event.target).attr("type");
	if(cls != "checkbox"){
		event.stopPropagation();
		jQuery(".core-create-install-pkg-child:first", this).toggle();
	}else{
		if(jQuery(event.target).is(":checked")){
			jQuery(".core-create-install-pkg-child input[type=checkbox]", this).attr('checked', true);
			jQuery(this).parents(".core-create-install-pkg-parent").each(function(index, el){
				jQuery(".core-create-install-pkg-title:first input[type=checkbox]", el).attr('checked', true);
			});
		}else{
			jQuery(".core-create-install-pkg-child input[type=checkbox]", this).removeAttr('checked');
		}
	}
});