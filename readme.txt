=== Scriptless Social Sharing ===

Contributors: littler.chicken
Donate link: https://robincornett.com/donate/
Tags: social networks, social sharing, sharing buttons
Requires at least: 3.1
Tested up to: 4.4
Stable tag: 1.0.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

This plugin adds dead simple social sharing buttons to the end of posts.

== Description ==

_Scriptless Social Sharing_ is a wee plugin to add buttons to your posts/pages, to make it easier for your readers to share your content on social networks.

This plugin adds sharing links using the most basic methods provided by each network. There is no javascript, nothing fancy included in this plugin. It just builds a set of links.

There is a small settings page, so you can make decisions about which content types should have sharing buttons, what buttons should be added, and whether or not to use the plugin's styles. Beyond that, developers may like to make use of filters throughout the plugin.

== Installation ==

1. Upload the entire `scriptless-social-sharing` folder to your `/wp-content/plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Visit the Settings > Scriptless Social Sharing page to change the default behavior of the plugin.

== Frequently Asked Questions ==

= What if I want to move where the buttons are output? =

The social sharing buttons are added to the end of your post content using `the_content` filter, so they'll work with any theme. If you want to move them, you can remove the original filter, and add the buttons using your own action. Example:

	remove_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
	add_filter( 'the_content', 'prefix_scriptlesssocialsharing_buttons_before_entry' );
	function prefix_scriptlesssocialsharing_buttons_before_entry( $content ) {
		$buttons = scriptlesssocialsharing_do_buttons();
			return $buttons . $content;
	}

= Can I add sharing buttons to posts on archive pages? =

Yes. First, you have to tell the plugin that it can, in fact, run, even on the relevant archive page:

	add_filter( 'scriptlesssocialsharing_can_do_buttons', 'prefix_add_buttons_archives' );
	function prefix_add_buttons_archives( $cando = '' ) {
		if ( is_home() || is_tax() || is_category() ) {
			$cando = true;
		}
		return $cando;
	}

Then you can add the buttons to the individual posts (this example works only with the Genesis Framework):

	add_action( 'genesis_entry_content', 'prefix_scriptlesssocialsharing_buttons_entry_content', 25 );
	function prefix_scriptlesssocialsharing_buttons_entry_content() {
		$cando = prefix_add_buttons_archives();
		if ( $cando ) {
			echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
		}
	}

== Screenshots ==

1. Screenshot of the plugin settings in Settings > Scriptless Social Sharing.

== Upgrade Notice ==

= 1.0.0 =
initial release

== Changelog ==

= 1.0.0 =
* Added a settings page
* Prep for release on the WordPress repository

= 0.1.0 =
* Initial release on Github
