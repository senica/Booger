/**
 * Handle saving of files from EditArea when CTRL+s is pushed
 * When CTRL+save is pushed, this allows you to define a parameter called bg_save from the init function and then specify
 * a function to call.  The parameters are the edit area id and the content.
 * Example:
 * editAreaLoader.init({
		id : "edit"+t		// textarea id
		,syntax: "php"			// syntax to be uses for highgliting
		,start_highlight: true		// to display with highlight mode on start-up
		,min_width:document.width
		,min_height:document.height-28
		,allow_toggle: false
		,language: "en"
		,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight, word_wrap"
		,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql"
		,word_wrap: true
		,is_multi_files: false
		,show_line_colors: true
		,replace_tab_by_spaces: false
		,plugins:"bgsave"
		,bg_ctrl_save:function(id, content){
			alert(content);	
		}
	});
 */
var EditArea_bgsave= {
	onkeydown: function(e){
		if(e.ctrlKey && e.keyCode == 83){
			if(editArea.settings["bg_ctrl_save"].length>0){
				var func = editArea.settings['bg_ctrl_save'];
				func.call(this, editArea.id, editArea.textarea.value);
				return false;
			}
		}
		return true;
	}
};

// Adds the plugin class to the list of available EditArea plugins
editArea.add_plugin("bgsave", EditArea_bgsave);
