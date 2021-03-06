<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booger - 204 Not Published</title>
<style type="text/css">
html, body{ margin:0; padding:0; background:#F9F9F9; width:100%; text-align:center; }
#wrapper{ display:inline-block; margin-top:120px; }
#message{ color:#666; font-weight:bold; text-align:left; padding:20px; font-size:14px; font-family:"Lucida Sans Unicode", "Lucida Grande", sans-serif;  -webkit-box-shadow: 2px 2px 10px #666666; -moz-box-shadow: 2px 2px 10px #666666; box-shadow: 2px 2px 10px #666666 ; display:inline-block; height:200px; width:300px; border:0; border-radius:10px; -moz-border-radius:10px; -webkit-borde-radius:10px; background:-webkit-gradient(linear, 0% 100%, 0% 0%, color-stop(0.15, rgb(242,242,242)), color-stop(0.61, rgb(255,255,255))); background:-moz-linear-gradient( center bottom, rgb(242,242,242) 15%, rgb(255,255,255) 61% ); }
#error405{ color:#aaa; font-weight:normal; }
#logo{ display:inline-block; background-image:url("../../admin/images/booger_logo.png"); width:183px; height:86px; }

</style>
</head>

<body>
<div id="wrapper">
<div id="logo"></div><br />
<div id="message">
The page you requested has been set to be published on<br /><br /><b style="display:block; text-align:center;"><?php echo $time; ?></b>
<br />
<br />
<div id="error405">Error 204 Request was successful, but no content will be returned: The page you requested is not set to be availabled until a later time.  Please try again later.<br /><br /><a href="<?php echo URL; ?>">Home</a></div>
</div>
</div>
</body>
</html>