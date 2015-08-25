<?php
/*
Plugin Name: wp2grav
Plugin URI: http://www.php-welt.net/really-static/index.html
Description: Converts your blog into a static site.
Author: Shane Logsdon
Author URI: http://www.slogsdon.com/
Version: 1.3.0
*/


namespace wp2grav;

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WP2GRAV_VERSION')) {
    define('WP2GRAV_VERSION', '1.0');
}
require_once 'vendor/html-to-markdown/src/ConfigurationAwareInterface.php';
require_once 'vendor/html-to-markdown/src/Configuration.php';
require_once 'vendor/html-to-markdown/src/ElementInterface.php';
require_once 'vendor/html-to-markdown/src/Element.php';
require_once 'vendor/html-to-markdown/src/Environment.php';
require_once 'vendor/html-to-markdown/src/HtmlConverter.php';

require_once 'vendor/html-to-markdown/src/Converter/ConverterInterface.php';
$converterDir = ABSPATH . 'wp-content/plugins/wp2grav/vendor/html-to-markdown/src/Converter/';
if (is_dir($converterDir)) {
    foreach (scandir($converterDir) as $_converter) {
        /* Scan all files. */
        if (is_file($converterDir . $_converter)) {
            require_once($converterDir . $_converter);
        }
    }
}

require_once 'includes/theme_init.php';
// Support
require_once 'includes/wp2grav-view.class.php';

// Do the businesss
require_once 'includes/wp2grav.class.php';
require_once 'includes/wp2grav-admin.class.php';


$plugin = basename(__FILE__, '.php');
if (is_admin()) {
    new WP2GravAdmin($plugin, __FILE__);
} else {
    new WP2Grav($plugin);
}
