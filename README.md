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
	// $buttons = scriptlesssocialsharing_do_buttons( false ); // optionally, output the buttons without the heading above.
   	return $buttons . $content;
}
```

### Can I add sharing buttons to posts on archive pages?

Yes. First, you have to tell the plugin that it can, in fact, run, even on the relevant archive page:

```
add_filter( 'scriptlesssocialsharing_can_do_buttons', 'prefix_add_buttons_archives' );
function prefix_add_buttons_archives( $cando ) {
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
	$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
	if ( ! $is_disabled && ! is_singular() ) {
		echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
	}
}
```

### Some posts are missing the Pinterest button. Why is that?

Yes, this is intentional. Pinterest really really _really_ wants your posts to have an image. The Pinterest button breaks if there isn't an image. The plugin looks in two places to find one: 1) the post featured image (ideal); and 2) if there is no featured image set, it picks the first image uploaded to that specific post. At this point, if there is still no image, rather than putting up a button which won't work, the plugin won't output a Pinterest button at all on that particular post.

## Changelog

### 1.2.1
* fixed: pinterest button is now protected from an overzealous pinit script

### 1.2.0
* added: setting to disable buttons on an individual post basis
* fixed: use repository language packs

### 1.1.0
* added: filter to disable heading on output
* added: filter for the post fallback image (because pinterest)
* fixed: made CSS a bit more specific to avoid theme conflicts

### 1.0.2
* Fix CSS for buttons

### 1.0.1
* add a fallback image method
* bugfix: don't add Pinterest button if there is no image

### 1.0.0
* Added a settings page.

### 0.1.0
* Initial release on Github
