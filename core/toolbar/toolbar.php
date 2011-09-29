<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_hook('admin-storage', 'core_toolbar_storage');
$bg->add_hook('admin-foot', 'core_toolbar_foot');
$bg->add_css(THEME_URL.'/toolbar.css', 'site-head');

function core_toolbar_foot(){
	global $bg;
	
	//Toolbox for template variables
	echo '<div id="core-toolbar-tv-tools-wrapper">';
		echo '<div id="core-toolbar-close"></div>';
		$bg->call_hook('admin-tools');
		echo '<div class="style" style="position:relative;"><img src="'.URL.'/core/toolbar/images/styles.png" title="Styles" /><div class="styles-dropdown"></div></div>';
		echo '<div class="background"><img src="'.URL.'/core/toolbar/images/background.png" title="Set a Background Color or Image." /></div>';
		echo '<div class="columns"><img src="'.URL.'/core/toolbar/images/columns.png" title="Columns" /></div>';
		echo '<div class="previous-line"><img src="'.URL.'/core/toolbar/images/previous_line.png" title="Insert Fresh Line Before Current Location" /></div>';
		echo '<div class="next-line"><img src="'.URL.'/core/toolbar/images/next_line.png" title="Insert Fresh Line After Current Location" /></div>';
		echo '<div class="parent"><img src="'.URL.'/core/toolbar/images/parent.png" title="Show Parent Elements On Click" /></div>';
		echo '<div class="lorem"><img src="'.URL.'/core/toolbar/images/lorem.png" title="Lorem Ipsum - Filler Text" /><div class="lorem-dropdown">Number of Characters: <input type="text" class="characters" size="5" /><div class="button insert">Insert</div></div></div>';
		echo '<div class="hold"><img src="'.URL.'/core/toolbar/images/hold.png" title="Hold Formatting Containers" /></div>';
		echo '<div class="unwrap"><img src="'.URL.'/core/toolbar/images/unwrap.png" title="Remove Line Wrappers" /></div>';
		echo '<div class="anchor"><img src="'.URL.'/core/toolbar/images/anchor.png" title="Create Page Anchor/Bookmark" />
				<div class="anchor-dropdown">
					<div><span style="display:inline-block; width:60px">Name:</span> <input type="text" class="aname" size="10" /> <input type="button" class="apply" value="Apply" /> <input type="button" class="remove" value="Remove" /></div>
				</div>
			</div>';
		echo '<div class="link"><img src="'.URL.'/core/toolbar/images/link.png" title="Link to Pages" /></div>';
		echo '<div class="unlink"><img src="'.URL.'/core/toolbar/images/unlink.png" title="Remove a Link" /></div>';
		echo '<div class="margin" style="position:relative;"><img src="'.URL.'/core/toolbar/images/margin.png" title="Margins" />
				<div class="margin-dropdown">
					<div><span style="display:inline-block; width:60px">Top:</span><input class="top" type="range" min="0" max="100" value="0" /> <input type="text" class="topt" size="2" /><select class="topm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Right:</span><input class="right" type="range" min="0" max="100" value="0" /> <input type="text" class="rightt" size="2" /><select class="rightm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Bottom:</span><input class="bottom" type="range" min="0" max="100" value="0" /> <input type="text" class="bottomt" size="2" /><select class="bottomm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Left:</span><input class="left" type="range" min="0" max="100" value="0" /> <input type="text" class="leftt" size="2" /><select class="leftm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
				</div>
			</div>';
		echo '<div class="padding" style="position:relative;"><img src="'.URL.'/core/toolbar/images/padding.png" title="Padding" />
				<div class="padding-dropdown">
					<div><span style="display:inline-block; width:60px">Top:</span><input class="top" type="range" min="0" max="100" value="0" /> <input type="text" class="topt" size="2" /><select class="topm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Right:</span><input class="right" type="range" min="0" max="100" value="0" /> <input type="text" class="rightt" size="2" /><select class="rightm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Bottom:</span><input class="bottom" type="range" min="0" max="100" value="0" /> <input type="text" class="bottomt" size="2" /><select class="bottomm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Left:</span><input class="left" type="range" min="0" max="100" value="0" /> <input type="text" class="leftt" size="2" /><select class="leftm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
				</div>
			</div>';
		echo '<div class="width-height" style="position:relative;"><img src="'.URL.'/core/toolbar/images/width_height.png" title="Width & Height" />
				<div class="width-height-dropdown">
					<div><span style="display:inline-block; width:60px">Width:</span><input class="width" type="range" min="0" max="500" value="0" /> <input type="text" class="widtht" size="2" /><select class="widthm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
					<div><span style="display:inline-block; width:60px">Height:</span><input class="height" type="range" min="0" max="500" value="0" /> <input type="text" class="heightt" size="2" /><select class="heightm"><option value="px">px</option><option value="%">%</option><option value="em">em</option><option value="pt">pt</option></select></div>
				</div>
			</div>';
		echo '<div class="move"><img src="'.URL.'/core/toolbar/images/move.png" title="Move/Drag Elements Around. Use arrows for precision. Hold Shift for bigger moves." /></div>';
		echo '<div class="delete"><img src="'.URL.'/core/toolbar/images/delete.png" title="Delete Selected Element. Cannot be undone." /></div>';
		echo '<div class="html"><img src="'.URL.'/core/toolbar/images/html.png" title="View Template Variable HTML" /></div>';
		echo '<div class="shortcode"><img src="'.URL.'/core/toolbar/images/shortcode.png" title="Show All Shortcodes" /></div>';
	echo '</div>';
	
	//Shortcode Edit Box
	echo '<div id="core-toolbar-shortcode-options">';
		echo '<div class="edit" title="Edit Shortcode"></div>';
		echo '<div class="delete" title="Delete Shortcode"></div>';
		echo '<div class="flatten" title="Flatten Shortcode"></div>';
	echo '</div>';
	
	$bg->add_js(URL.'/core/toolbar/toolbar.js');
	$bg->add_css(URL.'/core/toolbar/toolbar.css');
}

