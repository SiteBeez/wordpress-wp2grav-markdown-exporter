<?php

namespace wp2grav;

use League\HTMLToMarkdown\HtmlConverter;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP2Grav Plugin class
 *
 * Converts your blog into a static site.
 *
 * @package wp2grav
 * @version 1.0.0
 * @author  sitebeez
 */
class WP2Grav
{
    protected $destination = null;
    protected $plugin = null;

    /**
     * Sets up necessary bits.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct($plugin)
    {
        // make first plugin loaded to allow to set screen context
        add_action('activated_plugin', array($this, 'load_first'));

        $this->plugin = $plugin;
        $this->destination = $this->resolveDestination();
        $this->initHooks();
    }


    /**
     * Hooks on to necessary actions/filters for the
     * business end of the plugin.
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function initHooks()
    {
        //add_action('comment_post', array($this, 'addComment'), 10, 2);
    }


    public function processExport($contentArray, $qt, $qtranslate_slug, $currentUri = null, $pageFileName = 'default')
    {
        $languages = ($qtranslate_slug) ? $qt["enabled_languages"] : false;
        $this->themeInit();

        foreach ($contentArray as $idx => $contentItem) {
            $pageObj = $contentItem['page'];
            $subtreeData = $contentItem['subtree'];

            $permalink = get_permalink($pageObj->ID);

            if ($languages) {
                if ($qtranslate_slug) {
                    // some tricks to get the qtranslate slug
                    if ($pageObj->post_type == 'page') {
                        $q = $qtranslate_slug->filter_request(array('pagename' => $pageObj->post_name));
                    } else {
                        $q = $qtranslate_slug->filter_request(array('name' => $pageObj->post_name));
                    }
                } else {
                    $slug = $pageObj->page_name;
                }

                $addedUri = null;
                foreach ($languages as $language) {
                    if ($qtranslate_slug) {
                        $slugUrl = $qtranslate_slug->get_current_url($language);

                        $slugUrl = remove_query_arg(array('page', 'lang'), $slugUrl);
                        $slugUrl = str_replace('?page_id=', 'ID-', $slugUrl);

                        //echo "$language: " . $slugUrl . "<br>";
                        // get slug of current page
                        $slug = $this->_getLastPart($slugUrl);
                    }

                    $addedUri = $this->writeExport($language, $permalink, $slug, $pageObj, $qtranslate_slug, $idx, $currentUri, $pageFileName);
                }
            } else {
                $slug = $pageObj->post_name;
                $addedUri = $this->writeExport(null, $permalink, $slug, $pageObj, null, $idx, $currentUri, $pageFileName);
            }


            // process subtree
            if (is_array($subtreeData)) {
                $this->processExport($subtreeData, $qt, $qtranslate_slug, $currentUri . $addedUri, $pageFileName);
            }
        }
    }


    public function writeExport($language, $permalink, $slug, $post = null, $qtranslate_slug, $idx = 0, $currentUri = '/', $pageFileName = 'default')
    {
        $plugin_dir = plugin_dir_path(__FILE__);
        if ($post == null) {
            return false;
        }

        if ($post->post_type == 'page') {
            // add 01. style sorting to pages
            $idx_str = str_pad($idx + 1, 2, '0', STR_PAD_LEFT);
            $addedUri = $idx_str . '.' . $this->_getLastPart($permalink) . '/';
        } else {
            // all other types
            $addedUri = $this->_getLastPart($permalink) . '/';
        }

        if (isset($post->pageFileName)) {
            $pageFileName = $post->pageFileName;
        }

        $language_str = ($language) ? '.' . $language : '';

        $filename = $this->destination . '/' . $currentUri . $addedUri . $pageFileName . $language_str . '.md';
        $dir = $this->destination . '/' . $currentUri . $addedUri;

        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
        } else {
//            echo "addedUri: $addedUri \n";
        }

        // already generated
        if (is_file($filename)) {
            return $addedUri;
        }
        echo "\n\n<hr><b>generate Page $addedUri</b>\n";
        if ($language) {
            echo "\n\n<br>language: $language\n";
        }
        echo "<br>permalink: $permalink\n";
        echo "<br>page-id $post->ID\n";


        if ($language) {
            $title = qtrans_use($language, $post->post_title, false);
            $content = $this->processContent(qtrans_use($language, $post->post_content, false));
        } else {
            $title = get_the_title($post->ID);
            $content = $this->processContent($post->post_content);
        }


        $author_id = $post->post_author;

        echo "<br>title: $title\n";
        echo "<br>slug: $slug\n";
        echo "<br>author: ";
        echo the_author_meta('display_name', $author_id);

        echo get_the_author();
        echo "\n";

        echo "<br>category_list: ";
        echo strip_tags(get_the_category_list(', ', '', $post->ID));
        echo "\n";

        echo "<br>tag list: ";

        if (get_the_tags($post->ID)) {
            foreach (get_the_tags($post->ID) as $tag) {
                $t[] = $tag->name;
            }
            echo implode(', ', $t);
        }


        // todo: $post->post_status != 'publish'


        // include export template by type
        ob_start();
        include($plugin_dir . '../templates/export/' . $post->post_type . '.md');
        $content = ob_get_contents();
        ob_end_clean();
        file_put_contents($filename, $content);

        $GLOBALS['WP2GRAV_CNT']++;
        $cnt = WP2GRAV_EXPORT_BATCH_SIZE;
        if ($GLOBALS['WP2GRAV_CNT'] > $cnt) {
            echo "<hr>generated $cnt pages";
            ?>
            <script type="text/javascript">
                location.reload();
            </script>
            <?php
            exit();
        }

        return $addedUri;
    }

    private function _getLastPart($url)
    {
        $url_parts = explode('/', $url);
        $slug = array_pop($url_parts); // get last part of url

        // slug might end with /
        if ($slug == '') {
            $slug = array_pop($url_parts); // get last part of url
        }
        return $slug;
    }


    public function writeMeta()
    {
        // authors
        $this->writeMetaAuthors();

        // tags
        $tags = get_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                $tag_array[] = $tag->name;
            }
        }

        // categories
        // http://codex.wordpress.org/Function_Reference/get_categories
        $categories = get_categories();
        if ($categories) {
            foreach ($categories as $cat) {
                $cat_array[] = $cat->cat_name;

            }
        }

        if (is_array($tag_array)) {
            file_put_contents($this->destination . '/tags.txt', implode("\n", $tag_array));
        }

        if (is_array($cat_array)) {
            file_put_contents($this->destination . '/categories.txt', implode("\n", $cat_array));
        }

    }


    public function writeMetaAuthors()
    {
        // authors

        global $wpdb;
        $plugin_dir = plugin_dir_path(__FILE__);
        $dir = $this->destination . '/authors/';

        $defaults = array(
            'orderby' => 'name', 'order' => 'ASC', 'number' => '',
            'optioncount' => false, 'exclude_admin' => false,
            'show_fullname' => false, 'hide_empty' => true,
            'feed' => '', 'feed_image' => '', 'feed_type' => '', 'echo' => true,
            'style' => 'list', 'html' => false, 'exclude' => '', 'include' => ''
        );

        $authors = get_users($defaults);

        foreach ((array)$wpdb->get_results("SELECT DISTINCT post_author, COUNT(ID) AS count FROM $wpdb->posts WHERE post_type = 'post' AND " . get_private_posts_cap_sql('post') . " GROUP BY post_author") as $row) {
            $author_count[$row->post_author] = $row->count;
        }

        foreach ($author_count as $author_id => $cnt) {
            $author = get_userdata($author_id);

            $authorDir = $dir . $author->user_nicename;

            $authorFile = $authorDir . '/author.md';
            if (!is_dir($authorDir)) {
                wp_mkdir_p($authorDir);
            }

            // include export template by type
            ob_start();
            include($plugin_dir . '../templates/export/author.md');
            $content = ob_get_contents();
            ob_end_clean();

//        $data = file_get_contents($permalink);
            file_put_contents($authorFile, $content);

        }


        // category, tags

    }

    public function themeInit()
    {
        // theme specific initialization
        // e.g. to load shortcodes

        // defined in inludes/theme_init.php
        if (function_exists('themeInit')) {
            themeInit();
        }
    }

    public function processContent($content)
    {
        $content = apply_filters('the_content', $content);

        // make sure all shortcodes are resolved
        $content = do_shortcode($content);
        $content = do_shortcode($content);
        $content = do_shortcode($content);


        // issue: e.g. a "</p>" tag breaks the converter
        // try to remove these invalid content

        $converter = new HtmlConverter(array(
                'strip_tags' => true,
                'header_style' => 'atx'
            )
        );
        try {

            $content_converted = $converter->convert($content);

        } catch (\InvalidArgumentException $e) {

            if (WP2GRAV_EXPORT_HTMLPURIFIER) {

                if (!isset($this->purifier)) {
                    // http://htmlpurifier.org/
                    $config = \HTMLPurifier_Config::createDefault();
                    $this->purifier = new \HTMLPurifier($config);
                }

                $clean_content = $this->purifier->purify($content);
                $content_converted = $converter->convert($clean_content);

                echo "<hr>catched html convert exception " . $e->getMessage() . " and cleaned content<hr>";
            } else {
                $e->getMessage();
                exit();
            }


        }
//        $converter->setOption('italic_style', '_');
//        $converter->setOption('bold_style', '__');
        return $content_converted;
    }

    /**
     * Recursively deletes a directory and its contents.
     *
     * @since 1.2.0
     *
     * @return string
     */
    protected function resolveDestination()
    {
        $dir = '';
        $uploads = wp_upload_dir();

        if (isset($uploads['basedir'])) {
            $dir = $uploads['basedir'] . '/' . basename($this->plugin, '.php') . '/export';
        } else {
            $dir = WP_CONTENT_DIR . '/uploads/' . basename($this->plugin, '.php') . '/export';
        }

        return $dir;
    }


