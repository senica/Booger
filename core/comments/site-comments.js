// JavaScript Document
var core_comments = {};

/***** Comment Form ******/
//Handle Logout
jQuery(".comment-form-form .not-you").click( function(){
	var parent = jQuery(this).parents(".comment-form-form:first");
	var text = jQuery("a", this);
	text.html("please wait");
	jQuery.post("ajax.php?file=admin/logout.php", {}, function(data){
		if(data == true){
			jQuery.post("ajax.php?file=admin/verify_cookies.php", {}, function(data){
				if(data == false){ //cookie not set anymore
					jQuery(".logged-in-as", parent).hide();
					jQuery(".name-wrapper, .email-wrapper, .website-wrapper", parent).show();
					jQuery(".name-input, .email-input, .website-input", parent).val("");
					jQuery(".userid", parent).val(0);
				}else{
					text.html("Check connection.");	
					setTimeout( function(){ text.html("not you?"); }, 1000);
				}
			});
		}
	});
	return false;
}); //End Logout


jQuery(".comment-form-form").live("submit", function(){
	var obj = {};
											  
	obj.name = jQuery(".name-input", this).val();
	obj.email = jQuery(".email-input", this).val();
	obj.website = jQuery(".website-input", this).val();
	obj.comment = jQuery(".comment-input", this).val();
	obj.pageid = jQuery(".comment-pageid", this).val();
	obj.userid = jQuery(".userid", this).val();
	obj.parent = jQuery(".parentid", this).val();
	
	//As provided by aSeptik : http://stackoverflow.com/questions/2855865/jquery-validate-e-mail-address-regex
	function emailcheck(emailAddress) {
		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	};
	
	jQuery("*", this).removeClass("error");
	var error = false;
	if(obj.name == ""){ jQuery(".name-input", this).addClass("error"); error=true; }
	if(obj.email == "" || !emailcheck(obj.email)){ jQuery(".email-input", this).addClass("error"); error=true; }
	if(obj.comment == ""){ jQuery(".comment-input", this).addClass("error"); error=true; }
	
	//If it's a reply set prepend to this form's sibling comment-children. otherwise set it to the global comment-list
	var form = jQuery(this);
	var is_reply = (form.hasClass("reply-form")) ? true : false;
	var prepend_to = (is_reply) ? form.parents(".comment-wrapper:first").find(".comment-child:first") : '.comment-list';
	
	if(!error){
		var inputs = jQuery("input, textarea", this); 
		inputs.attr("disabled", "true");
		var loading = jQuery(".loading", this);
		var button = jQuery(".submit-button", this);
		var buttonval = button.val();
		button.val('Posting...');
		loading.show();
		jQuery.post("ajax.php?file=core/comments/post.php", {obj:obj}, function(json){
			var json = jQuery.parseJSON(json);
			if(json.error == 'true'){
				button.val('Posted!');
				if(jQuery(".comment-list").get(0)){
					var author = (json.website != "") ? '<a href="'+json.website+'" target="_blank">'+json.name+'</a>' : json.name;
					jQuery(prepend_to).prepend(
						'<div class="comment-wrapper">'
							+'<input type="hidden" name="comment-id" class="comment-id" value="'+json.id+'" />'
							+'<div class="posted-by"><div class="posted-by-text">Posted by&nbsp;</div><div class="posted-by-author">'+author+'</div></div>'
							+'<div class="posted-on"><div class="posted-on-text">on&nbsp;</div><div class="posted-on-date">'+json.date+'</div></div>'
							+'<div class="reply"><a href="#">Reply</a></div>'
							+'<div class="comment-text">'+json.comment+'</div>'
							+'<div class="comment-form-wrapper"></div>'
							+'<div class="comment-child"></div>'
						+'</div>'								
					);	
				}
				if(is_reply){
					form.parents(".comment-form:first").hide();	
				}
			}else{
				button.val('Could not post.');	
			}
			setTimeout(function(){ button.val(buttonval); }, 2000);
			loading.hide();
			inputs.removeAttr("disabled");
		});
	}
	
	return false;
});



/***** Comment List *******/
//Load Comments
core_comments.more = function(obj, parent){
	jQuery.post("ajax.php?file=core/comments/get_comments.php", {obj:obj}, function(data){
		jQuery(".comment-list-list", parent).append(data);
		jQuery(".loaded", parent).val(parseInt(obj.loaded)+parseInt(obj.count));
		jQuery(".loading", parent).hide();
	});
}

jQuery(".comment-list .more-comments").click( function(){
	var parent = jQuery(this).parents(".comment-list:first");
	var obj = {};
	obj.count = jQuery(".count", parent).val();
	obj.pageid = jQuery(".pageid", parent).val();
	obj.loaded = jQuery(".loaded", parent).val();
	jQuery(".loading", parent).show();
	core_comments.more(obj, parent);
	return false;
});

//Force first set of comments
jQuery(".comment-list .more-comments").click();

jQuery(".comment-list .reply").live("click", function(){
	var parent = jQuery(this).parents(".comment-list:first");
	var container = jQuery(this).parents(".comment-wrapper:first").find(".comment-form-wrapper:first");
	var obj = {};
	obj.pageid = jQuery(".pageid", parent).val();
	obj.parent = jQuery(this).parents(".comment-wrapper:first").find(".comment-id:first").val();
	if(jQuery(".comment-list .comment-form").get(0)){
		var form = jQuery(".comment-list .comment-form");
		jQuery(".parentid", form).val(obj.parent);
		container.html(form.get(0));
		form.show();
	}else{
		jQuery.post("ajax.php?file=core/comments/get_form.php", {obj:obj}, function(data){
			container.html(data);																			
		});
	}
	return false;
});

jQuery(".comment-list .cancel-reply").live("click", function(){
	jQuery(this).parents(".comment-form:first").hide();
	return false;
});





