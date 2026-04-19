<?php
/**
 * Helper functions for Scriptless Social Sharing.
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015 Robin Cornett
 * @license           GPL-2.0+
 * @since 1.5.0
 */

/**
 * Helper function to get the buttons for output.
 * @param bool $heading
 *
 * @return string
 */
function scriptlesssocialsharing_do_buttons( $heading = true ) {
	return apply_filters( 'scriptlesssocialsharing_get_buttons', false, $heading );
}

/**
 * Helper function to get the plugin setting with defaults.
 *
 * @param string $key
 *
 * @return mixed
 */
function scriptlesssocialsharing_get_setting( $key = '' ) {
	return apply_filters( 'scriptlesssocialsharing_get_setting', $key );
}

/**
 * example function showing how easy it is to add buttons to any single entry, rather than
 * using the_content filter. This would add buttons at the beginning of any post/page.
 */
// add_action( 'genesis_entry_content', 'scriptlesssocialsharing_buttons_entry_content', 5 );
function scriptlesssocialsharing_buttons_entry_content() {
	echo wp_kses_post( scriptlesssocialsharing_do_buttons() );
}

/**
 * Check whether the current post type can show sharing buttons.
 * @return array
 * @since 1.5.0
 */
function scriptlesssocialsharing_post_types() {
	$setting    = scriptlesssocialsharing_get_setting( 'post_types' );
	$post_types = $setting;
	if ( isset( $setting['post'] ) ) {
		$post_types = array();
		foreach ( $setting as $post_type => $value ) {
			if ( is_array( $setting[ $post_type ] ) ) {
				if ( in_array( 1, $setting[ $post_type ], true ) ) {
					$post_types[] = $post_type;
				}
			} elseif ( is_string( $post_type ) && $value ) {
				$post_types[] = $post_type;
			}
		}
	}

	return apply_filters( 'scriptlesssocialsharing_post_types', $post_types );
}

/**
 * Instantiate the SVG class.
 * @return \ScriptlessSocialSharing\Output\SVG
 * @since 3.0.0
 */
function scriptlesssocialsharing_svg() {
	return \ScriptlessSocialSharing\Output\SVG::instance();
}

add_action( 'init', 'scriptlesssocialsharing_register' );
/**
 * Helper function to create a new sharing button in one go.
 *
 * @since 3.2
 * @return void
 */
function scriptlesssocialsharing_register() {
	$buttons = apply_filters( 'scriptlesssocialsharing_register', array() );
	if ( empty( $buttons ) || ! is_array( $buttons ) ) {
		return;
	}

	$defaults = array(
		'label'    => '',
		'url_base' => '',
		'args'     => array(),
	);
	foreach ( $buttons as $id => $button ) {
		$button = wp_parse_args( $button, $defaults );
		if ( empty( $id ) || empty( $button['label'] || empty( $button['url_base'] ) ) ) {
			continue;
		}
		new \ScriptlessSocialSharing\ButtonMaker( $id, $button['label'], $button['url_base'], $button['args'] );
	}
}

/**
 * Adds the sharing buttons to the post content.
 * Deprecated in version 2.0.0
 *
 * @param $content
 *
 * @return string
 */
function scriptlesssocialsharing_print_buttons( $content ) {
	_deprecated_function( __FUNCTION__, '2.0.0' );
	return $content;
}
