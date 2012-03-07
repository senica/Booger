<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*
 * Project Name: Stripe Integrator
 * Project URI: http://
 * Description: Easily allow you to accept credit cards using Stripe.com
 * Version: 0.01
 * Author: Senica Gonzalez
 * Author URI: http://www.allebrum.com
 * License: ALL RIGHTS Reserved
 * REQUIRES PHP 5.3, jQuery
 * [stripe {'api_key':'yourkey', 'publish_key':'yourkey'}]
 * <input type="text|hidden" class="stripe-charge-amount" [disabled="true"] />
 * stripe.form.php
 * stripe.token.js
 * stripe.style.css
 */

$bg->add_shortcode('stripe', function($obj){
	global $bg, $bdb, $bg_key;
	$options = $obj->options;
	
	//Make sure we have a key
	if(empty($options->publish_key) || empty($options->api_key)){ echo "You must specify two keys. i.e. &lsbkt;stripe {'publish_key':'yourkey', 'api_key':'yourkey'}]"; return false; }
	
	//Setup keys in database
	$bg->settings->stripe_publish_key = $bdb->decrypt($bg->settings->stripe_publish_key);
	$bg->settings->stripe_api_key = $bdb->decrypt($bg->settings->stripe_api_key);
	if(empty($bg->settings->stripe_publish_key) || empty($bg->settings->stripe_api_key) || $bg->settings->stripe_publish_key != $options->publish_key || $bg->settings->stripe_api_key != $options->api_key){
		$bg->setting("stripe_publish_key", $bdb->encrypt($options->publish_key));
		$bg->setting("stripe_api_key", $bdb->encrypt($options->api_key));
	}
	
	//Get Stripe Javascript and other required Javascript and CSS items
	$bg->add_js('https://js.stripe.com/v1/', 'site-foot'); //Token Creator
	$bg->add_css((file_exists(THEME_DIR.'/stripe.style.css')) ? THEME_URL.'/stripe.style.css' : $bg->plugin_url(false).'/templates/stripe.style.css', 'site-foot'); //Style Form
	$bg->add_js((file_exists(THEME_DIR.'/stripe.token.js')) ? THEME_URL.'/stripe.token.js' : $bg->plugin_url(false).'/templates/stripe.token.js', 'site-foot'); //Token Response Handler
	$bg->add_hook('site-foot', function() use ($options, $bg){ //Handle Form Submission
		echo '<script type="text/javascript">
			Stripe.allebrum	= {}; //These are publicy sent so be careful what you pass
			Stripe.allebrum.directory = "'.dirname(__FILE__).'"; //Set directory for ajax calls
			Stripe.allebrum.message_success = "'.$options->message_success.'"; //Prepended to the success message.
			Stripe.setPublishableKey("'.$bg->settings->stripe_publish_key.'");
			jQuery(window).load(function() {
				jQuery("#stripe-payment-form").click(function(){
					jQuery(".message-wrapper", this).slideUp();
					jQuery(".message", this).fadeOut();
				}).submit(function(event) {
					event.preventDefault();
					jQuery(".message", this).fadeOut();
					jQuery(".message.working", this).html("Validating...").fadeIn();
					jQuery(".message-wrapper", this).stop().slideDown();
					
					jQuery(".submit-button", this).attr("disabled", "disabled"); //disable the submit button to prevent repeated clicks
					var amount = parseFloat(jQuery(".stripe-charge-amount").val().replace(/[^0-9|\.]/g, ""))*100; //amount you want to charge in cents
					Stripe.createToken({
						number: jQuery(".card-number", this).val(),
						cvc: jQuery(".card-cvc", this).val(),
						exp_month: jQuery(".card-expiry-month", this).val(),
						exp_year: jQuery(".card-expiry-year", this).val()
					}, amount, Stripe.ResponseHandler);
					return false; //prevent the form from submitting with the default action
				});
			});
		</script>';
	});
	
	//Get Form template file.  User can have a file called stripe.form.php in their theme directory and we'll use that instead.
	require (file_exists(THEME_DIR.'/stripe.form.php')) ? THEME_DIR.'/stripe.form.php' : $bg->plugin_url(false, true).'/templates/stripe.form.php';
});
?>
