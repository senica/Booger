<?php $bg->get_header(); ?>
<div id="header-separator" class="auto"><div id="header-separator-inner"><h3 class="header-separator-title">[page-title]</h3>[global {'name':'page-search'}]</div><div id="header-separator-shadow"></div></div>
<div id="content" class="color-3">
	[global {'name':'sidebar-left', 'class':'color-7 round-corners valign-top'}]
	[content {'class':'color-7 page-padding round-corners doc valign-top', 'default':'<b>[page-title]</b><br /><br />'}]
	<?php if($bg->comments_allowed()): ?>
		<div class="page-padding round-corners color-7 valign-top" style="width:536px; float:right; margin-top:10px; display:inline-block;">
			[comment-list]
			[comment-form]
		</div>
	<?php endif; ?>
	
	<div class="clearfix"></div>
</div>
<?php $bg->get_footer(); ?>