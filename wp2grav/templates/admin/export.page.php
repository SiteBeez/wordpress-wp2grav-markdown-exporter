<div class="wrap" id="wp2grav-export">
  <h2>WP2Grav export</h2>
  <img src="../wp-content/plugins/wp2grav/grav.png" style="float: right">

  <form action="" method="post">
    <input type="hidden" name="wp2grav-action" value="export" />
    <?php wp_nonce_field('wp2grav'); ?>

    <p>Start exporting?</p>
    <input type="submit" value="Go for it!" class="button action" />
  </form>
</div>