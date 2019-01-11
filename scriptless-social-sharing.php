<?php
/**
 * Scriptless Social Sharing
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett <hello@robincornett.com>
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015-2019 Robin Cornett
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Scriptless Social Sharing
 * Plugin URI:        https://github.com/robincornett/scriptless-social-sharing
 * Description:       A scriptless plugin to add sharing buttons.
 * Version:           2.3.0
 * Author:            Robin Cornett
 * Author URI:        https://robincornett.com
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

if ( ! defined( 'SCRIPTLESSOCIALSHARING_BASENAME' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_VERSION' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_VERSION', '2.3.0' );
}

// Include classes
function scriptlesssocialsharing_require() {
	$files = array(
		'class-scriptlesssocialsharing',
		'class-scriptlesssocialsharing-enqueue',
		'output/class-scriptlesssocialsharing-output',
		'output/class-scriptlesssocialsharing-output-buttons',
		'output/class-scriptlesssocialsharing-output-locations',
		'output/class-scriptlesssocialsharing-output-shortcode',
		'postmeta/class-scriptlesssocialsharing-postmeta',
		'settings/class-scriptlesssocialsharing-help',
		'settings/class-scriptlesssocialsharing-settings',
		'helper-functions',
	);

	foreach ( $files as $file ) {
		require plugin_dir_path( __FILE__ ) . 'includes/' . $file . '.php';
	}
}

scriptlesssocialsharing_require();

// Instantiate main class
$scriptlesssocialsharing_help      = new ScriptlessSocialSharingHelp();
$scriptlesssocialsharing_locations = new ScriptlessSocialSharingOutputLocations();
$scriptlesssocialsharing_output    = new ScriptlessSocialSharingOutputButtons();
$scriptlesssocialsharing_postmeta  = new ScriptlessSocialSharingPostMeta();
$scriptlesssocialsharing_settings  = new ScriptlessSocialSharingSettings();
$scriptlesssocialsharing_shortcode = new ScriptlessSocialSharingOutputShortcode();
$scriptlesssocialsharing           = new ScriptlessSocialSharing(
	$scriptlesssocialsharing_help,
	$scriptlesssocialsharing_locations,
	$scriptlesssocialsharing_output,
	$scriptlesssocialsharing_postmeta,
	$scriptlesssocialsharing_settings,
	$scriptlesssocialsharing_shortcode
);

// Run the plugin
$scriptlesssocialsharing->run();
