/* 
 * Project Name: Stripe Integrator
 * Project URI: http://
 * Description: Easily allow you to accept credit cards using Stripe.com
 * Version: 0.01
 * Author: Senica Gonzalez
 * Author URI: http://www.allebrum.com
 * License: ALL RIGHTS Reserved
 * This file is the handler callback when the createToken method id called.
 * You can copy it to your theme directory and alter it to handle the actual
 * charging differently.
 */

Stripe.ResponseHandler = function(status, response) {
	var my_form = jQuery("#stripe-payment-form");
	if (response.error) {
		//Allow user to submit again
		jQuery(".submit-button", my_form).removeAttr("disabled"); 
        //show the errors on the form
		jQuery(".message", my_form).fadeOut();
        jQuery(".message.error", my_form).html(response.error.message).fadeIn();
		jQuery(".message-wrapper", my_form).stop().slideDown();
    } else {
		jQuery(".message", my_form).fadeOut();
        jQuery(".message.working", my_form).html("Processing Payment...").fadeIn();
		jQuery(".message-wrapper", my_form).stop().slideDown();
		var extras = {}; //Handle any extra form fields to be attached to the description
		jQuery("input[type=text].option, input[type=checkbox]:checked.option, input[type=radio]:checked.option, input[type=password].option, input[type=hidden].option, textarea.option", my_form).each( function(index, el){
			var attr = jQuery(el).attr("name");
			extras[attr] = jQuery(el).val();
		});
		jQuery.post("/ajax.php?file="+Stripe.allebrum.directory+"/charge.php", {response:response, extras:extras, obj:Stripe.allebrum }, function(obj){
			
			//This is the area you will most likely change.  You can set what happens after each response
			//Such as forwarding them to another page, or making a database entry.
			
			if(obj.status === false){
				jQuery(".message", my_form).fadeOut();
				jQuery(".message.error", my_form).html(obj.message).fadeIn();
				jQuery(".message-wrapper", my_form).stop().slideDown();
				//Allow user to submit again
				jQuery(".submit-button", my_form).removeAttr("disabled");
			}else if(obj.status === true){
				jQuery(".message", my_form).fadeOut();
				jQuery(".message.ok", my_form).html(obj.message).fadeIn();
				jQuery(".message-wrapper", my_form).stop().slideDown();
				//Remove Button
				jQuery(".submit-button", my_form).remove();
			}else{
				jQuery(".message", my_form).fadeOut();
				jQuery(".message.error", my_form).html("An unknown error occurred. Do not submit again. Please contact us.").fadeIn();
				jQuery(".message-wrapper", my_form).stop().slideDown();
				//Remove Button
				jQuery(".submit-button", my_form).remove();
			}
		});
    }
}


