---
# http://learn.getgrav.org/content/headers
title: <?php echo $title;?><?php echo "\n"; // for some strange reason the newline after the closing tag disappears ?>
slug: <?php echo $slug;?><?php echo "\n"; ?>
# menu: <?php echo $title;?><?php echo "\n"; ?>
date: <?php echo mysql2date('d-m-Y', $post->post_date);?><?php echo "\n"; ?>
published: <?php echo (get_post_status( $post->ID ) == 'publish')  ? 'true' : 'false'; echo "\n"; ?>
publish_date: <?php echo mysql2date('d-m-Y', $post->post_date);?><?php echo "\n"; ?>
# unpublish_date: <?php echo mysql2date('d-m-Y', $post->post_date);?><?php echo "\n"; ?>
# template: false
# theme: false
visible: true
summary:
    enabled: true
    format: short
    size: 128
taxonomy:
    migration-status: review
    category: [<?php echo strip_tags(get_the_category_list(',', '', $post->ID)); ?>]
    tag: [<?php
if (get_the_tags($post->ID)) {
    foreach (get_the_tags($post->ID) as $tag)
      {
          $t[] =  $tag->name;
      }
      echo implode(',', $t);
    }
?>]
# added collection selector
<?php if ($post->pageFileName == 'blog') { ?>
content:
    items: @self.children
    order:
        by: date
        dir: desc
    limit: 5
    pagination: true   

<?php } ?>

author:
    name: <?php echo the_author_meta('user_nicename', $author_id); echo "\n"; ?>
metadata:
    author: <?php echo the_author_meta('user_nicename', $author_id); echo "\n"; ?>
#      description: Your page description goes here
#      keywords: HTML, CSS, XML, JavaScript
#      robots: noindex, nofollow
#      og:
#          title: The Rock
#          type: video.movie
#          url: http://www.imdb.com/title/tt0117500/
#          image: http://ia.media-imdb.com/images/rock.jpg
#  cache_enable: false
#  last_modified: true
---

<?php echo $content; ?>