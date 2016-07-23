<?php
/**
 * Scriptless Social Sharing
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015-2016 Robin Cornett
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Scriptless Social Sharing
 * Plugin URI:        https://github.com/robincornett/scriptless-social-sharing
 * Description:       A scriptless plugin to add sharing buttons.
 * Version:           1.2.2
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
		'class-scriptlesssocialsharing-postmeta',
		'class-scriptlesssocialsharing-settings',
	);

	foreach ( $files as $file ) {
		require plugin_dir_path( __FILE__ ) . 'includes/' . $file . '.php';
	}
}
scriptlesssocialsharing_require();

// Instantiate main class
$scriptlesssocialsharingpostmeta = new ScriptlessSocialSharingPostMeta();
$scriptlesssocialsharingsettings = new ScriptlessSocialSharingSettings();
$scriptlesssocialsharing = new ScriptlessSocialSharing(
	$scriptlesssocialsharingpostmeta,
	$scriptlesssocialsharingsettings
);

// Run the plugin
$scriptlesssocialsharing->run();

/**
 * Helper function to get the buttons for output.
 * @param bool $heading
 *
 * @return mixed|void
 */
function scriptlesssocialsharing_do_buttons( $heading = true ) {
	return apply_filters( 'scriptlesssocialsharing_get_buttons', false, $heading );
}

/**
 * Helper function to get the plugin setting with defaults.
 * @return mixed|void
 */
function scriptlesssocialsharing_get_setting() {
	return apply_filters( 'scriptlesssocialsharing_get_setting', false );
}

add_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
function scriptlesssocialsharing_print_buttons( $content ) {
	$setting = scriptlesssocialsharing_get_setting();
	$buttons = scriptlesssocialsharing_do_buttons();
	$before  = $setting['location']['before'] ? $buttons : '';
	$after   = $setting['location']['after'] ? $buttons : '';
	return $before . $content . $after;
}

/**
 * example function showing how easy it is to add buttons to any single entry, rather than
 * using the_content filter. This would add buttons at the beginning of any post/page.
 */
// add_action( 'genesis_entry_content', 'scriptlesssocialsharing_buttons_entry_content', 5 );
function scriptlesssocialsharing_buttons_entry_content() {
	echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
}
