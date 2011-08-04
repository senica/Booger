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
<div class="top-bar"></div>
<div class="top-wrapper">
	<div class="top">
		<h1 class="site-title">[site-title]</h1>
		[global {'name':'tag-line', 'default':'a Booger site posing as an art canvas....'}]
		[search {'form_template':'$input', 'temp':'page', 'in':[['page','content,page-heading'],['post','content,page-heading']], 'length':300 }]
		<nav>[core_menus {'levels':0, 'type':'page', 'exclude':[3,4]}]</nav>
	</div>
</div>