function core_toolbar_storage(){
	global $bg;
	
	//Columns
	echo 	'<div id="core-toolbar-columns-dialog" title="Columns">
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Unique ID:</span><input type="text" class="id" size="10" /></div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Number of Columns:</span><input type="text" class="cols" size="4" value="3" /></div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Overall Width:</span><input type="text" class="owidth" size="4" value="100%" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Top Padding:</span><input type="text" class="tpadding" size="4" value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Bottom Padding:</span><input type="text" class="bpadding" size="4" value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Side Padding:</span><input type="text" class="spadding" size="4" value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Top Margin:</span><input type="text" class="tmargin" size="4"  value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Bottom Margin:</span><input type="text" class="bmargin" size="4" value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Column Side Margin:</span><input type="text" class="smargin" size="4" value="5px" /> (specify px,%,em,pt)</div>
				<div style="white-space:nowrap;"><div class="button insert">Insert Columns</div></div>
			</div>';
			
	//Background
	echo 	'<div id="core-toolbar-background" title="Background">
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Vertical Align:</span><select class="va"><option value="center">Center</option><option value="top">Top</option><option value="bottom">Bottom</option></select></div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Horizontal Align:</span><select class="ha"><option value="center">Center</option><option value="left">Left</option><option value="right">Right</option></select></div>
				<div style="white-space:nowrap;"><span style="display:inline-block; width:220px;">Repeat:</span><select class="repeat"><option value="no-repeat">No Repeat</option><option value="repeat-y">Repeat Y</option><option value="repeat-x">Repeat X</option><option value="repeat">Repeat Both</option></select></div>
				<div style="white-space:nowrap;" class="image"></div>
				<div style="white-space:nowrap;"><div class="button select-image">Select Image</div> <div class="button update">Update Background</div></div>
			</div>';
	
	//HTML Box
	echo 	'<div id="core-toolbar-html" title="HTML">
				<div><textarea class="html" wrap="off" style="width:460px; height:400px; font-size:12px;"></textarea></div>
				<div style="margin-top:10px;"><div class="button update">Update</div> Auto Update <input type="checkbox" class="auto"/></div>
			</div>';
			
	//Parents
	echo 	'<div id="core-toolbar-parent" title="Parents">
				<div class="parents"></div>
			</div>';
	
	//Link Box
	echo 	'<div id="core_toolbar_link" title="Link">
				<div class="text">Link Text <input class="text" type="text" /></div>
				<div style="font-size:small">If <i>Link Text</i> is blank, then the last selected element will be wrapped as a link.</div>
				<div class="alt">Alternate Text <input class="alt" type="text" /></div>
				<div class="target">Target <input class="target" type="text" /></div>
				<div style="font-size:small">_blank, _self, _top, _parent</div>
				<div class="extra">Extra<input class="extra" type="text" /></div>
				<div><br /></div>
				<div id="core_toolbar_link_wrapper">
					<h3 class="web"><a href="#">Web Link</a></h3>
					<div>URL <input class="web" type="text" /></div>
					<h3 class="page"><a href="#">Page Link</a></h3>
					<div><div style="height:50px;"><select class="page"></select></div></div>
					<h3 class="post"><a href="#">Post Link</a></h3>
					<div><div style="height:50px;"><select class="post"></select></div></div>
					<h3 class="image"><a href="#">Image Link</a></h3>
					<div><div style="height:50px;"><select class="image"></select></div></div>
					<h3 class="upload"><a href="#">Upload Link</a></h3>
					<div><div style="height:50px;"><select class="upload"></select></div></div>
					<h3 class="anchor"><a href="#">Anchor/Bookmark Link</a></h3>
					<div><div style="height:50px;"><select class="anchor"></select></div></div>
				</div>
				<div><br /></div>
				<div><div class="button ok" style="margin-right:10px;">OK</div><div class="button cancel">Cancel</div></div>
			</div>';
}
?>