    function load_first()
    {
        $path = 'wp2grav/wp2grav.php';
        if ($plugins = get_option('active_plugins')) {
            if ($key = array_search($path, $plugins)) {
                array_splice($plugins, $key, 1);
                array_unshift($plugins, $path);
                update_option('active_plugins', $plugins);
            }
        }
    }
}


// thx to admin-menu-tree-page-view

class wp2grav_content_tree
{

    public static $arr_all_pages_id_parent;
    public static $one_page_parents;

    static function get_all_pages_id_parent($post_type = 'page')
    {
        if (!is_array(wp2grav_content_tree::$arr_all_pages_id_parent)) {
            // get all pages, once, to spare some queries looking for children
            $all_pages = get_posts(array(
                "numberposts" => -1,
                "post_type" => $post_type,
                "post_status" => "any",
                "fields" => "id=>parent"
            ));
            //print_r($all_pages);exit;
            wp2grav_content_tree::$arr_all_pages_id_parent = $all_pages;
        }
        return wp2grav_content_tree::$arr_all_pages_id_parent;
    }

    static function get_post_ancestors($post_to_check_parents_for)
    {
        if (!isset(wp2grav_content_tree::$one_page_parents)) {
            wp_cache_delete($post_to_check_parents_for, 'posts');
            $one_page_parents = get_post_ancestors($post_to_check_parents_for);
            wp2grav_content_tree::$one_page_parents = $one_page_parents;
        }
        return wp2grav_content_tree::$one_page_parents;
    }

}


