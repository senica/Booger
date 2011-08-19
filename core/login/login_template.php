<div class="<?php echo $options->class; ?> core-login-wrapper">
	<div class="<?php echo $options->class; ?> core-login-login-wrapper">
		<div class="<?php echo $options->class; ?> core-login-login-text">Login</div>
		<div class="<?php echo $options->class; ?> core-login-login-user"><label for="user">Username</label>[forms {'ref':'core-login-login', 'type':'text', 'name':'user', 'req':1, 'action':{'ajax':'core/login/process.php'}}]</div>
		<div class="<?php echo $options->class; ?> core-login-login-pass"><label for="pass">Password</label>[forms {'ref':'core-login-login', 'type':'password', 'name':'pass', 'req':1}]</div>
		<div class="<?php echo $options->class; ?> core-login-login-remember"><label></label>[forms {'ref':'core-login-login', 'type':'checkbox', 'name':'remember', 'value':'1'}] Remember me</div>
		<div class="<?php echo $options->class; ?> core-login-login-submit">[forms {'ref':'core-login-login', 'type':'submit', 'value':'Login'}]</div>
		<div class="<?php echo $options->class; ?> core-login-login-recover"><a href="#">Forgot Login</a></div>
		[forms {'ref':'core-login-login', 'type':'message', 'pending':'Logging you in...', 'error':'Wrong username or password.', 'complete':'Okay. Refreshing page...'}]
	</div>
	<div class="<?php echo $options->class; ?> core-login-login-wrapper" style="display:none;">
		<div class="<?php echo $options->class; ?> core-login-login-text">Recover Login Info</div>
		<div class="<?php echo $options->class; ?> core-login-login-user"><label for="user">Email</label>[forms {'ref':'core-login-recover', 'type':'text', 'name':'email', 'req':1, 'action':{'ajax':'core/login/process.php'}}]</div>
		<div class="<?php echo $options->class; ?> core-login-login-submit">[forms {'ref':'core-login-recover', 'type':'submit', 'value':'Recover'}]</div>
		<div class="<?php echo $options->class; ?> core-login-login-recover"><a href="#">Login</a></div>
		[forms {'ref':'core-login-recover', 'type':'hidden', 'name':'recover', 'value':true}]
		[forms {'ref':'core-login-recover', 'type':'message', 'pending':'Getting your info...', 'error':'Email does not exist', 'complete':'Check your email.'}]
	</div>
	<div class="<?php echo $options->class; ?> core-login-register-wrapper">
		<div class="<?php echo $options->class; ?> core-login-register-text">Register</div>
		<div class="<?php echo $options->class; ?> core-login-register-name"><label for="name">Name</label>[forms {'ref':'core-login-register', 'type':'text', 'name':'name', 'req':1, 'action':{'ajax':'core/login/process.php'}}]</div>
		<div class="<?php echo $options->class; ?> core-login-register-name"><label for="name">Alias</label>[forms {'ref':'core-login-register', 'type':'text', 'name':'alias', 'req':1}]</div>
		<div class="<?php echo $options->class; ?> core-login-register-email"><label for="email">Email</label>[forms {'ref':'core-login-register', 'type':'text', 'name':'email', 'req':1}]</div>
		<div class="<?php echo $options->class; ?> core-login-register-website"><label for="website">Website</label>[forms {'ref':'core-login-register', 'type':'text', 'name':'website'}]</div>
		<div class="<?php echo $options->class; ?> core-login-register-pass">You will receive an email with a link to add your password.</div>
		<div class="<?php echo $options->class; ?> core-login-register-submit">[forms {'ref':'core-login-register', 'type':'submit', 'value':'Register'}]</div>
		[forms {'ref':'core-login-register', 'type':'hidden', 'name':'register', 'value':true}]
		[forms {'ref':'core-login-register', 'type':'message', 'pending':'Setting up your account...', 'error':'echo', 'complete':'Check your email.'}]
	</div>
</div>