<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*********************************************
* This is not a substitue for /admin/auth.php
*********************************************/
$bg->add_shortcode('login', 'core_login_init');

function core_login_init($obj){
	global $bg, $bdb;
	$options = $obj->options;
	
	$bg->add_css('/core/login/style.css', 'site-foot');
	$bg->add_js('/core/login/login.js', 'site-foot');
	$bg->add_js('/assets/js/jquery-formobj.js', 'site-foot');
	
	if(count($bg->user->permissions) <= 1){ //User is only a Guest because she only has Guest Group assigned to them
		require( (!empty($options->login_template)) ? $options->login_template : SITE.'/core/login/login_template.php' ); 		
	}else{
		require( (!empty($options->logout_template)) ? $options->logout_template : SITE.'/core/login/logout_template.php' );		
	}
	//for future releases, allow specific groups and users to access certain parts for the site
}
?>