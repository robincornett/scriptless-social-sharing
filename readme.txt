=== Scriptless Social Sharing ===

Contributors: littler.chicken
Donate link: https://robincornett.com/donate/
Tags: social networks, social sharing, sharing buttons
Requires at least: 4.1
Tested up to: 4.6
Stable tag: 1.4.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

This plugin adds super simple social sharing buttons to the end of posts.

== Description ==

_Scriptless Social Sharing_ is a wee plugin to add buttons to your posts/pages, to make it easier for your readers to share your content on social networks.

The sharing links use the most basic methods provided by each network. There is no JavaScript, nothing fancy included in this plugin, so if you want fancy, this is not the plugin you're looking for. It just builds a set of links.

The sharing buttons are accessible--even if you choose the "Icons Only" button styles, the network names are still part of the buttons, just hidden in an accessible-ready manner.

There is a small settings page, so you can make decisions about which content types should have sharing buttons, what buttons should be added, and whether or not to use the plugin's styles. Beyond that, developers may like to make use of filters throughout the plugin.

Banner/icon image credit: [Ryan McGuire on Gratisography](http://www.gratisography.com/).

== Installation ==

1. Upload the entire `scriptless-social-sharing` folder to your `/wp-content/plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit the Settings > Scriptless Social Sharing page to change the default behavior of the plugin.

== Frequently Asked Questions ==

= What if I want to move where the buttons are output? =

As of version 1.3.0, you can choose whether to add sharing buttons to the end (default) or beginning of your content, or both--check the settings page.

The social sharing buttons are added to your post content using `the_content` filter, so they'll work with any theme. If you want to move them, you can remove the original filter, and add the buttons using your own action. Example:

	remove_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
	add_filter( 'the_content', 'prefix_scriptlesssocialsharing_buttons_before_entry' );
	function prefix_scriptlesssocialsharing_buttons_before_entry( $content ) {
		if ( ! function_exists( 'scriptlesssocialsharing_do_buttons' ) ) {
			return;
		}
		$buttons = scriptlesssocialsharing_do_buttons();
		// $buttons = scriptlesssocialsharing_do_buttons( false ); // optionally, output the buttons without the heading above.
		return $buttons . $content;
	}

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
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
		if ( ! $is_disabled && ! is_singular() ) {
			echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
		}
	}

= Some posts are missing the Pinterest button. Why is that? =

Yes, this is intentional. Pinterest really really _really_ wants your posts to have an image. The Pinterest button breaks if there isn't an image. The plugin looks in two places to find one: 1) the post featured image (ideal); and 2) if there is no featured image set, it picks the first image uploaded to that specific post. At this point, if there is still no image, rather than putting up a button which won't work, the plugin won't output a Pinterest button at all on that particular post.

= How can I change the order of the sharing buttons? =

You can customize the order of the sharing buttons with a filter. Here's an example which places Reddit as the first button, and email as the last:

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

You can set any order you like. `0` is the first number.

== Screenshots ==

1. Screenshot of the plugin settings in Settings > Scriptless Social Sharing.

== Upgrade Notice ==

1.4.0 new styling options for button output

== Changelog ==

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
