=== Scriptless Social Sharing ===

Contributors: littler.chicken
Donate link: https://robincornett.com/donate/
Tags: social networks, social sharing, sharing buttons
Requires at least: 6.2
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 3.3.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

This plugin adds super simple social sharing buttons to your content.

== Description ==

_Scriptless Social Sharing_ is a wee plugin to add buttons to your posts/pages, to make it easier for your readers to share your content on social networks.

The sharing links use the most basic methods provided by each network. There is no JavaScript, nothing fancy included in this plugin, so if you want fancy, this is not the plugin you're looking for. It just builds a set of links.

The sharing buttons are accessible--even if you choose the "Icons Only" button styles, the network names are still part of the buttons, just hidden in an accessible-ready manner.

There is a small settings page, so you can make decisions about which content types should have sharing buttons and where, what buttons should be added, and whether or not to use the plugin's styles. Beyond that, developers may like to make use of filters throughout the plugin.

Banner/icon image credit: [Ryan McGuire on Gratisography](https://gratisography.com/).

== Installation ==

1. Upload the entire `scriptless-social-sharing` folder to your `/wp-content/plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit the Settings > Scriptless Social Sharing page to change the default behavior of the plugin.

== Frequently Asked Questions ==

= How are the social network icons/buttons displayed? =

Scriptless uses SVG files to display the social network icons, or you can revert to using the old FontAwesome webfont.

Text only buttons are an option as well. And if you prefer flexbox for styling items in rows instead of table CSS, that's now available on the settings page.

= What social networks are supported? =

Scriptless Social Sharing currently supports the following social networks:

* X (Twitter)
* Facebook
* Pinterest
* LinkedIn
* Reddit
* WhatsApp
* Pocket
* Telegram
* Hatena Bookmark
* SMS
* Email
* Bluesky

Instagram does not support social sharing buttons.

= Can I change the SVG icons? =

Yes, using a filter, you can change which SVG icons are used. The plugin provides SVG alternatives for social networks if they are available.

Here's an example of how you could switch to using the "square" icons for each network (not all networks have one):

	add_filter( 'scriptlesssocialsharing_svg_icons', 'rgc_use_square_icons' );
	/**
	 * Change the Scriptless Social Sharing SVG icons to use the square versions when available.
	 *
	 * @param $icons
	 *
	 * @return array
	 */
	function rgc_use_square_icons( $icons ) {
		$square_icons = array(
			'email'     => 'envelope-square',
			'facebook'  => 'facebook-square',
			'pinterest' => 'pinterest-square',
			'reddit'    => 'reddit-square',
			'twitter'   => 'twitter-square',
			'whatsapp'  => 'whatsapp-square',
		);

		return array_merge( $icons, $square_icons );
	}

Want to use an icon not provided by the plugin? Load your own icons in your theme. As of version 3.2.0, the plugin uses SVG files directly, instead of sprite files. To use your own SVG files instead of the plugin's, add them to your theme, in `assets/svg`. The plugin will use the theme icons in preference of the plugin.

= What if I want to move where the buttons are output? =

Buttons can be added in multiple places, or easily add support so you can add buttons anywhere you like. The default button locations are:

* Before Content: at the beginning of the post/entry, within the post/entry content.
* After Content: at the end of the post/entry, within the post/entry content.
* Manual: select this if you are adding buttons with your own code (this ensures that the necessary styles are loaded, and some other housekeeping).

To take advantage of the new location options, you must visit the plugin settings page and update your settings.

**Note:** if you have code that removes the original buttons output and adds it back by hand, make sure that you select Manual for the location for each affected content type.

The best way to change the button output location is by using a filter. This example changes the locations from using `the_content` filter (with `hook` set to `false`) to using action hooks instead.

	add_filter( 'scriptlesssocialsharing_locations', 'prefix_change_sss_locations' );
	function prefix_change_sss_locations( $locations ) {
		$locations['before'] = array(
			'hook'     => 'genesis_before_entry',
			'filter'   => false,
			'priority' => 8,
		);
		$locations['after'] = array(
			'hook'     => 'loop_end',
			'filter'   => false,
			'priority' => 8,
		);

		return $locations;
	}

If you use the Genesis Framework, there is a setting to tell the plugin to use Genesis hooks instead.

= Is there a Scriptless block? =

Yes! Introduced in version 3.0, the new sharing block allows you to put sharing buttons anywhere in your content. Add just a few buttons, or rely on the default configuration defined on the settings page.

= What about a shortcode? =

As of version 2.0.0, you can add sharing buttons directly to your content with a shortcode. You can tweak the output, too. For example, to add the buttons to your content, exactly as you have them set up in your settings, just use this shortcode:

	[scriptless]

If you want to remove the heading, try it this way (or customize the heading by adding text):

	[scriptless heading=""]

Want to only show certain buttons in the shortcode? Add them as a shortcode attribute (separate with commas, no spaces). This will show just the email and facebook buttons:

	[scriptless buttons="email,facebook"]

= Can I add sharing buttons to posts on archive pages? =

Yes. First, you have to tell the plugin that it can, in fact, run, even on the relevant archive page:

	add_filter( 'scriptlesssocialsharing_can_do_buttons', 'prefix_add_buttons_archives' );
	function prefix_add_buttons_archives( $cando ) {
		if ( is_home() || is_tax() || is_category() ) {
			$cando = true;
		}
		return $cando;
	}

Then you can add the buttons to the individual posts (this example works only with the Genesis Framework):

	add_action( 'genesis_entry_content', 'prefix_scriptlesssocialsharing_buttons_entry_content', 25 );
	function prefix_scriptlesssocialsharing_buttons_entry_content() {
		if ( ! function_exists( 'scriptlesssocialsharing_do_buttons' ) ) {
			return;
		}
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true );
		if ( ! $is_disabled && ! is_singular() ) {
			echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
		}
	}

= Some posts are missing the Pinterest button. Why is that? =

Yes, this is intentional. Pinterest really really _really_ wants your posts to have an image. The Pinterest button breaks if there isn't an image. The plugin looks in three places to find one: 1) the custom Pinterest image; 2) the post featured image; and 3) if there is no featured image set, it picks the first image uploaded to that specific post. At this point, if there is still no image, rather than putting up a button which won't work, the plugin won't output a Pinterest button at all on that particular post.

= What is this "Custom Pinterest Image"? =

You can add an image for the plugin to use specifically for Pinterest, instead of the post's featured image. This image will be added to the Pinterest sharing button as well as hidden in your content, so that the Pinterest bookmarklet will be able to "see" the image. Scroll down in the post editor sidebar to find where to add the custom image.

= How can I add a custom sharing button? =

It has always been possible to add a custom sharing button with custom code, but version 3.2.0 makes this a little easier by creating a new helper function. You'll access the helper function by using a filter. Here's an example of how to add a button to share a post to Tumblr:

	add_filter( 'scriptlesssocialsharing_register', 'prefix_scriptless_add_tumblr_button' );
	/**
	* Adds a custom sharing button to Scriptless Social Sharing.
	*
	* @return void
	*/
	function prefix_scriptless_add_tumblr_button( $buttons ) {
		$buttons['tumblr'] = array(
			'label'    => __( 'Tumblr', 'scriptless-social-sharing' ),
			'url_base' => 'https://www.tumblr.com/share/link',
			'args'     => array(
				'query_args' => array(
					'name' => '%%title%%',
					'url'  => '%%permalink%%',
				),
				'color'      => '#35465c',
				'svg'        => 'tumblr-square', // Use this with the SVG icons and add the SVG file to your theme's `assets/svg` folder
				'icon'       => 'f173', // Use this when using the FontAwesome font for icons
			),
		);

		return $buttons;
	}

The `%%` are used to designate placeholders for the attribute variables that the plugin will apply when building the button.

Note that there is both an `svg` and an `icon` argument in the code sample. `svg` is preferred, but only applies if you are using the SVG option for the sharing icons. To add a new icon, upload it to your theme's `assets/svg` directory and the plugin will use it automatically. If you are using the older FontAwesome option, use `icon` to add the CSS unicode for the icon.

== Screenshots ==

1. Screenshot of the plugin settings in Settings > Scriptless Social Sharing.
2. Screenshot of the sharing buttons on a Post.

== Upgrade Notice ==

3.3.0: Bluesky support has been added.

== Changelog ==

= 3.3.0 =
* added: Bluesky sharing button
* updated: FontAwesome version
* fixed: textdomain handling
* fixed: block theme compatibility
* fixed: PHP compatibility, shortcode

= 3.2.4 =
* updated: SVG output has been updated for PHP 8 compatibility

= 3.2.3 =
* updated: Twitter is now X

= 3.2.2 =
* updated: script dependencies for the Scriptless block
* improved: Scriptless metabox can now be loaded regardless of location settings
* fixed: improved CSS class handling for the block
* dev: Scriptless now requires WordPress 5.2 or higher
* dev: additional filters have been added for the Pinterest button

= 3.2.1 =
* fixed: fatal error for sites without the mbstring extension installed
* fixed: new icons should not override the original SVG filter usage

= 3.2.0 =
* new: adding custom buttons is easier than ever with the new `scriptlesssocialsharing_register` filter (use described in FAQ)
* new/improved: SVG icons are now used directly, instead of from a sprite file
* added: Hatena Bookmark sharing button (props @kyontan)
* added: minimum PHP version is 5.6
* updated: FontAwesome 5.15.4
* fixed: block editor check for old versions of WordPress
* fixed: PHP constants for older versions of PHP
* note: this is the final version of Scriptless which will support WordPress versions earlier than 5.2

= 3.1.6 =
* added: filter for the Pinterest image size
* improved: screen reader text on sharing buttons (buttons now say "Share on ...")
* fixed: post meta sanitization was using a function deprecated in PHP 7.4

= 3.1.5 =
* updated: tested to WordPress 5.4
* fixed: the LinkedIn label

= 3.1.4 =
* added: filter on button container element
* updated: Twitter color
* fixed: button class instantiation when button names are translated
* fixed: styles not loading on shortcodes outside of content

= 3.1.3 =
* fixed: fatal error for fallback button class

= 3.1.2 =
* fixed: SMS link behavior
* fixed: custom color CSS for custom buttons when using flexbox
* fixed: block script enqueue
* fixed: custom buttons now have access to query args, base URL filters, which are preferable to filtering the final URL

= 3.1.1 =
* changed: HTML character decoding before URL encoding
* fixed: updated WhatsApp URL to use the API link instead of the shortened link due to issues on mobile

= 3.1.0 =
* added: links opening in new tabs are no noopener, noreferrer, and nofollow by default, and can be filtered
* added: filter on the link target
* added: custom class on the hidden Pinterest image
* added: option to prevent the Scriptless block from being registered
* improved: scriptlesssocialsharing_link_markup filter has access to all link attributes
* improved: link parameter decoding/encoding
* changed: source SVG is set to role="img"
* updated: Font Awesome 5.10.2
* fixed: SVG role and aria attributes
* fixed: singular post check which was always returning true

= 3.0.1 =
* fixed: compatibility issue with WordPress versions before 5.0

= 3.0.0 =
* added: SVG icons
* added: buttons for Telegram and SMS
* added: show buttons as icons only, icon + text, or text only
* added: select default CSS style: table (old) or flexbox (new, now default)
* added: a block!
* added: Finnish translation, props Hannu Jaatinen of Jargon Oy
* changed: icon only buttons use screen-reader-text class for label
* changed: shortcodes/blocks can now use any button, not just those selected in settings
* updated: Font Awesome is now 5.8.2 when using the webfont
* removed: Google+
* removed: media uploader no longer shows only images attached to the current post
* fixed: Pinterest buttons properly pass on hashtags

= 2.3.0 =
* added: button for sharing on WhatsApp (props @yig)
* added: button for Pocket (props @rryyaanndd)
* added: ability to easily update the button display order
* added: `scriptlesssocialsharing_heading_element` filter to change heading level for sharing buttons
* improved: custom Pinterest image defaults to show images uploaded to the current post

= 2.2.2 =
* changed: Google+ is now off for new users and will be removed in a future version
* changed: order of Pinterest button parameters to maybe reduce conflicts with lightbox plugins (props @pnwwebworks)
* fixed: overly aggressive sanitization of the custom Pinterest description which had issues with special characters (props @pnwwebworks)

= 2.2.1 =
* fixed: error on settings validation

= 2.2.0 =
* added: custom Pinterest description per post
* added: default email content setting
* changed: code reorganization
* fixed: email button should not open a new tab (props @salcode)
* fixed: initial Gutenberg compatibility
* fixed: end of content sharing buttons longer show after a shortcode if disabled

= 2.1.1 =
* changed: CSS autoprefixing; buttons are now hidden on print

= 2.1.0 =
* added: filter on the sharing link markup
* added: tabnapping fix on links
* fixed: button attributes on archives
* fixed: title encoding when special characters are present

= 2.0.1 =
* fixed: possible division by zero if Pinterest is the only button and there is no image
* fixed: special characters in post titles breaking Twitter share

= 2.0.0 =
* added: new settings to manage buttons output by content type
* added: a shortcode!
* added: link to the settings page from the Plugins page
* added: filter to manage button locations
* improved: URL construction methods now allow you to do things like add your own custom query args (props Sal Ferrarello)
* improved: if you've gone to the trouble of adding alt text to your featured images, thank you, and your Pinterest button will now use that (update from 1.5.2 applied to all featured images)

= 1.5.2 =
* improved: custom Pinterest image alt text will be preferred over post title, if alt text is set
* fixed: URL encoding for strings with spaces

= 1.5.1 =
* updated: Font Awesome (4.7.0)

= 1.5.0 =
* added: ability to set a custom Pinterest image
* added: "related" parameter to Twitter URL (props Ben Meredith)
* improved: filter methods for adding new buttons
* fixed: disappearing post meta settings

= 1.4.0 =
* added: option for button padding
* added: option for table width (width of all buttons)
* bugfix: errant + in some mail programs (props Anders Carlen)

= 1.3.0 =
* added: option to only show icons on buttons, no text
* added: Reddit sharing button
* added: option to add sharing buttons to the beginning or end of content
* updated: code cleanup for settings and output
* bugfix: post type setting was not saved correctly--settings should be resaved

= 1.2.2 =
* updated: Font Awesome 4.6.3
* fixed: error when a post is embedded in another site (feature introduced in WP 4.4) due to other checks being bypassed

= 1.2.1 =
* fixed: pinterest button is now protected from an overzealous pinit script

= 1.2.0 =
* added: setting to disable buttons on an individual post basis
* fixed: use repository language packs

= 1.1.0 =
* added: filter to disable heading on output
* added: filter for the post fallback image (because pinterest)
* fixed: made CSS a bit more specific to avoid theme conflicts

= 1.0.2 =
* Fix CSS for buttons

= 1.0.1 =
* add a fallback image method
* bugfix: don't add Pinterest button if there is no image

= 1.0.0 =
* Added a settings page
* Prep for release on the WordPress repository

= 0.1.0 =
* Initial release on Github
