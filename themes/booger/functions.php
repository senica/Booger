<?php
$bg->add_shortcode('content');
$bg->add_shortcode('home-title-1');
$bg->add_shortcode('home-content-1');
$bg->add_shortcode('home-title-2');
$bg->add_shortcode('home-content-2');
$bg->add_shortcode('home-content-3');
$bg->add_shortcode('footer');
$bg->add_shortcode('page-search');
$bg->add_shortcode('sidebar-left');

$bg->add_hook('site-settings', 'domain_settings');
function domain_settings(){
	echo '<div><a href="https://gator1394.hostgator.com:2083/" target="_blank">CPanel</a></div>';
	echo '<div><a href="https://gator1394.hostgator.com:2083/3rdparty/phpMyAdmin/index.php" target="_blank">PHPMyAdmin</a></div>';
}

?>