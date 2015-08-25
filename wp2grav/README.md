# WP2Grav

Export your Wordpress content into a content structure for getgrav.org

## Description

Ready to move to getgrav.org? This plugin allows you to export your posts, pages, authors, tags and categories into a filebased content structure for getgrav.org

The export is stored in /wp-content/uploads/wp2grav/export

Depending on your theme, addons etc. you might need to customize the plugin and.




## Installation

Upload the WP2Grav plugin to your site and activate it.


## Configuration

wp-content/plugins/wp2grav/includes/wp2grav.config.php

WP2GRAV_BLOG_MASTER_PAGE_ID

WP2GRAV_EXPORT_BATCH_SIZE

add the necessary code to be loaded to the function themeInit() which is called before the export process: 
/wp-content/plugins/wp2grav/includes/theme_init.php

adopt the format of the exported grav markdown files by editing the export templates:

Page:
/wp-content/plugins/wp2grav/templates/export/page.md

Post:
/wp-content/plugins/wp2grav/templates/export/post.md

Author:
/wp-content/plugins/wp2grav/templates/export/author.md


## Credits





## Changelog


### 1.0.0

*Release Date - 3rd March, 2015*

- Initial release.
- Does basic static file generation.
- Sends file if it exists only for `GET` requests.

## License

WP2Grav is released under the MIT License.

See [LICENSE](https://github.com/SiteBeez/wordpress-wp2grav-markdown-exporter/blob/master/wp2grav/LICENSE) for details.
