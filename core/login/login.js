// JavaScript Document

//Toggle Between Login and Forgot Login forms
jQuery(".core-login-login-recover a").click( function(){
	jQuery(".core-login-login-wrapper").show();
	jQuery(this).parent().parent().parent().hide(); /* Don't forget that <form> element is added */
	return false;
});

//Handle successful login - reload the page
jQuery(document).ready(function(){
	jQuery("form.core-login-login").change(function(event, response){
		if(typeof response != 'undefined' && typeof response.error != undefined && response.error == false){
			location.reload(true);	
		}
	});
});

//Handle Logout
jQuery(".core-login-logout-link").click( function(){
	var el = jQuery(this);
	var orig = el.html();
	el.html("Logging out");
	jQuery.post("/ajax.php?file=admin/logout.php", {}, function(response){
		if(response == true){
			el.html("Refreshing");
			location.reload(true);
		}else{
			el.html("Error");
			setTimeout( function(){
				el.html(orig);					 
			}, 2000);
		}
	});
	return false;													
});

