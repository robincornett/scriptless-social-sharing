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
 * Version:           4.0.0
 * Requires at least: 6.9
 * Tested up to:      7.1
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

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

if ( ! defined( 'SCRIPTLESSOCIALSHARING_BASENAME' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_VERSION' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_VERSION', '4.0.0' );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_FILE' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_FILE', __FILE__ );
}

if ( ! defined( 'SCRIPTLESSOCIALSHARING_DIR' ) ) {
	define( 'SCRIPTLESSOCIALSHARING_DIR', __DIR__ );
}

spl_autoload_register( 'scriptlesssocialsharing_autoload' );

/**
 * Autoload the plugin classes from the src directory.
 *
 * @param string $class_name The fully qualified class name.
 * @return void
 */
function scriptlesssocialsharing_autoload( $class_name ) {
	$prefix = 'ScriptlessSocialSharing\\';
	if ( 0 !== strpos( $class_name, $prefix ) ) {
		return;
	}

	$file = SCRIPTLESSOCIALSHARING_DIR . '/src/' . str_replace( '\\', '/', substr( $class_name, strlen( $prefix ) ) ) . '.php';
	if ( is_readable( $file ) ) {
		require $file;
	}
}

/**
 * Include the plugin files.
 *
 * @return void
 */
function scriptlesssocialsharing_require() {
	$files = array(
		'helper-functions',
		'legacy-classes',
	);

	foreach ( $files as $file ) {
		require __DIR__ . '/includes/' . $file . '.php';
	}
}

scriptlesssocialsharing_require();

$scriptlesssocialsharing = new ScriptlessSocialSharing\Plugin(
	new ScriptlessSocialSharing\Output\Locations(),
	new ScriptlessSocialSharing\Output\Buttons(),
	new ScriptlessSocialSharing\Output\Pinterest(),
	new ScriptlessSocialSharing\PostMeta\Meta(),
	new ScriptlessSocialSharing\Settings\Settings(),
	new ScriptlessSocialSharing\Output\Shortcode()
);
$scriptlesssocialsharing->run();
