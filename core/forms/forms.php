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
			
		case 'select':
			echo '<select class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" name="'.$opt->name.'">';
				foreach($opt->options as $k=>$v){
					echo '<option value="'.$v.'">'.$k.'</option>';	
				}
			echo '</select>';
			break;
			
		case 'textarea':
			echo '<textarea class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" name="'.$opt->name.'">'.$opt->value.'</textarea>';
			break;
			
		case 'submit':
			echo '<input class="'.$opt->ref.' '.((isset($opt->req))?'required':'').'" type="submit" name="'.$opt->name.'" value="'.$opt->value.'" />';
			break;
			
		case 'message':
			echo '<div class="'.$opt->ref.' forms-message"></div>';
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
		var firstp = first.parent();
		var lastp = last.parent();
		
		//Get a common parent. Don't use the actual form elements as siblings, because it will mess up text nodes.
		while(firstp[0] !== lastp[0]){
			//If parents are siblings, this will do for wrapping.
			var isS = false;
				firstp.siblings().each( function(index, el){ 
					if(jQuery(el)[0] === lastp[0]){
						isS = true;
						return true;
					}
				});
			if(isS === true){ break; }
			firstp = firstp.parent();
			lastp = lastp.parent();
		}
		
		//Wrap all of form elements in a form tag
		firstp.before('<form class="'+index+'" '+obj.string+'></form>');
		var form = firstp.prev();
		if(firstp[0] === lastp[0]){
			form.append(firstp);
		}else{
			while(firstp.next()[0] !== lastp[0]){
				form.append(firstp.next());
			}
			form.append(lastp);
			form.prepend(firstp);
		}
		
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
				var post = {};	
				jQuery("input[type=text],input[type=checkbox],input[type=radio]:checked,input[type=password],input[type=submit],textarea,select", form).each( function(index,el){
					var name = jQuery(el).attr("name");
					post[name] = {};
					post[name].value = jQuery(el).val();
					post[name].type = jQuery(el).attr("type");
					jQuery(el).attr("disabled", "true");
				});
				
				//Set pending message
				var message_field = jQuery(".forms-message", form);
				(obj.message && obj.message.pending) ? message_field.html(obj.message.pending) : message_field.html("Sending...");
				
				jQuery.post("ajax.php?file=core/forms/process.php", {obj:obj, post:post}, function(json){
					var r = jQuery.parseJSON(json);
					if(r.error == false){
						(obj.message && obj.message.complete) ? message_field.html(obj.message.complete) : message_field.html("Success");	
					}else{
						(obj.message && obj.message.error) ? message_field.html(obj.message.error): message_field.html("Failed");	
					}
					jQuery("input[type=text],input[type=checkbox],input[type=radio]:checked,input[type=password],input[type=submit],textarea,select", form).each( function(index,el){
						jQuery(el).removeAttr("disabled");
					});
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