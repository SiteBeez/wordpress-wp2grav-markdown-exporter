---
name: <?php echo the_author_meta('display_name', $author_id); echo "\n"; ?>
email: <?php echo the_author_meta('user_email', $author_id); echo "\n"; ?>
website: <?php echo the_author_meta('user_url', $author_id); echo "\n"; ?>
routable: false
taxonomy:
    migration-status: review
    author: <?php echo the_author_meta('user_nicename', $author_id); echo "\n"; ?>
# template: false
    
---

<?php echo the_author_meta('display_name', $author_id); ?> loves writing blog posts on this site