<div class="wrap" id="wp2grav-export">
  <h2>WP2Grav export</h2>

  <h3>What's "export" mean?</h3>
  <p>
    convert your Wordpress Site into MarkDown Content files for Grav.
  </p>
  
  <form action="" method="post">
    <input type="hidden" name="wp2grav-action" value="export" />
    <?php wp_nonce_field('wp2grav'); ?>

    <p>Start exporting?</p>
    <input type="submit" value="Go for it!" class="button action" />
  </form>
</div>