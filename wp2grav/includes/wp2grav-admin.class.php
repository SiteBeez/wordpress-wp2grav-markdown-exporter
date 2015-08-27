<?php

namespace wp2grav;

use \Exception;
use \WP_Query;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP2GravAdmin Plugin class
 *
 * Provides admin functionality.
 *
 * @package wp2grav
 * @version 1.0.0
 * @author  sitebeez
 */


class WP2GravAdmin extends WP2Grav
{
    protected $file = null;

    /**
     * Sets up necessary bits.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct($plugin, $file = __FILE__)
    {
        $this->file = $file;
        parent::__construct($plugin);
        $this->initHooks();
    }

    /**
     * activate plugin
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function activate()
    {
        $notice = 'Thanks for installing WP2Grav! Might we suggest <a href="'
            . admin_url('admin.php?page=wp2grav-export')
            . '">export site</a>?';
        $this->addNotice($notice);
    }

    /**
     * Creates menu for WP2Grav admin pages.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function addMenu()
    {
        add_menu_page(
            __('WP2Grav', $this->plugin),
            __('WP2Grav', $this->plugin),
            'manage_options',
            $this->plugin,
            array($this, 'InfoPage')
        );
        add_submenu_page(
            $this->plugin,
            __('WP2Grav Export', $this->plugin),
            __('Export', $this->plugin),
            'manage_options',
            $this->plugin . '-export',
            array($this, 'exportPage')
        );
    }

    /**
     * Cleans up after itself on deactivation.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function deactivate()
    {

        delete_option('wp2grav_version');
        delete_option('wp2grav_deferred_admin_notices');
    }

    /**
     * Displays admin notices.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function displayNotices()
    {
        if ($notices = get_option('wp2grav_deferred_admin_notices')) {
            foreach ($notices as $notice) {
                $message = $notice;
                $type = 'updated';

                if (is_array($notice)) {
                    $message = isset($notice['message']) ? $notice['message'] : '';
                    $type = isset($notice['type']) ? $notice['type'] : $type;
                }

                echo '<div class="' . $type . '"><p>' . $message . '</p></div>';
            }
            delete_option('wp2grav_deferred_admin_notices');
        }
    }

    /**
     * Error handler to convert errors to exceptions to make it
     * easier to catch them.
     *
     * @param int $num
     * @param string $mes
     * @param string $file
     * @param int $line
     * @param array $context
     *
     * @return bool
     */
    public static function errorToException($num, $mes, $file = null, $line = null, $context = null)
    {
        throw new Exception($mes, $num);
    }

    /**
     * Handles form submission on WP2Grav admin pages.
     *
     * @return void
     */
    public function handlePost()
    {
        if (!isset($_POST['wp2grav-action'])) {
            return;
        }
        if (!check_admin_referer('wp2grav')) {
            return;
        }

        switch ($_POST['wp2grav-action']) {
            case 'export':
                $this->export();
                break;
            default:
                break;
        }
    }

    /**
     * Displays info page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function infoPage()
    {
        WP2GravView::page('admin/info');
    }

    /**
     * Hooks on to necessary actions/filters for the
     * administration end of the plugin.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function initHooks()
    {
        register_activation_hook($this->file, array($this, 'activate'));
        register_deactivation_hook($this->file, array($this, 'deactivate'));

//        add_action('save_post', array($this, 'updateHtml'), 10, 2);

        add_action('admin_init', array($this, 'handlePost'));
        add_action('admin_init', array($this, 'update'));
        add_action('admin_menu', array($this, 'addMenu'));
        add_action('admin_notices', array(__CLASS__, 'displayNotices'));
    }

    /**
     * Displays export page.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function exportPage()
    {
        WP2GravView::page('admin/export');
    }

    /**
     * Handles plugin updates.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update()
    {
        $version = get_option('wp2grav_version');
        if ($version != WP2GRAV_VERSION) {
            update_option('wp2grav_version', WP2GRAV_VERSION);
        }
    }

    /**
     * Pushes a given notice to be displayed.
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function addNotice($notice)
    {
        $notices = get_option('wp2grav_deferred_admin_notices', array());
        $notices[] = $notice;
        update_option('wp2grav_deferred_admin_notices', $notices);
    }


    /**
     * qtranslate support
     *
     * @since 1.0.0
     *
     * @return $qt
     */
    function qt_settings()
    {
        $qt = array("enabled" => false);
        if (function_exists('qtrans_getAvailableLanguages') && function_exists('qtrans_convertURL')) {
            global $q_config;
            $qt["enabled"] = true;
            $qt["default_language"] = $q_config['default_language'];
            $qt["enabled_languages"] = $q_config['enabled_languages'];
            $qt["hide_default_language"] = $q_config['hide_default_language'];
            return $qt;
        } else {
            return false;
        }
    }

    /**
     * Loops through posts and pages to compile md files for each.
     *
     * @since 1.0.0
     *
     * @return void
     */
    protected function export()
    {
        // export blog items
        // page id for exporting blog posts
        $blogPageId = (int)WP2GRAV_BLOG_MASTER_PAGE_ID;



        // Load qTranslate settings if available
        $qt = $this->qt_settings();
        if (is_array($qt)) {
            // found qtranslate_slug
            global $qtranslate_slug;
            $qtranslate_slug = (isset($qtranslate_slug)) ? $qtranslate_slug : null;
        } else {
            $qtranslate_slug = false;
        }

        // generate tree array of content structure
        // array(
        //  'page' => $pageObj,
        //  'subtree' => $subTreeArray
        //)

        $GLOBALS['WP2GRAV_CNT'] = 0;

        // export authors
        $this->writeMeta();

        // get root items
        $args = array(
            "echo" => 0,
            "sort_order" => "ASC",
            "sort_column" => "menu_order",
            "parent" => 0
        );

        // export pages
        $contentArray = wp2grav_get_pages($args, 'page');
        $this->processExport($contentArray, $qt, $qtranslate_slug, null, 'default');


        // reset content tree
        wp2grav_content_tree::$arr_all_pages_id_parent = null;
        // export Blog pages
        $contentArray = wp2grav_get_pages($args, 'post');

        $blogPageArray = array(
            array(
                'page' => get_post($blogPageId),
                'subtree' => $contentArray
            )
        );

        //override pageFileName property
        $blogPageArray[0]['page']->pageFileName = 'blog';

        $this->processExport($blogPageArray, $qt, $qtranslate_slug, null, 'item');


        $this->addNotice(WP2GravView::notice('admin/export-success'). $this->destination);

        wp_reset_postdata();

        // emulate wp_safe_redirect();
        $location = admin_url('admin.php?page=' . $this->plugin . '-export');
        $location = wp_sanitize_redirect($location);
       	$location = wp_validate_redirect($location, admin_url());
        ?>
        <script type="text/javascript">
            location.href = "<?php echo $location; ?>";
        </script>
        <?php
        exit();
    }

    /**
     * Recursively deletes a directory and its contents.
     *
     * @since 1.0.0
     * @param string $dir
     *
     * @return void
     */
    protected function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }
}


