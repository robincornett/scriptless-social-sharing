# Scriptless Social Sharing

This plugin adds dead simple social sharing buttons to the end of posts. There are no settings, so any changes need to be done using filters.

## Installation

### Manual

1. Download the latest tagged archive (choose the "zip" option).
2. Unzip the archive.
3. Copy the folder to your `/wp-content/plugins/` directory.
4. Go to the Plugins screen and click __Activate__.

Check out the Codex for more information about [installing plugins manually](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

### Git

Using git, browse to your `/wp-content/plugins/` directory and clone this repository:

`git clone git@github.com:robincornett/scriptless-social-sharing.git`

Then go to your Plugins screen and click __Activate__.

## Frequently Asked Questions

### What if I want to change...

As of 1.0.0, Scriptless Social Sharing now includes a settings page, so although the original filters still exist, you can change a lot of things quite easily without touching any code. Go to Settings > Scriptless Social Sharing to set buttons, twitter handle, and stylesheets.

### What if I want to move where the buttons are output?

The social sharing buttons are added to the end of your post content using `the_content` filter, so they'll work with any theme. If you want to move them, you can remove the original filter, and add the buttons using your own action. Example:

```php
remove_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
add_action( 'genesis_entry_content', 'prefix_scriptlesssocialsharing_buttons_entry_content', 5 );
function prefix_scriptlesssocialsharing_buttons_entry_content() {
	echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
}
```

### I'd like to add sharing buttons to a different content type.

Scriptless Social Sharing adds buttons to posts only. You can change this easily with a simple filter:

```php
add_filter( 'scriptlesssocialsharing_post_types', 'prefix_add_download_buttons' );
function prefix_add_download_buttons( $post_types ) {
	$post_types[] = 'download';
	return $post_types;
}
```

This example adds buttons to the download post type (Easy Digital Downloads) as well as to posts.

## Changelog

### 1.0.0
* Added a settings page.

### 0.1.0
* Initial release on Github