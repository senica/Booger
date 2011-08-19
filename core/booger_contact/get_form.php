<?php require(ASSETS.'/no_direct.php'); ?>
<form id="core-booger-contact-form">
<div><span style="width:100px; display:inline-block">From</span><?php echo $bg->user->name; ?> (<?php echo $bg->user->email; ?>)</div>
<div><span style="width:100px; display:inline-block">Subject</span>
	<select name="subject">
		<option value="general">General</option>
		<option value="support">Support</option>
		<option value="paid">Paid Work</option>
		<option value="bug">Bug</option>
		<option value="suggest">Suggestion</option>
	</select>
</div>
<div><span style="width:100px; display:inline-block; float:left;">Message</span><textarea name="message"></textarea></div>
<div style="text-align:right;"><button type="submit" class="button">Send</button></div>
<div class="msg" style="font-size:small;"></div>
<!--
Skype 'My status' button
http://www.skype.com/go/skypebuttons
-->
<script type="text/javascript" src="http://download.skype.com/share/skypebuttons/js/skypeCheck.js"></script>
<div>or Skype us: <a href="skype:allebrum?call"><img src="http://mystatus.skype.com/balloon/allebrum" style="border: none;" width="150" height="60" alt="My status" /></a></div>
</form>
