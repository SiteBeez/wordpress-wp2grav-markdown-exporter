<?php
// theme specific initialization code

function themeInit()
{
    if (file_exists(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_actions.php')) {
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/rss.php');

        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_actions.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_actions_add.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_affiliate_init.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_filters.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_platform_init.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_download_configuration.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_affiliate_package_maker.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_shortcodes.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/markdown.php');
        include_once(WP_CONTENT_DIR . '/themes/awake-mailbeez/php/mailbeez_zip.php');

        mailbeez_init();
        __mysite_shortcodes_init();

        $platform_mailhive_comp_mapping = array('oscommerce' => 'osc',
            'creloaded' => 'cre',
            'digistore' => 'digi',
            'zencart' => 'zencart',
            'xtc' => 'xtc',
            'gambio' => 'gambio');


        $data = array();
        $dl = get_downloads();
        foreach ($dl as $d) {
            $compatibility = array();
            foreach (array_values($platform_mailhive_comp_mapping) as $comp) {
                if ($d->meta['comp_' . $comp] == 'COMP_OK') {
                    $compatibility[] = 'comp_' . $comp;
                }
            }

            $data[$d->meta['code']] = array(
                'compatibility' => implode(',', $compatibility),
                'changelog' => $d->meta['changelog'],
                'thumbnail' => $d->meta['thumbnail'],
                'compatibility_note' => $d->meta['comp_note'],
                'partof_essential' => $d->meta['partof_essential'], // PARTOF_YES
                'mc_ready' => $d->meta['mc_ready'], // MCREADY_YES
                'price' => $d->meta['price'],
                'pro' => $d->meta['pro'], // pro
                'cert' => $d->meta['cert'], // true
                'title_en' => $d->title,
                'teaser_en' => $d->meta['teaser_en'],
                'title_de' => $d->meta['title_de'],
                'teaser_de' => $d->meta['teaser_de'],
                'author' => $d->meta['author'],
            );
        }

        $GLOBALS['EOL_DL'] = $data;
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