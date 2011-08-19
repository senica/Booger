<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Booger Install</title>
<style>
	html,body{ border:0px; margin:0px; padding:0px; width:100%; height:100%; background:#7CB8E7; }
	body{
		background: rgb(30,87,153); /* Old browsers */
		background: -moz-linear-gradient(top, rgba(30,87,153,1) 0%, rgba(41,137,216,1) 50%, rgba(32,124,202,1) 51%, rgba(125,185,232,1) 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(30,87,153,1)), color-stop(50%,rgba(41,137,216,1)), color-stop(51%,rgba(32,124,202,1)), color-stop(100%,rgba(125,185,232,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 51%,rgba(125,185,232,1) 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 51%,rgba(125,185,232,1) 100%); /* Opera11.10+ */
		background: -ms-linear-gradient(top, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 51%,rgba(125,185,232,1) 100%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */
		background: linear-gradient(top, rgba(30,87,153,1) 0%,rgba(41,137,216,1) 50%,rgba(32,124,202,1) 51%,rgba(125,185,232,1) 100%); /* W3C */
	}
	a{ color:#fff; }
	#content-wrapper{ width:1050px; margin:0 auto; padding-top:20px; }
	.agent-message{ display:none; font-family:Verdana, Geneva, sans-serif; font-size:18px; color:#ededed; padding:5px 10px; width:1050px; margin:0 auto; background:#8e8e8e; border-bottom:1px solid #ffffff; border-left:1px solid #ffffff; border-right:1px solid #ffffff; -webkit-border-bottom-right-radius: 7px; -moz-border-radius-bottomright: 7px; border-bottom-right-radius: 7px; -webkit-border-bottom-left-radius: 7px; -moz-border-radius-bottomleft: 7px; border-bottom-left-radius: 7px;  }
	.col{ width:500px; float:left; }
	.box-wrapper{ display:inline-block; background:#8e8e8e; border:2px solid #ffffff; -webkit-border-radius: 15px; -moz-border-radius: 15px; border-radius: 15px; }
	.box{ padding:10px; width:500px; -webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px; margin:2px;
		background: rgb(249,249,249); /* Old browsers */
		background: -moz-linear-gradient(top, rgba(249,249,249,1) 0%, rgba(224,224,224,1) 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(249,249,249,1)), color-stop(100%,rgba(224,224,224,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(224,224,224,1) 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(224,224,224,1) 100%); /* Opera11.10+ */
		background: -ms-linear-gradient(top, rgba(249,249,249,1) 0%,rgba(224,224,224,1) 100%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f9f9f9', endColorstr='#e0e0e0',GradientType=0 ); /* IE6-9 */
		background: linear-gradient(top, rgba(249,249,249,1) 0%,rgba(224,224,224,1) 100%); /* W3C */
	}
	.button-text{ -webkit-text-stroke:1px #8e8e8e; text-align:center; width:300px; text-transform:uppercase; color:#ededed; font-size:26px; font-family:Arial, Helvetica, sans-serif; font-weight:bold; padding:5px 0 0 0; }
	.title{ font-family:Verdana, Geneva, sans-serif; font-size:18px; color:#ededed; padding:3px 0px 3px 15px; }
	.row{ display:block; text-align:center; padding-top:10px; }
	.row span{ text-align:left; text-transform:uppercase; font-family:Verdana, Geneva, sans-serif; font-size:18px; width:140px; display:inline-block; }
	.row input{ width:300px; font-size:18px; font-family:Verdana, Geneva, sans-serif; padding:7px; -webkit-border-radius: 10px; -moz-border-radius: 10px; border-radius: 10px; border:1px solid #144374; }
	.example{ color:#aeaeae; font-family:Arial, Helvetica, sans-serif; font-size:12px; margin-left:170px; display:inline-block;  }
	
	input.weak{
		background: rgb(169,3,41); /* Old browsers */
		background: -moz-linear-gradient(left, rgba(169,3,41,1) 0%, rgba(255,255,255,1) 38%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(169,3,41,1)), color-stop(38%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(left, rgba(169,3,41,1) 0%,rgba(255,255,255,1) 38%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(left, rgba(169,3,41,1) 0%,rgba(255,255,255,1) 38%); /* Opera11.10+ */
		background: -ms-linear-gradient(left, rgba(169,3,41,1) 0%,rgba(255,255,255,1) 38%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#a90329', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 */
		background: linear-gradient(left, rgba(169,3,41,1) 0%,rgba(255,255,255,1) 38%); /* W3C */
	}
	input.medium{
		background: rgb(255,182,0); /* Old browsers */
		background: -moz-linear-gradient(left, rgba(255,182,0,1) 0%, rgba(255,255,255,1) 91%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,182,0,1)), color-stop(91%,rgba(255,255,255,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(left, rgba(255,182,0,1) 0%,rgba(255,255,255,1) 91%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(left, rgba(255,182,0,1) 0%,rgba(255,255,255,1) 91%); /* Opera11.10+ */
		background: -ms-linear-gradient(left, rgba(255,182,0,1) 0%,rgba(255,255,255,1) 91%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffb600', endColorstr='#ffffff',GradientType=1 ); /* IE6-9 */
		background: linear-gradient(left, rgba(255,182,0,1) 0%,rgba(255,255,255,1) 91%); /* W3C */	
	}
	input.strong{
		background: rgb(210,255,82); /* Old browsers */
		background: -moz-linear-gradient(left, rgba(210,255,82,1) 0%, rgba(145,232,66,1) 100%); /* FF3.6+ */
		background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(210,255,82,1)), color-stop(100%,rgba(145,232,66,1))); /* Chrome,Safari4+ */
		background: -webkit-linear-gradient(left, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* Chrome10+,Safari5.1+ */
		background: -o-linear-gradient(left, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* Opera11.10+ */
		background: -ms-linear-gradient(left, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#d2ff52', endColorstr='#91e842',GradientType=1 ); /* IE6-9 */
		background: linear-gradient(left, rgba(210,255,82,1) 0%,rgba(145,232,66,1) 100%); /* W3C */	
	}
	.install-message{ font-family:Arial, Helvetica, sans-serif; font-size:12px; color:#fff; padding:5px; }
	
	.required{ background:#ffe84d; }
	.busy-wrapper{ display:none; text-align:center; }
	.busy{ width:32px; height:32px; display:inline-block; background:url(busy.gif); }
	.error{ border-color:#F00 !important; }
	.config{ font-family:Arial, Helvetica, sans-serif; font-size:12px; }
	
</style>

<script type="text/javascript" src="../assets/js/jquery.min.js"></script>

<script>
jQuery(document).ready( function(){
	//Let them know that they need to use Chrome
	var userAgent = /chrome/.test( navigator.userAgent.toLowerCase() );
	if(userAgent == false){
		setTimeout( function(){
			jQuery(".agent-message").slideDown('fast', 'swing');					 
		}, 1000 );	
	}
	
	jQuery("#install-form input:first").focus();
	
	//Handle Form
	jQuery("#install-form").submit( function(){
		var test = true;
		var form = this;
		jQuery("input", this).removeClass("required").attr("disabled", "disabled");
		jQuery("*").removeClass("error");
		jQuery("input[type=text]", this).each( function(index, el){
			if(jQuery.trim(jQuery(this).val()) == ""){
				test = false;
				jQuery(this).addClass("required");
			}
		});
		if(test === false){ jQuery("input", this).not(".admin").removeAttr("disabled"); }
		else{
			jQuery(".busy-wrapper").slideDown('fast');
			//Get form fields
			var post = {};	
			jQuery("input[type=text],input[type=checkbox]:checked,input[type=radio]:checked,input[type=password],input[type=submit],textarea,select", this).each( function(index,el){
				var name = jQuery(el).attr("name");
				post[name] = {};
				post[name].value = jQuery(el).val();
				post[name].type = jQuery(el).attr("type");
			});
			jQuery.post("install.php", {post:post}, function(json){
				jQuery("input", form).not(".admin").removeAttr("disabled");
				jQuery(".busy-wrapper").slideUp('fast');
				var obj = jQuery.parseJSON(json);
				var html = '';
				var ch = false;
				if(obj.error == true){
					jQuery.each(obj.error_type, function(index, val){
						if(val == 'db_server'){
							jQuery(".db_settings").addClass("error");
							html = html+'<div>Cannot connect to your database server.</div>';
						}
						if(val == 'db_db'){
							jQuery(".db_db").addClass("error");
							html = html+'<div>Cannot select the specified database.</div>';		
						}
						if(val == 'config'){
							ch = true;
							if(jQuery(".config").html() == ""){
								jQuery(".config").html('<input type="checkbox" name="config" /> I will fill out the config file manually.');
							}
							html = html+'<div>Your /assets/config.php file is not writable.</div>';		
						}
						if(val == 'config_write'){
							html = html+'<div>We did NOT write your config file due to a problem.  You will have to do this manually.  Open /assets/config.php and fill out the first 6 lines.</div>';	
						}
						if(val == 'query'){
							html = html+'<div>Install was unsuccessful. We could not enter database values.</div>';
						}
					});
					if(ch === false){ jQuery(".config").html(''); }
				}else if(obj.error == false){
					html = html+'<div>Install was successful. If you chose to manually fill out your config file, do that now. One moment please...</div>';
					setTimeout(function(){document.location.href='../admin/'}, 2000);
				}else{
					html = html+'<div>An unknown error occured.  Perhaps the connection timed out.</div>';		
				}
				jQuery(".install-message").html(html);
			});
		}
		
		return false;										 
	});
	
	//Test password
	jQuery(".password").keyup( function(e){
		//Modified from http://www.marketingtechblog.com/programming/javascript-password-strength/
		jQuery(this).removeClass("strong").removeClass("medium").removeClass("weak");
		//8 characters, uppercase, and numeric.  Add (?=.*\\W) if you want a special character also.
		var strong = new RegExp("^(?=.{8,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).*$", "g");
		var med = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");
		if(strong.test(jQuery(this).val())){
			jQuery(this).addClass("strong");	
		}else if(med.test(jQuery(this).val())){
			jQuery(this).addClass("medium");	
		}else{
			jQuery(this).addClass("weak");	
		}
	});
});
</script>

<?php
//Thanks to http://www.lost-in-code.com/programming/php-code/php-random-string-with-numbers-and-letters/
function random() {
    $length = 10;
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz*&^()$#@!";
    $string = '';    
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
}
?>
</head>

<body>
	<div class="agent-message">The Booger Admin has features that requires you to use the <a href="http://www.google.com/chrome" target="_blank">Chrome</a> browser.</div>
	<form id="install-form">
	<div id="content-wrapper">
		<div class="col" style="margin-right:50px;">
			<div class="box-wrapper">
				<div class="title">SITE INFO</div>
				<div class="box">
					<div class="row"><span>Title</span><input type="text" name="site_title" /></div>
					<div class="example">Example: The Flower Shop</div>
					<div class="row"><span>Description</span><input type="text" name="site_description" /></div>
					<div class="example">Example: Exotic flowers shipped around the world!</div>
					<div class="row"><span>URL</span><input type="text" name="site_url" value="http://<?php echo $_SERVER['HTTP_HOST']; ?>" /></div>
					<div class="example">Example: http://www.mysite.com</div>
					<div class="row"><span>Secure URL</span><input type="text" name="site_secure" value="https://<?php echo $_SERVER['HTTP_HOST']; ?>:443" /></div>
					<div class="example">Example: https://www.mysite.com:443 (include port)</div>
					<div class="row"><span>Secret Key</span><input type="text" name="site_key" value="<?php echo random(); ?>" /></div>
					<div class="example">Example: #lskdif%^23498y (anything but ')</div>
				</div>
			</div>
		</div>
		
		<div class="col">
			<div class="box-wrapper db_settings">
				<div class="title">DATABASE SETTINGS</div>
				<div class="box">
					<div class="row"><span>Server</span><input type="text" name="db_server" value="localhost" /></div>
					<div class="example">Examples: localhost or db.mysite.com</div>
					<div class="row"><span>Username</span><input type="text" name="db_user" /></div>
					<div class="example">Example: Database User</div>
					<div class="row"><span>Password</span><input type="text" name="db_pass" /></div>
					<div class="example">Example: Database Password</div>
					<div class="row"><span>Database</span><input type="text" name="db_db" class="db_db" /></div>
					<div class="example">Example: boogercms</div>
					<div class="row"><span>Prefix</span><input type="text" name="db_prefix" value="bg" /></div>
					<div class="example">Example: bg (underscore will be added bg_)</div>
				</div>
			</div>
		</div>
		
		<div class="col" style="margin-top:20px; margin-right:50px; margin-bottom:30px;">
			<div class="box-wrapper">
				<div class="title">ADMIN INFO</div>
				<div class="box">
					<div class="row"><span>Name</span><input type="text" name="admin_name" /></div>
					<div class="example">Your real name.</div>
					<div class="row"><span>Email</span><input type="text" name="admin_email" /></div>
					<div class="example">Important emails will go here.</div>
					<div class="row"><span>Username</span><input type="text" name="admin_user" class="admin" value="admin" disabled="disabled" /></div>
					<div class="example">You can add more users once logged in.</div>
					<div class="row"><span>Password</span><input type="text" name="admin_pass" class="password" /></div>
					<div class="example"></div>
				</div>
			</div>
		</div>
		
		<div class="col" style="margin-top:20px;">
			<div class="box-wrapper">
				<div class="title">FINISH</div>
				<div class="box" style="text-align:center;">
					<div class="config"></div>
					<div><input type="submit" class="button-text" value="install" /></div>
					<div class="busy-wrapper"><div class="busy"></div></div>	 <!-- http://www.sanbaldo.com/wordpress/1/ajax_gif/ -->
				</div>
			</div>
			<div class="install-message"></div>
		</div>
	</div>
	</form>
</body>
</html>