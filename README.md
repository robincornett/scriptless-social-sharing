# Scriptless Social Sharing

This plugin adds super simple social sharing buttons to the end of posts.

_Scriptless Social Sharing_ is a wee plugin to add buttons to your posts/pages, to make it easier for your readers to share your content on social networks.

The sharing links use the most basic methods provided by each network. There is no JavaScript, nothing fancy included in this plugin, so if you want fancy, this is not the plugin you're looking for. It just builds a set of links.

The sharing buttons are accessible--even if you choose the "Icons Only" button styles, the network names are still part of the buttons, just hidden in an accessible-ready manner.

There is a small settings page, so you can make decisions about which content types should have sharing buttons, what buttons should be added, and whether or not to use the plugin's styles. Beyond that, developers may like to make use of filters throughout the plugin.

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
![Screenshot of the Scriptless Social Sharing Settings Page](https://github.com/robincornett/scriptless-social-sharing/blob/develop/assets/screenshot-1.png)  
_Screenshot of the Scriptless Social Sharing Settings Page._

![Screenshot of the Scriptless Social Sharing Buttons on a Post](https://github.com/robincornett/scriptless-social-sharing/blob/develop/assets/screenshot-2.png)  
_Screenshot of the Scriptless Social Sharing Buttons on a Post._

## Frequently Asked Questions

### What if I want to move where the buttons are output?

Version 2.0.0 changes everything here. The plugin now offers options for adding sharing buttons to each and every type of content on your site. Buttons can be added in multiple places, or easily add support so you can add buttons anywhere you like. The default button locations are:

* Before Content (legacy plugin location): at the beginning of the post/entry, within the post/entry content.
* After Content (legacy plugin location): at the end of the post/entry, within the post/entry content.
* Before Entry: at the beginning of the post/entry, outside of the post/entry content. Likely before the post title.
* After Entry: at the end of the post/entry, outside of the post/entry content.
* Manual: select this if you are adding buttons with your own code (this ensures that the necessary styles are loaded, and some other housekeeping).

**Note:** if you have code that removes the original buttons output and adds it back by hand, make sure that you select Manual for the location for each affected content type.

### What about a shortcode?

As of version 2.0.0, you can add sharing buttons directly to your content with a shortcode. You can tweak the output, too. For example, to add the buttons to your content, exactly as you have them set up in your settings, just use this shortcode:

```
[scriptless]
```

If you want to remove the heading, try it this way (or customize the heading by adding text):

```
[scriptless heading=""]
```

Want to only show certain buttons in the shortcode? Add them as a shortcode attribute (separate with commas, no spaces). This will show just the email and facebook buttons:

```[
scriptless buttons="email,facebook"]
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
	if ( ! function_exists( 'scriptlesssocialsharing_do_buttons' ) ) {
		return;
	}
	$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
	if ( ! $is_disabled && ! is_singular() ) {
		echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
	}
}
```

### Some posts are missing the Pinterest button. Why is that?

Yes, this is intentional. Pinterest really really _really_ wants your posts to have an image. The Pinterest button breaks if there isn't an image. The plugin looks in two places to find one: 1) the post featured image (ideal); and 2) if there is no featured image set, it picks the first image uploaded to that specific post. At this point, if there is still no image, rather than putting up a button which won't work, the plugin won't output a Pinterest button at all on that particular post.

### What is this "Custom Pinterest Image"?

As of version 1.5.0, you can add an image for the plugin to use specifically for Pinterest, instead of the post's featured image. This image will be added to the Pinterest sharing button as well as hidden in your content, so that the Pinterest bookmarklet will be able to "see" the image.

### How can I change the order of the sharing buttons?

You can customize the order of the sharing buttons with a filter. Here's an example which places Reddit as the first button, and email as the last:

```php
add_filter( 'scriptlesssocialsharing_networks', 'prefix_sort_networks_custom' );
function prefix_sort_networks_custom( $networks ) {
	$networks['email']['order']     = 7;
	$networks['facebook']['order']  = 1;
	$networks['google']['order']    = 2;
	$networks['linkedin']['order']  = 4;
	$networks['reddit']['order']    = 0;
	$networks['twitter']['order']   = 6;
	$networks['pinterest']['order'] = 5;

	uasort( $networks, 'prefix_set_scriptless_sort_order' );

	return $networks;
}

/**
 * Custom comparison function to sort the networks.
 * @param $a
 * @param $b
 *
 * @return bool
 */
function prefix_set_scriptless_sort_order( $a, $b ) {
	return $a['order'] > $b['order'];
}
```

You can set any order you like. `0` is the first number.

## Changelog

### 2.0.0
* added: new settings to manage buttons output by content type
* added: a shortcode!
* improved: URL construction methods now allow you to do things like add your own custom query args (props Sal Ferrarello)

### 1.5.2
* improved: custom Pinterest image alt text will be preferred over post title, if alt text is set
* fixed: URL encoding for strings with spaces

### 1.5.1
* updated: Font Awesome (4.7.0)

### 1.5.0
* added: ability to set a custom Pinterest image
* added: "related" parameter to Twitter URL (props Ben Meredith)
* improved: filter methods for adding new buttons
* fixed: disappearing post meta settings

### 1.4.0
* added: option for button padding
* added: option for table width (width of all buttons)
* bugfix: errant + in some mail programs (props Anders Carlen)

### 1.3.0
* added: option to only show icons on buttons, no text
* added: Reddit sharing button
* added: option to add sharing buttons to the beginning or end of content
* updated: code cleanup for settings and output
* bugfix: post type setting was not saved correctly--settings should be resaved

### 1.2.2
* updated: Font Awesome 4.6.3
* fixed: error when a post is embedded in another site (feature introduced in WP 4.4) due to other checks being bypassed

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
