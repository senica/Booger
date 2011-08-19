// JavaScript Document
jQuery("#core-booger-contact-admin-dialog").dialog({width:500, height:550, autoOpen:false});

jQuery("#bg-admin-sidebar-top .booger-contact").click( function(){
	jQuery("#core-booger-contact-admin-dialog").dialog('open');
	if(jQuery("#core-booger-contact-admin-dialog").html() == ""){
		jQuery("#core-booger-contact-admin-dialog").load("/ajax.php?file=core/booger_contact/get_form.php");
	}
});

jQuery("#core-booger-contact-form").live("submit", function(){
	var error = jQuery(".msg", this);
	var button = jQuery("button", this);
	var orig = button.html();
	button.html("Sending");
	var obj = {};
	obj.subject = jQuery("select[name='subject']", this).val();
	obj.message = jQuery("textarea[name='message']", this).val();
	jQuery.post("/ajax.php?file=core/booger_contact/send.php", {obj:obj}, function(data){
		var json = jQuery.parseJSON(data);
		if(json.err == "true"){
			button.html("Error");
			setTimeout( function(){ button.html(orig); }, 2000);
			error.html(json.message);
		}else if(json.err == "false"){
			button.html("Sent");
			setTimeout( function(){ button.html(orig); }, 2000);
			error.html(json.message);
		}else{
			button.html("What?");
			setTimeout( function(){ button.html(orig); }, 2000);
			error.html("An unknown error occured.  We are not sure if your message got sent or not.");		
		}
	});
	return false;
});
