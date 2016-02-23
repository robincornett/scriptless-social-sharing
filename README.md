# Scriptless Social Sharing

This plugin adds dead simple social sharing buttons to the end of posts.

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

## Screenshots
![Screenshot of the Scriptless Social Sharing Settings Page](https://github.com/robincornett/scriptless-social-sharing/blob/master/assets/screenshot-1.png)  
_Screenshot of the Scriptless Social Sharing Settings Page._

## Frequently Asked Questions

### What if I want to change...

As of 1.0.0, Scriptless Social Sharing now includes a settings page, so although the original filters still exist, you can change a lot of things quite easily without touching any code. Go to Settings > Scriptless Social Sharing to set buttons, twitter handle, and stylesheets.

### What if I want to move where the buttons are output?

The social sharing buttons are added to the end of your post content using `the_content` filter, so they'll work with any theme. If you want to move them, you can remove the original filter, and add the buttons using your own action. Example:

```php
remove_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
add_filter( 'the_content', 'prefix_scriptlesssocialsharing_buttons_before_entry' );
function prefix_scriptlesssocialsharing_buttons_before_entry( $content ) {
	$buttons = scriptlesssocialsharing_do_buttons();
    	return $buttons . $content;
}
```

### Can I add sharing buttons to posts on archive pages?

Yes. First, you have to tell the plugin that it can, in fact, run, even on the relevant archive page:

```
add_filter( 'scriptlesssocialsharing_can_do_buttons', 'prefix_add_buttons_archives' );
function prefix_add_buttons_archives( $cando = '' ) {
    if ( is_home() || is_tax() || is_category() ) {
        $cando = true;
    }
    return $cando;
}
```

Then you can add the buttons to the individual posts:

```
add_action( 'genesis_entry_content', 'prefix_scriptlesssocialsharing_buttons_entry_content', 25 );
function prefix_scriptlesssocialsharing_buttons_entry_content() {
    $cando = prefix_add_buttons_archives();
    if ( $cando ) {
        echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
    }
}
```

## Changelog

### 1.0.0
* Added a settings page.

### 0.1.0
* Initial release on Github