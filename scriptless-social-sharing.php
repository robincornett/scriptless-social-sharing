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
 * Version:           2.1.1
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

if ( ! defined( 'SCRIPTLESSOCIALSHARING_BASENAME' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_BASENAME', plugin_basename( __FILE__ ) );
}

// Include classes
function scriptlesssocialsharing_require() {
	$files = array(
		'class-scriptlesssocialsharing',
		'class-scriptlesssocialsharing-enqueue',
		'class-scriptlesssocialsharing-help',
		'class-scriptlesssocialsharing-output',
		'class-scriptlesssocialsharing-postmeta',
		'class-scriptlesssocialsharing-settings',
		'helper-functions',
	);

	foreach ( $files as $file ) {
		require plugin_dir_path( __FILE__ ) . 'includes/' . $file . '.php';
	}
}
scriptlesssocialsharing_require();

// Instantiate main class
$scriptlesssocialsharinghelp     = new ScriptlessSocialSharingHelp();
$scriptlesssocialsharingoutput   = new ScriptlessSocialSharingOutput();
$scriptlesssocialsharingpostmeta = new ScriptlessSocialSharingPostMeta();
$scriptlesssocialsharingsettings = new ScriptlessSocialSharingSettings();
$scriptlesssocialsharing         = new ScriptlessSocialSharing(
	$scriptlesssocialsharinghelp,
	$scriptlesssocialsharingoutput,
	$scriptlesssocialsharingpostmeta,
	$scriptlesssocialsharingsettings
);

// Run the plugin
$scriptlesssocialsharing->run();