function wp2grav_get_pages($args, $post_type = 'page')
{

    $defaults = array(
        "post_type" => $post_type,
        "parent" => "0",
        "post_parent" => "0",
        "numberposts" => "-1",
        "orderby" => "menu_order",
        "order" => "ASC",
        "post_status" => "any",
        "suppress_filters" => 0 // suppose to fix problems with WPML
    );
    $args = wp_parse_args($args, $defaults);

    // contains all page ids as keys and their parent as the val
    $arr_all_pages_id_parent = wp2grav_content_tree::get_all_pages_id_parent($post_type);

    $pages = get_posts($args);

    $data_array = array();

    // go through pages

    foreach ($pages as $one_page) {
        // add num of children to the title
        // @done: this is still being done for each page, even if it does not have children. can we check if it has before?
        // we could fetch all pages once and store them in an array and then just check if the array has our id in it. yeah. let's do that.
        // if our page id exists in $arr_all_pages_id_parent and has a value
        // so result is from 690 queries > 474 = 216 queries less. still many..
        // from 474 to 259 = 215 less
        // so total from 690 to 259 = 431 queries less! grrroooovy
        if (in_array($one_page->ID, $arr_all_pages_id_parent)) {
            $post_children = get_children(array(
                "post_parent" => $one_page->ID,
                "post_type" => $post_type
            ));
            $post_children_count = sizeof($post_children);
        } else {
            $post_children_count = 0;
        }

        $child_data_array = null;
        if ($post_children_count > 0) {

            $args_childs = $args;
            $args_childs["parent"] = $one_page->ID;
            $args_childs["post_parent"] = $one_page->ID;
            $args_childs["child_of"] = $one_page->ID;

            // can we run this only if the page actually has children? is there a property in the result of get_children for this?
            // eh, you moron, we already got that info in $post_children_count!
            // so result is from 690 queries > 474 = 216 queries less. still many..
            $child_data_array = wp2grav_get_pages($args_childs, $post_type);
        }

        $data_array[] = array(
            'page' => $one_page,
            'subtree' => $child_data_array
        );


    }


    return $data_array;
}



