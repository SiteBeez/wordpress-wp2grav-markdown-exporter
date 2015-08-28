<?php
/*

Plugin Name: wp2grav
Plugin URI: https://github.com/SiteBeez/wordpress-wp2grav-markdown-exporter/tree/master/wp2grav
Description: export your Wordpress into getgrav.org content files
Author: Cord | SiteBeez.com
Author URI: http://www.sitebeez.com/
Version: 1.2.0

*/


namespace wp2grav;

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP2GRAV_VERSION')) {
    define('WP2GRAV_VERSION', '1.2');
}
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/ConfigurationAwareInterface.php';
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/Configuration.php';
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/ElementInterface.php';
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/Element.php';
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/Environment.php';
require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/HtmlConverter.php';

require_once WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/Converter/ConverterInterface.php';
$converterDir = WP_PLUGIN_DIR . '/wp2grav/vendor/html-to-markdown/src/Converter/';
if (is_dir($converterDir)) {
    foreach (scandir($converterDir) as $_converter) {
        /* Scan all files. */
        if (is_file($converterDir . $_converter)) {
            require_once($converterDir . $_converter);
        }
    }
}

// load configuration
require_once WP_PLUGIN_DIR . '/wp2grav/includes/wp2grav.config.php';

if (WP2GRAV_EXPORT_HTMLPURIFIER) {
    require_once WP_PLUGIN_DIR . '/wp2grav/vendor/htmlpurifier/library/HTMLPurifier.auto.php';
}


// init theme
if (file_exists(WP_PLUGIN_DIR . '/wp2grav/includes/theme_init.php')) {
    require_once WP_PLUGIN_DIR . '/wp2grav/includes/theme_init.php';
}

// Support
require_once WP_PLUGIN_DIR . '/wp2grav/includes/wp2grav-view.class.php';

// Do the businesss
require_once WP_PLUGIN_DIR . '/wp2grav/includes/wp2grav.class.php';
require_once WP_PLUGIN_DIR . '/wp2grav/includes/wp2grav-admin.class.php';


$plugin = 'wp2grav/' . basename(__FILE__);
if (is_admin()) {
    new WP2GravAdmin($plugin, __FILE__);
    if ($_POST['wp2grav-action']) {
        // set screen context to site
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $GLOBALS['current_screen'] = \WP_Screen::get('front');
    }
} else {
    new WP2Grav($plugin);
}

