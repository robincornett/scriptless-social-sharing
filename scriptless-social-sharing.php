<?php
/**
 * Scriptless Social Sharing
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015 Robin Cornett
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Scriptless Social Sharing
 * Plugin URI:        https://github.com/robincornett/scriptless-social-sharing
 * Description:       A scriptless plugin to add sharing buttons.
 * Version:           1.0.2
 * Author:            Robin Cornett
 * Author URI:        http://robincornett.com
 * Text Domain:       scriptless-social-sharing
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/robincornett/scriptless-social-sharing
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Include classes
function scriptlesssocialsharing_require() {
	$files = array(
		'class-scriptlesssocialsharing',
		'class-scriptlesssocialsharing-settings',
	);

	foreach ( $files as $file ) {
		require plugin_dir_path( __FILE__ ) . 'includes/' . $file . '.php';
	}
}
scriptlesssocialsharing_require();

// Instantiate main class
$scriptlesssocialsharingsettings = new ScriptlessSocialSharingSettings();
$scriptlesssocialsharing = new ScriptlessSocialSharing(
	$scriptlesssocialsharingsettings
);

// Run the plugin
$scriptlesssocialsharing->run();


function scriptlesssocialsharing_do_buttons() {
	return apply_filters( 'scriptlesssocialsharing_get_buttons', false );
}

add_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
function scriptlesssocialsharing_print_buttons( $content ) {
	$buttons = scriptlesssocialsharing_do_buttons();
	return $content . $buttons;
}

/**
 * example function showing how easy it is to add buttons to any single entry, rather than
 * using the_content filter. This would add buttons at the beginning of any post/page.
 */
// add_action( 'genesis_entry_content', 'scriptlesssocialsharing_buttons_entry_content', 5 );
function scriptlesssocialsharing_buttons_entry_content() {
	echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
}
