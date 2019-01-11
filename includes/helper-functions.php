<?php
/**
 * Helper functions for Scriptless Social Sharing.
 * 
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015-2019 Robin Cornett
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
 * @return mixed
 */
function scriptlesssocialsharing_get_setting() {
	return apply_filters( 'scriptlesssocialsharing_get_setting', false );
}

add_filter( 'the_content', 'scriptlesssocialsharing_print_buttons', 99 );
/**
 * Adds the sharing buttons to the post content.
 * Deprecated in version 2.0.0
 *
 * @param $content
 *
 * @return string
 */
function scriptlesssocialsharing_print_buttons( $content ) {
	if ( ! is_main_query() || ! get_queried_object_id() ) {
		return $content;
	}
	$setting = scriptlesssocialsharing_get_setting();
	if ( ! $setting['location'] ) {
		return $content;
	}
	$post_type = get_post_type();
	if ( ! isset( $setting['post_types'][ $post_type ] ) || ! $setting['post_types'][ $post_type ] || is_array( $setting['post_types'][ $post_type ] ) ) {
		return $content;
	}
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

/**
 * Check whether the current post type can show sharing buttons.
 * @return array
 * @since 1.5.0
 */
function scriptlesssocialsharing_post_types() {
	$setting    = scriptlesssocialsharing_get_setting();
	$post_types = $setting['post_types'];
	if ( isset( $setting['post_types']['post'] ) ) {
		$post_types = array();
		foreach ( $setting['post_types'] as $post_type => $value ) {
			if ( is_array( $setting['post_types'][ $post_type ] ) ) {
				if ( in_array( 1, $setting['post_types'][ $post_type ], true ) ) {
					$post_types[] = $post_type;
				}
			} elseif ( is_string( $post_type ) && $value ) {
				$post_types[] = $post_type;
			}
		}
	}
	return apply_filters( 'scriptlesssocialsharing_post_types', $post_types );
}
