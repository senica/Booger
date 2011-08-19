<?php require(ASSETS.'/no_direct.php'); ?>
<?php

// Example: First Name[forms {'ref':'contact', 'type':'text', 'name':'first_name', 'req':1, 'method':'post', 'enctype':'multipart/form-data', 'action':{'db':'contact', 'email':'email@email.com,email2@email.com', 'file':'form.php'} }]
//If no action is set, then the form will email the Admin Email Address that is set in Site Settings

$bg->add_shortcode('forms', 'core_forms_func');
$bg->add_hook('shutdown', 'core_forms_shutdown');

$bg->core_forms = array(); //Create holder for form variables that can be accessed by the shutdown function


function core_forms_func($obj){
	global $bg;
	$opt = $obj->options;
	
	$bg->add_js('/assets/js/jquery-sha1.js', 'site-foot');
	$bg->add_js('/assets/js/jquery-formobj.js', 'site-foot');
	
	//This should probably be put into a logging function that can be seen from the debug in the admin
	//May not need ref parameter if they are doing something custom
	//if(!isset($opt->ref)){ echo "forms must have a 'ref' parameter.  example: &lsbkt;forms {'ref':'contact'}]"; return false; }
	
	//Build forms array that can be handled at shutdown
	if(!isset($bg->core_forms[$opt->ref])){ $bg->core_forms[$opt->ref] = array(); }
	array_push($bg->core_forms[$opt->ref], $opt);
	
	if(!isset($bg->core_forms[$opt->ref]['string'])){ $bg->core_forms[$opt->ref]['string'] = ''; }
	if(isset($opt->action)){ $bg->core_forms[$opt->ref]['action'] = $opt->action; }
	if(isset($opt->action->file)){ $bg->core_forms[$opt->ref]['string'] .= ' action="'.$opt->action->file.'"'; }
	if(isset($opt->method)){ $bg->core_forms[$opt->ref]['method'] = $opt->method; $bg->core_forms[$opt->ref]['string'] .= ' method="'.$opt->method.'"'; }
	if(isset($opt->enctype)){ $bg->core_forms[$opt->ref]['enctype'] = $opt->enctype; $bg->core_forms[$opt->ref]['string'] .= ' enctype="'.$opt->enctype.'"'; }
	if(isset($opt->accept_charset)){ $bg->core_forms[$opt->ref]['accept_charset'] = $opt->accept_charset; $bg->core_forms[$opt->ref]['string'] .= ' accept-charset="'.$opt->accept_charset.'"'; }
	if(isset($opt->accept)){ $bg->core_forms[$opt->ref]['accept'] = $opt->accept; $bg->core_forms[$opt->ref]['string'] .= ' accept="'.$opt->accept.'"'; }
	if(isset($opt->form_name)){ $bg->core_forms[$opt->ref]['form_name'] = $opt->form_name; $bg->core_forms[$opt->ref]['string'] .= ' name="'.$opt->form_name.'"'; }

	switch($opt->type){
			
		case 'text':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="text" name="'.$opt->name.'" value="'.$opt->value.'" />';
			break;
			
		case 'password':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="password" name="'.$opt->name.'" value="'.$opt->value.'" />';
			break;
			
		case 'hidden':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="hidden" name="'.$opt->name.'" value="'.$opt->value.'" />';
			break;
			
		case 'select':
			echo '<select class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" name="'.$opt->name.'">';
				foreach($opt->options as $k=>$v){
					echo '<option value="'.$v.'">'.$k.'</option>';	
				}
			echo '</select>';
			break;
			
		case 'checkbox':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="checkbox" name="'.$opt->name.'" value="'.$opt->value.'" '.((!empty($opt->checked)) ? 'checked' : '').' />';
			break;
			
		case 'textarea':
			echo '<textarea class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" name="'.$opt->name.'">'.$opt->value.'</textarea>';
			break;
		
		case 'image':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="image" name="'.$opt->name.'" src="'.$opt->value.'" />';
			break;
		
		case 'submit':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="submit" name="'.$opt->name.'" value="'.$opt->value.'" />';
			break;
			
		case 'message':
			//Made the following into an input field so it can be watched with js for a change
			echo '<div class="'.$opt->ref.' forms-message"><input class="'.$opt->ref.' forms-message-input" type="text" readonly="readonly" value="" style="border:0; background:none;" /></div>';
			if(isset($opt->pending)){ $bg->core_forms[$opt->ref]['message']['pending'] = $opt->pending; }
			if(isset($opt->complete)){ $bg->core_forms[$opt->ref]['message']['complete'] = $opt->complete; }
			if(isset($opt->error)){ $bg->core_forms[$opt->ref]['message']['error'] = $opt->error; }
			break;
		
		default:
			echo $opt->type.' is currently not handled in the forms plugin.';
			break;
	}
}

