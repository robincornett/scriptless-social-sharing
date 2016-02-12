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

### How do I change the twitter handle (@username)?

Use a filter:

```php
add_filter( 'scriptlesssocialsharing_twitter_handle', 'rgc_twitter_handle' );
function rgc_twitter_handle( $handle ) {
    return 'robincornett';
}
```

### What if I want to change the heading?

Good question. It's another filter:

```php
add_filter( 'scriptlesssocialsharing_heading', 'rgc_social_heading' );
function rgc_social_heading( $heading ) {
    return 'Be a friend: share this post.';
}
```

### What if I want to remove a button?

It's another filter:

```php
add_filter( 'scriptlesssocialsharing_default_buttons', 'rgc_test_remove' );
function rgc_test_remove( $buttons ) {
    unset( $buttons['pinterest'] );
    return $buttons;
}
```

### I don't want to use FontAwesome.

Filter:

```php
add_filter( 'scriptlesssocialsharing_fontawesome', '__return_false' );
```

## Changelog

### 1.0.0
* Added a settings page.

### 0.1.0
* Initial release on Github