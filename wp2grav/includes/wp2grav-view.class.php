<?php

namespace wp2grav;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WP2GravView class
 *
 * Provides helpers for loading template files
 *
 * @package static-wp
 * @version 1.3.0
 * @author  slogsdon
 */
class WP2GravView
{
    /**
     * Loads notice templates.
     *
     * @since 1.3.0
     * @param string $slug
     *
     * @return string|array
     */
    public static function notice($slug, $type = null)
    {
        ob_start();
        self::template($slug, 'notice');

        $result = null;
        if ($type == null) {
            $result = ob_get_flush();
        } else {
            $result = array(
                'message' => ob_get_flush(),
                'type'    => $type,
            );
        }

        return $result;
    }

    /**
     * Loads page templates.
     *
     * @since 1.3.0
     * @param string $slug
     *
     * @return void
     */
    public static function page($slug)
    {
        self::template($slug, 'page');
    }

    /**
     * Loads templates.
     *
     * @since 1.3.0
     * @param string $slug
     * @param string $type
     *
     * @return void
     */
    protected static function template($slug, $type)
    {
        include plugin_dir_path(__FILE__)
            . '../templates/' . $slug . '.' . $type . '.php';
    }
}
