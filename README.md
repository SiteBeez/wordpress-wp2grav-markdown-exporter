# WP2Grav - Export your Wordpress Site into getgrav.org content structure

Export your Wordpress posts, pages and more into a markdown content structure for [Grav - A Modern Flat-File CMS](getgrav.org)

## Description

Ready to move to [Grav - A Modern Flat-File CMS](getgrav.org)? 

This plugin allows you to export your posts, pages, authors, tags and categories into a markdown file based content structure for getgrav.org

The exporter creates a folder structure using the slugs of Wordpress - so you will keep your Urls!

Depending on your Wordpress installation (theme, add-ons) you will need to add some custom code loaded before the export process and review & edit the generated file set.

### Supported content types and meta data

Currently following content types are supported

- pages
- posts
- authors

Following meta data is supported:

- publish data
- published status
- authors
- categories
- tags 


To add more content types start with the `export()` function in `/wp-content/plugins/wp2grav/includes/wp2grav-admin.class.php` to see how to add more types.


### Images, Links

Urls of images and links are not touched by the export process. So all references will be kept like they are in Wordpress and converted into markdown.

**Images:** You might either move the wordpress `uploads` directory into the root of your grav installation or manually move all referenced media files into a location of your choice and use the search & replace function of your IDE change the references.

**Links**: You might need to use the search & replace function of your IDE to fix the link urls in the exported content files.

### Export location
The export will be stored in 

	/wp-content/uploads/wp2grav/export
	

### qTranslate support

The multi-language add-ons **qtranslate** and **qtranslate slug** are supported to export into the getgrav.org multi-language file format keeping language specific slugs.


### Note

Consider this plugin as "good enough to do the job", but not more ;)


## Installation

Upload the WP2Grav plugin to your site into

	wp-content/plugins/

and activate it through the Wordpress Administration.


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