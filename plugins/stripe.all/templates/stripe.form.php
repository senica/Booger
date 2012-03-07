<?php require(ASSETS.'/no_direct.php'); ?>
<!-- 
 * Project Name: Stripe Integrator
 * Project URI: http://
 * Description: Easily allow you to accept credit cards using Stripe.com
 * Version: 0.01
 * Author: Senica Gonzalez
 * Author URI: http://www.allebrum.com
 * License: ALL RIGHTS Reserved
 * This file is the template form.  Classes and form ID must remain intact.
 * The amount field may be moved outside the form, but must contain the class
 * stripe-charge-amount.
 * If you are passing the amount into the page, you can set the amount field value
 * using a post or get variable and make the field hidden.
 * 
 * You may include any fields that you want.  Extra fields MUST have a class of 
 * option and MUST have a name attribute
-->

<form action="" method="POST" id="stripe-payment-form">
	<div class="form-row">
		<label>Amount</label>
		<input type="text" size="20" autocomplete="off" class="stripe-charge-amount" />
	</div>
	<div class="form-row">
        <label>Card Number</label>
        <input type="text" size="20" autocomplete="off" class="card-number"/>
    </div>
    <div class="form-row">
        <label>CVC</label>
        <input type="text" size="4" autocomplete="off" class="card-cvc"/>
    </div>
    <div class="form-row">
        <label>Expiration (MM/YYYY)</label>
        <input type="text" size="2" class="card-expiry-month"/>
        <span> / </span>
        <input type="text" size="4" class="card-expiry-year"/>
    </div>
	<div class="message-wrapper">
		<div class="message error"></div>
		<div class="message ok"></div>
		<div class="message working"></div>
	</div>
    <button type="submit" class="submit-button">Submit Payment</button>
</form>
