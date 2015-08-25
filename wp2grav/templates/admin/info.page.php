<div class="wrap" id="static-wp">
    <h2>WP2Grav</h2>

    <p>
        Export your Wordpress Site into getGrav.org MD files.

        <br>
        supports multilanguage addons qTranslate and qTranslate Slug
        <br>
        <br>
        <?php

        global $q_config;
print_r($q_config);
        ?>
        <?php if (file_exists('/qtranslate/qtranslate.php')) {
            ?>
            <b>qTranslate addon detected</b>
        <?php
        } ?>

        Based on StaticWP
    </p>

    <p>
        <strong>Note:</strong> Not all Wordpress features, content
        types, and plugins are currently supported.

    <h3>Questions? Feedback?</h3>

    <p>
        Head over to <a href="https://github.com/slogsdon/wp2grav">Github</a>
        and submit an issue, or go the <a href="https://wordpress.org/plugins/wp2grav/">Wordpress
            Plugin Directory</a> to create a support thread.
    </p>
</div>