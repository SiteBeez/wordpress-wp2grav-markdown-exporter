# WP2Grav - Export your Wordpress Site into getgrav.org content structure

Export your Wordpress posts, pages and more into a markdown content structure for [Grav - A Modern Flat-File CMS](getgrav.org)

## Description

Ready to move to [Grav - A Modern Flat-File CMS](getgrav.org)? This plugin allows you to export your posts, pages, authors, tags and categories into a markdown file based content structure for getgrav.org

The export will be stored in 

	/wp-content/uploads/wp2grav/export
	
using the slug structure of Wordpress - so you will keep your Urls!

The multilanguage addons **qtranslate** and **qtranslate slug** are supported to export into the getgrav.org multilanguage file format keeping language specific slugs.

Depending on your Wordpress installation you will need to review and edit the generated fileset.

Consider this plugin as "good enough to do the job". 


## Installation

Upload the WP2Grav plugin to your site and activate it.


## Configuration

the exporter should work with the default settings, but there are a number of options to tweak with:

### set a master page to hold blog posts

in getgrav.org you might want to store all blog posts within a page, e.g.  `01.blog` page. 
You can achieve this by creating a new page in wordpress (this page will be exported as a folder where all blog post files are stored) and assign the postId (you find it in the url while editing the page) to constant
    
    define('WP2GRAV_BLOG_MASTER_PAGE_ID', '<pageId>');

located in 

    wp-content/plugins/wp2grav/includes/wp2grav.config.php


### export batch size / php timeouts

to avoid php timeout issues for large sites you can change the batch size defined in constant:

    define('WP2GRAV_EXPORT_BATCH_SIZE', 100);

located in 

    wp-content/plugins/wp2grav/includes/wp2grav.config.php

the exporter will load itself again until all content is exported.


### add custom code
rename

    /wp-content/plugins/wp2grav/includes/_theme_init.php
    
into

    /wp-content/plugins/wp2grav/includes/theme_init.php

add the necessary code to be loaded to the function themeInit() which is called before the export process.


### adopt export files format

adopt the format of the exported grav markdown files by editing the export templates:

    Page:
    /wp-content/plugins/wp2grav/templates/export/page.md

    Post:
    /wp-content/plugins/wp2grav/templates/export/post.md

    Author:
    /wp-content/plugins/wp2grav/templates/export/author.md



### Tips

- the exporter will not overwrite already generated files. So if you want to generate a certain subset of pages again just delete them from the export directory and start the exporter.
- you can export all pages and then change the template configuration in the page template. When you delete a certain folder and run the exporter again the markdown files are generated based on the new page template)
- you can use your IDE to search / replace within the folder structure to change configuration values
- check the files categories.txt and tags.txt - these files contain all categories and tags and make it easy to clean up this data, e.g. to remove similar / duplicate values


## qtranslate fixes

### export date issue

open

	/wp-content/plugins/qtranslate/qtranslate_core.php
 
in

	function qtrans_dateFromPostForCurrentLanguage()

change around line 460

	return qtrans_strftime(qtrans_convertDateFormat($format), mysql2date('U',$post->post_date), $old_date, $before, $after);

into (remove $before, $after)

	return qtrans_strftime(qtrans_convertDateFormat($format), mysql2date('U',$post->post_date), $old_date);


## Credits

thanks to following add-ons for inspiration:

[StaticWP](https://github.com/slogsdon/staticwp)

[WordPress CMS Tree Page View](http://wordpress.org/plugins/cms-tree-page-view/)