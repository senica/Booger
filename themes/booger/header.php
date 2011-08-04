<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php $bg->call_hook('site-html-tag'); ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>[page-title]</title>
<link type="text/css" href="[theme-url]/reset.css" rel="stylesheet" />
<link type="text/css" href="[theme-url]/site.css" rel="stylesheet" />
<link type="text/css" href="[theme-url]/toolbar.css" rel="stylesheet" />
<?php $bg->call_hook('site-head'); ?>
</head>

<body>
<div id="top-bar-wrapper" class="color-1">
	<div id="top-bar" class="font-color-1">
		[global {'name':'top-bar-flag'}]
		[global {'name':'top-info-bar'}]
	</div>
</div>
<div id="nav-area-wrapper">
	<div class="nav-area">
	[global {'name':'logo'}]
	[global {'name':'nav-1'}]
	</div>
</div>