---
# http://learn.getgrav.org/content/headers
title: <?php echo $title;?><?php echo "\n"; // for some strange reason the newline after the closing tag disappears ?>
slug: <?php echo $slug;?><?php echo "\n"; ?>
# menu: <?php $title;?><?php echo "\n"; ?>
# date: <?php echo get_the_date('', $post);?><?php echo "\n"; ?>
published: <?php echo (get_post_status( $post->ID ) == 'published')  ? true : false; echo "\n"; ?>
# publish_date: <?php //echo get_the_date('', $post);?><?php  echo "\n"; ?>
# unpublish_date: 05/17/2015 00:32
# template: false
visible: true
summary:
    enabled: true
    format: short
    size: 128
taxonomy:
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
author: <?php echo the_author_meta('user_nicename', $author_id); echo "\n"; ?>
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