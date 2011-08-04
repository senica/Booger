<?php
// See https://dev.twitter.com/docs/api for available resources
$bg->add_shortcode('twitter', 'core_twitter_init');

function core_twitter_init($obj){
	global $bg;
	$options = $obj->options;
	
	if(empty($options->resource)){ $options->resource = 'user_timeline'; } //There must be a resource set such as user_timeline
	if(empty($options->screen_name) && empty($options->user_id)){ $options->screen_name = 'boogercms'; } //Set some stream so it works
	if(empty($options->count)){ $options->count = 1; } //How many tweets to get on each request by default
	if(empty($options->page)){ $options->page = 1; } //What page of results to start on
	if(empty($options->uid)){ $options->uid = uniqid('ct-'); } //Unique ID for jQuery to get more tweets
	if(empty($options->wait)){ $options->wait = 5; } //Number of seconds to wait before scrolling
	if(empty($options->max)){ $options->max = 1; } //Max number of set to grab
	$options->wait = $options->wait * 1000;
	
	echo '<div class="core-twitter-wrapper '.$options->class.' '.$options->uid.'"></div>';
	
	if($obj->index == 1){ $bg->add_hook('site-foot', 'core_twitter_js'); } //Add plugin on first call
	
	$bg->add_hook('site-foot', 'core_twitter_js_call', $options);
}

function core_twitter_js(){
	global $bg;
	//Make options available to javascript
	$bg->add_js(URL.'/core/twitter/twitter.js');
}

function core_twitter_js_call($options){
	echo '<script type="text/javascript"> jQuery(".'.$options->uid.'").coreTwitter(jQuery.parseJSON("'.(addslashes(json_encode($options))).'")); </script>'; 
}
?>