# WP2Grav

Converts your blog into a static site.

## Description

Have performance issues? WP2Grav converts your blog into a static site, so you don't have to worry.

## Installation

Upload the WP2Grav plugin to your site, and activate it! Yep, that's it!

## Changelog

### 1.4.2

*In progress*

- Fix issue with preload.
- Fix issue with uninstall.

### 1.4.1

*Release Date - 9th March, 2015*

- Fix misuse of `wp_mkdir`.

### 1.4.0

*Release Date - 9th March, 2015*

- Make preloading safer.
- Ensure more than posts are compiled.
- Allow comments to be added
- Fix bug when files are recompiled.

### 1.3.0

*Release Date - 4th March, 2015*

- Refactor frontend and admin into separate classes.
- Abstracted HTML into templates and `WP2GravView`.
- Add admin menu pages.
- Allow users to preload site.

### 1.2.0

*Release Date - 4th March, 2015*

- Improve directory resolution.

### 1.1.1

*Release Date - 4th March, 2015*

- Fix bug with plugin name.

### 1.1.0

*Release Date - 3rd March, 2015*

- Add deactivation hook for cleanup.
- Add uninstall hook for cleanup.
- Move storage directory to uploads directory.

### 1.0.0

*Release Date - 3rd March, 2015*

- Initial release.
- Does basic static file generation.
- Sends file if it exists only for `GET` requests.

## License

WP2Grav is released under the MIT License.

See [LICENSE](https://github.com/slogsdon/static-wp/blob/master/LICENSE) for details.
