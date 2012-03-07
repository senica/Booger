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
 * REQUIRES Stripe PHP Libraries (should be included with plugin)
 * $bg->settings->stripe_api_key (should be set with SOJO)
 */
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$obj = (object) $_POST['response'];
$vars = (object) $_POST['obj']; //Variables that are passed in from Stripe.allebrum in stripe.all.php
require(dirname(__FILE__).'/lib/Stripe.php');
Stripe::setApiKey($bdb->decrypt($bg->settings->stripe_api_key)); //This should be set in main plugin
$json = (object) array();
$json->status = false;
$que = false;
$extras = $_POST['extras'];
if(!empty($extras)){
	$extra = '';
	foreach($extras as $k=>$v){
		$extra .= $k.' - '.$v.'  ';
	}
}
try{
	$que = Stripe_Charge::create(array(
	  "amount" => $obj->amount,
	  "currency" => $obj->currency,
	  "card" => $obj->id, // token obtained with stripe.js
	  "description" => "Website Charge: ".$extra)
	);
	if($que !== false){ 
		$json->status = true;
		$json->message = $vars->message_success."<br>Your card was successfully charged for $".number_format(($que->amount / 100), 2)."<br>Your transaction ID is ".$que->id;
		$json->details = (object) array(); 
		$json->details->id = $que->id;
		$json->details->amount = $que->amount;
	}
}catch(Exception $e){
	$arr = $e->getTrace();
	$arr = $arr[0]["args"][2]["error"]["message"];
	$json->status = false;
	$json->message = $arr ?: "Unknown Error";
}
echo json_encode($json);
?>
