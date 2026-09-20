<?php
/**
 * Legacy class aliases for backward compatibility.
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2026 Robin Cornett
 * @license           GPL-2.0+
 * @since 4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

spl_autoload_register( 'scriptlesssocialsharing_autoload_legacy_class' );

/**
 * Alias a pre-4.0 class name to its namespaced replacement.
 *
 * Registered as an autoloader instead of aliasing the whole map up front:
 * class_alias() loads its target, so eager aliasing would pull every file in
 * src/ into every request.
 *
 * @param string $class_name The class PHP is trying to load.
 * @return void
 */
function scriptlesssocialsharing_autoload_legacy_class( $class_name ) {
	$classes = scriptlesssocialsharing_get_legacy_classes();
	if ( ! isset( $classes[ $class_name ] ) ) {
		return;
	}

	class_alias( $classes[ $class_name ], $class_name );
}

/**
 * Get the map of pre-4.0 class names to their namespaced replacements.
 *
 * @return array
 */
function scriptlesssocialsharing_get_legacy_classes() {
	return array(
		'ScriptlessSocialSharing'                 => 'ScriptlessSocialSharing\Plugin',
		'ScriptlessSocialSharingButtonMaker'      => 'ScriptlessSocialSharing\ButtonMaker',
		'ScriptlessSocialSharingEnqueue'          => 'ScriptlessSocialSharing\Enqueue',
		'ScriptlessSocialSharingButton'           => 'ScriptlessSocialSharing\Buttons\Button',
		'ScriptlessSocialSharingButtonBluesky'    => 'ScriptlessSocialSharing\Buttons\Bluesky',
		'ScriptlessSocialSharingButtonEmail'      => 'ScriptlessSocialSharing\Buttons\Email',
		'ScriptlessSocialSharingButtonFacebook'   => 'ScriptlessSocialSharing\Buttons\Facebook',
		'ScriptlessSocialSharingButtonFallback'   => 'ScriptlessSocialSharing\Buttons\Fallback',
		'ScriptlessSocialSharingButtonHatena'     => 'ScriptlessSocialSharing\Buttons\Hatena',
		'ScriptlessSocialSharingButtonLinkedin'   => 'ScriptlessSocialSharing\Buttons\Linkedin',
		'ScriptlessSocialSharingButtonPinterest'  => 'ScriptlessSocialSharing\Buttons\Pinterest',
		'ScriptlessSocialSharingButtonReddit'     => 'ScriptlessSocialSharing\Buttons\Reddit',
		'ScriptlessSocialSharingButtonSms'        => 'ScriptlessSocialSharing\Buttons\Sms',
		'ScriptlessSocialSharingButtonTelegram'   => 'ScriptlessSocialSharing\Buttons\Telegram',
		'ScriptlessSocialSharingButtonTwitter'    => 'ScriptlessSocialSharing\Buttons\Twitter',
		'ScriptlessSocialSharingButtonWhatsapp'   => 'ScriptlessSocialSharing\Buttons\Whatsapp',
		'ScriptlessSocialSharingOutput'           => 'ScriptlessSocialSharing\Output\Output',
		'ScriptlessSocialSharingOutputAttributes' => 'ScriptlessSocialSharing\Output\Attributes',
		'ScriptlessSocialSharingOutputBlock'      => 'ScriptlessSocialSharing\Output\Block',
		'ScriptlessSocialSharingOutputButtons'    => 'ScriptlessSocialSharing\Output\Buttons',
		'ScriptlessSocialSharingOutputLocations'  => 'ScriptlessSocialSharing\Output\Locations',
		'ScriptlessSocialSharingOutputPinterest'  => 'ScriptlessSocialSharing\Output\Pinterest',
		'ScriptlessSocialSharingOutputShortcode'  => 'ScriptlessSocialSharing\Output\Shortcode',
		'ScriptlessSocialSharingOutputSVG'        => 'ScriptlessSocialSharing\Output\SVG',
		'ScriptlessSocialSharingPostMeta'         => 'ScriptlessSocialSharing\PostMeta\Meta',
		'ScriptlessSocialSharingPostMetaFields'   => 'ScriptlessSocialSharing\PostMeta\Fields',
		'ScriptlessSocialSharingSettings'         => 'ScriptlessSocialSharing\Settings\Settings',
		'ScriptlessSocialSharingSettingsFields'   => 'ScriptlessSocialSharing\Settings\Fields',
		'ScriptlessSocialSharingSettingsHelp'     => 'ScriptlessSocialSharing\Settings\Help',
		'ScriptlessSocialSharingSettingsValidate' => 'ScriptlessSocialSharing\Settings\Validate',
	);
}
