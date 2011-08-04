<?php $bg->get_header(); ?>
<div class="home-featured">
	[blog-list-reset]
	[blog-list-post-image {'class':'home-featured-left', 'noimage':'[theme-url]/images/home-default.jpg'}]
	<div class="home-featured-right">
		<article>
		<h1 class="home-featured-title">[blog-list-post-title]</h1>
		<div class="home-featured-meta">
			<div class="home-featured-posted-by">Posted by [blog-list-post-author {'link':'true'}]</div><div class="home-featured-tags">Tags: [blog-list-post-tags {'link':'true'}]</div>
		</div>
		<div class="home-featured-content">[blog-list-post-content {'length':270, 'nohtml':'true'}]....</div>
		[blog-list-post-read-more {'class':'home-featured-more'}]
		</article>
	</div>
	<div class="clearfix"></div>
</div>
<div class="home-featured-bottom-divider"></div>
<div class="body-wrapper">
	<div class="content-wrapper">
		[content]
	</div>
	<div class="sidebar-wrapper">
		[global {'name':'sidebar'}]
	</div>
</div>
<?php $bg->get_footer(); ?>