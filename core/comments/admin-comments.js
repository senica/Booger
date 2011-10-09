// JavaScript Document
jQuery("#core-comments-admin-dialog").dialog({autoOpen:false, width:600, height:500});
jQuery("#core-comments-admin-tabs").tabs();

//Total Comments
jQuery("#core-comments-sidebar-list .total, #core-comment-admin-dialog-comments").click( function(){
	jQuery("#core-comments-admin-tabs").tabs("select", 0);
	jQuery("#core-comments-admin-dialog").dialog('open');
	if(jQuery("#core-comments-admin-comments").html() == ""){
		jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:0, limit:10, type:'comments'}, function(html){
			jQuery("#core-comments-admin-comments").append(html);
			var load_images = function(offset, limit){
				var offset = offset + limit;
				jQuery("#core-comments-admin-comments").bind("scroll.comments_comments", function(event){
					if(jQuery(this).outerHeight() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
						jQuery("#core-comments-admin-comments").unbind(".comments_comments"); //Wait till request set loads before listening to scroll event again
						jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:offset, limit:limit, type:'comments'}, function(html){
							jQuery("#core-comments-admin-comments").append(html);
							load_images(offset, limit); //Rebind scrolling listener
						});	
					}												  
				});
			}; load_images(0, 10);
		});
	}
});

//Approved Comments
jQuery("#core-comments-sidebar-list .approved, #core-comment-admin-dialog-approved").live("click", function(){
	jQuery("#core-comments-admin-tabs").tabs("select", 1);
	jQuery("#core-comments-admin-dialog").dialog('open');
	if(jQuery("#core-comments-admin-approved").html() == ""){
		jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:0, limit:10, type:'approved'}, function(html){
			jQuery("#core-comments-admin-approved").append(html);
			var load_images = function(offset, limit){
				var offset = offset + limit;
				jQuery("#core-comments-admin-approved").bind("scroll.comments_approved", function(event){
					if(jQuery(this).outerHeight() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
						jQuery("#core-comments-admin-approved").unbind(".comments_approved"); //Wait till request set loads before listening to scroll event again
						jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:offset, limit:limit, type:'approved'}, function(html){
							jQuery("#core-comments-admin-approved").append(html);
							load_images(offset, limit); //Rebind scrolling listener
						});	
					}												  
				});
			}; load_images(0, 10);
		});
	}
});

//Pending Comments
jQuery("#core-comments-sidebar-list .pending, #core-comment-admin-dialog-pending").click( function(){
	jQuery("#core-comments-admin-tabs").tabs("select", 2);
	jQuery("#core-comments-admin-dialog").dialog('open');
	if(jQuery("#core-comments-admin-pending").html() == ""){
		jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:0, limit:10, type:'pending'}, function(html){
			jQuery("#core-comments-admin-pending").append(html);
			var load_images = function(offset, limit){
				var offset = offset + limit;
				jQuery("#core-comments-admin-pending").bind("scroll.comments_pending", function(event){
					if(jQuery(this).outerHeight() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
						jQuery("#core-comments-admin-pending").unbind(".comments_pending"); //Wait till request set loads before listening to scroll event again
						jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:offset, limit:limit, type:'pending'}, function(html){
							jQuery("#core-comments-admin-pending").append(html);
							load_images(offset, limit); //Rebind scrolling listener
						});	
					}												  
				});
			}; load_images(0, 10);
		});
	}
});

//Spam Comments
jQuery("#core-comments-sidebar-list .spam, #core-comment-admin-dialog-spam").click( function(){
	jQuery("#core-comments-admin-tabs").tabs("select", 3);
	jQuery("#core-comments-admin-dialog").dialog('open');
	if(jQuery("#core-comments-admin-spam").html() == ""){
		jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:0, limit:10, type:'spam'}, function(html){
			jQuery("#core-comments-admin-spam").append(html);
			var load_images = function(offset, limit){
				var offset = offset + limit;
				jQuery("#core-comments-admin-spam").bind("scroll.comments_spam", function(event){
					if(jQuery(this).outerHeight() + jQuery(this).scrollTop() >= jQuery(this)[0].scrollHeight){
						jQuery("#core-comments-admin-spam").unbind(".comments_spam"); //Wait till request set loads before listening to scroll event again
						jQuery.post("/ajax.php?file=core/comments/admin-get-comments.php", {offset:offset, limit:limit, type:'spam'}, function(html){
							jQuery("#core-comments-admin-spam").append(html);
							load_images(offset, limit); //Rebind scrolling listener
						});	
					}												  
				});
			}; load_images(0, 10);
		});
	}
});

jQuery("#core-comments-admin-tabs .status a").live("click", function(){
	var cls = jQuery(this).attr("class");
	var button = jQuery(this);
	var status = jQuery(this).parent();
	var id = button.parents(".comment-wrapper:first").attr("dbid");
	jQuery.post("/ajax.php?file=core/comments/admin-change-status.php", {id:id, status:cls}, function(html){
		if(html == "true"){
			jQuery("span.pending", status).replaceWith('<a href="#" class="pending">Make Pending</a>');
			jQuery("span.approved", status).replaceWith('<a href="#" class="approved">Approve</a>');
			//jQuery("a.notspam", status).replaceWith('<a href="#" class="spam">Spam</a>');
			if(cls == "pending"){
				button.replaceWith('<span class="pending">Pending</span>');	
			}else if(cls == "approved"){
				button.replaceWith('<span class="approved">Approved</span>');
			}else if(cls == "spam"){
				button.replaceWith('<a href="#" class="notspam">Not Spam</a>');
			}else if(cls == "notspam"){
				button.replaceWith('<a href="#" class="spam">Spam</a>');
				console.log(jQuery(".pending", status));
				jQuery(".pending", status).replaceWith('<span class="pending">Pending</span>');
			}
		}
	});
});


