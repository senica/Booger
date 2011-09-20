<?php require(ASSETS.'/no_direct.php'); ?>
<?php
$bg->add_shortcode('theme-url', 'core_misc_shortcodes_theme_url');
$bg->add_shortcode('theme-dir', 'core_misc_shortcodes_theme_dir');
$bg->add_shortcode('site-title', 'core_misc_shortcodes_site_title');
$bg->add_shortcode('site-url', 'core_misc_shortcodes_site_url');

function core_misc_shortcodes_theme_url($obj){ echo THEME_URL; }
function core_misc_shortcodes_theme_dir($obj){ echo THEME_DIR; }
function core_misc_shortcodes_site_title($obj){ global $bg; echo $bg->settings->site_name; }
function core_misc_shortcodes_site_url($obj){ echo URL; return 'noparse'; } //Don't parse in admin since it is often used in anchors
?>