function core_forms_shutdown(){
	global $bg;
	
	//If in the admin, don't allow the form element to be added to html
	if(isset($_GET['noparse'])){ return false; }
	
	//Addslashes because php wasn't meant to be string literal for json.  See http://stackoverflow.com/questions/949604/json-parse-error-with-double-quotes
	$formobj = addslashes(json_encode($bg->core_forms));
	?>
	
	<script type="text/javascript">
	
	//Turn $formobj into a javascript object
	var core_forms = jQuery.parseJSON('<?php echo $formobj; ?>');
	
	//Process each form
	jQuery.each(core_forms, function(index, obj){
		var first = jQuery("."+index+":first");
		var last = jQuery("."+index+":last");
		//var firstp = first.parent();
		//var lastp = last.parent();
		
		//Get a common parent. Don't use the actual form elements as siblings, because it will mess up text nodes.
		var firstp = first.parents().filter(last.parents()).first();
		
		//Wrap parents children in form element -- Don't wrap the parent element as it may be the body or have special positioning
		jQuery('<form class="'+index+'" '+obj.string+'></form>').append(firstp.children()).prependTo(firstp);
		
		//Assign form element
		var form = firstp.children("form:first");
		
		//Handle required fields
		jQuery("form."+index).submit(function(event){
			var test = true;
			jQuery(".required", this).removeClass('blank');
			jQuery(".required", this).each(function(index, el){
				if(jQuery.trim(jQuery(el).val()) == ''){
					jQuery(el).addClass('blank');
					test = false;
				} 
			});
			
			//If no action is set, then we'll email the form to the admin, otherwise let the user handle the form
			//If db or email is set, we'll handle those and then let the form process
			if(test === true && (typeof obj.action == 'undefined' || typeof obj.action.db != 'undefined' || typeof obj.action.email != 'undefined') ){					
				//Get form fields
				var post = jQuery(form).formobj("disable");
				
				//Set pending message
				var message_field = jQuery(".forms-message-input", form);
				(obj.message && obj.message.pending) ? message_field.val(obj.message.pending) : message_field.val("Sending...");
				message_field.change(); //Force change so Js listeners can see the change
				
				jQuery.post("/ajax.php?file=core/forms/process.php", {obj:obj, post:post}, function(json){
					var r = jQuery.parseJSON(json);
					if(r.error == false){
						(obj.message && obj.message.complete) ? message_field.val(obj.message.complete) : message_field.val("Success");	
					}else{
						(obj.message && obj.message.error) ? message_field.val(obj.message.error): message_field.val("Failed");	
					}
					//message_field.trigger('change', [r, post, obj]); //Trigger to message input
					jQuery(form).trigger('change', [r, post, obj]); //Trigger to form.  If listener is from a plugin, will need to wrap in jQuery(document).ready as form element is not added till after document is loaded
					var post = jQuery(form).formobj("enable");
				});
			}
			
			//Handle ajax actions
			if(test === true && typeof obj.action != 'undefined' && typeof obj.action.ajax != 'undefined'){
				//Get form fields
				var post = jQuery(form).formobj("disable");	
				//Set pending message
				var message_field = jQuery(".forms-message-input", form);
				(obj.message && obj.message.pending) ? message_field.val(obj.message.pending) : message_field.val("Sending...");
				message_field.change();
				
				jQuery.post("/ajax.php?file="+obj.action.ajax, {obj:obj, post:post}, function(json){
					var r = jQuery.parseJSON(json);
					if(r.error == false){
						//For ajax calls, allow error and complete parameters to specify 'echo' which should just tranfer the ajax response if formed with obj.message
						(obj.message && obj.message.complete) ? ((obj.message.complete == 'echo') ? message_field.val(r.message) : message_field.val(obj.message.complete)) : message_field.val("Success");	
					}else{
						(obj.message && obj.message.error) ? ((obj.message.error == 'echo') ? message_field.val(r.message) : message_field.val(obj.message.error)) : message_field.val("Failed");	
					}
					//message_field.trigger('change', [r, post, obj]); //Trigger to message input
					jQuery(form).trigger('change', [r, post, obj]); //Trigger to form.  If listener is from a plugin, will need to wrap in jQuery(document).ready as form element is not added till after document is loaded
					jQuery(form).formobj("enable");
				});	
			}
			
			//If no file action is set, then we have to assume that the user does not want to process the form any further, just return false and rely on the above processing
			if(typeof obj.action == 'undefined' || typeof obj.action.file == 'undefined'){
				test = false;
			}
			return test;
		});
	});
	</script>
<?php } ?>