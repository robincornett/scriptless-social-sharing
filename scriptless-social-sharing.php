<?php
/**
 * Scriptless Social Sharing
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett <hello@robincornett.com>
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015-2021 Robin Cornett
 * @license           GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name:       Scriptless Social Sharing
 * Plugin URI:        https://github.com/robincornett/scriptless-social-sharing
 * Description:       A scriptless plugin to add sharing buttons.
 * Version:           3.3.1
 * Requires at least: 6.2
 * Tested up to:      6.8
 * Requires PHP:      7.4
 * Author:            Robin Cornett
 * Author URI:        https://robincornett.com
 * Text Domain:       scriptless-social-sharing
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/robincornett/scriptless-social-sharing
 * GitHub Branch:     master
 */

namespace ScriptlessSocialSharing;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SCRIPTLESSOCIALSHARING_BASENAME' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_VERSION' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_VERSION', '3.3.1' );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_FILE' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_FILE', __FILE__ );
}

// Include classes
function scriptlesssocialsharing_require() {
	$files = array(
		'helper-functions',
	);

	foreach ( $files as $file ) {
		require __DIR__ . '/src/' . $file . '.php';
	}
}

scriptlesssocialsharing_require();

// Instantiate main class
$scriptlesssocialsharing = new Plugin(
	new Output\Locations(),
	new Output\Buttons(),
	new Output\Pinterest(),
	new PostMeta\Meta(),
	new Settings\Settings(),
	new Output\Shortcode()
);

// Run the plugin
$scriptlesssocialsharing->run();
