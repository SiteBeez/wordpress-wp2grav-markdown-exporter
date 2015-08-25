<?php
// theme specific initialization code

function themeInit()
{

    if (file_exists(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_actions.php')) {

        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_actions.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/rss.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_affiliate_init.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_platform_init.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_download_configuration.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_affiliate_package_maker.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_zip.php');

        mailbeez_init();
        __mysite_shortcodes_init();
    }
}


if (!function_exists('__mysite_shortcodes_init')) :
    /**
     *
     */
    function __mysite_shortcodes_init()
    {
        if (!function_exists('mysite_shortcodes')) {
            return false;
        }
//        echo "*** mysitemyway Init ***";

        foreach (mysite_shortcodes() as $shortcodes)
            require_once THEME_SHORTCODES . '/' . $shortcodes;


        # Long posts should require a higher limit, see http://core.trac.wordpress.org/ticket/8553
        @ini_set('pcre.backtrack_limit', 9000000);

        foreach (mysite_shortcodes() as $shortcodes) {
            $class = 'mysite' . ucfirst(preg_replace('/[0-9-_]/', '', str_replace('.php', '', $shortcodes)));
            $class_methods = get_class_methods($class);

            foreach ($class_methods as $shortcode)
                if ($shortcode[0] != '_' && $class != 'mysiteLayouts') {
                    add_shortcode($shortcode, array($class, $shortcode));
                }
        }
    }
endif;