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
 * Version:           0.1.0
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

add_action( 'wp_enqueue_scripts', 'scriptlesssocialsharing_style' );
function scriptlesssocialsharing_style() {
	$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'scriptlesssocialsharing-style.css' );
	wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), '0.1.0', 'screen' );

	$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'scriptlesssocialsharing-fontawesome.css' );
	if ( ! $fa_file ) {
		return;
	}
	wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', esc_url( $fa_file ), array(), '0.1.0', 'screen' );
}

add_filter( 'the_content', 'scriptlesssocialsharing_do_buttons', 50 );
function scriptlesssocialsharing_do_buttons( $content ) {

	$buttons = scriptlesssocialsharing_make_buttons();
	if ( ! is_singular( 'post' ) && ! $buttons ) {
		return $content;
	}

	$output  = scriptlesssocialsharing_heading();
	$output .= '<div class="scriptlesssocialsharing-buttons">';
	foreach ( $buttons as $button ) {
		$output .= sprintf( '<a class="button %s" target="_blank" href="%s"><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), esc_url( $button['url'] ), esc_attr( $button['title'] ) );
	}
	$output .= '</div>';

	return $content . $output;
}

function scriptlesssocialsharing_make_buttons() {

	$title          = the_title_attribute( 'echo=0' );
	$title          = str_replace( ' ', '+', $title );
	$permalink      = get_the_permalink();
	$twitter        = apply_filters( 'scriptlesssocialsharing_twitter_handle', 'twitter' );
	$home           = home_url();
	$featured_image = get_post_thumbnail_id();
	$image_source   = wp_get_attachment_image_src( $featured_image, 'large', true );
	$image_url      = isset( $image_source ) ? $image_source[0] : '';
	$image          = $image_url ? sprintf( '&media=%s', $image_url ) : '';
	$description    = get_the_excerpt();
	$description    = str_replace( ' ', '+', $description );

	$buttons = array(
		'twitter' => array(
			'name'  => 'twitter',
			'title' => 'Twitter',
			'url'   => sprintf( 'https://twitter.com/intent/tweet?text=%s&url=%s&via=%s', $title, $permalink, $twitter ),
		),
		'facebook' => array(
			'name'  => 'facebook',
			'title' => 'Facebook',
			'url'   => sprintf( 'http://www.facebook.com/sharer/sharer.php?u=%s', $permalink ),
		),
		'google' => array(
			'name'  => 'google',
			'title' => 'Google+',
			'url'   => sprintf( 'https://plus.google.com/share?url=%s', $permalink ),
		),
		'pinterest' => array(
			'name'  => 'pinterest',
			'title' => 'Pinterest',
			'url'   => sprintf( 'http://pinterest.com/pin/create/button/?url=%s&description=%s%s', $permalink, $title, $image ),
		),
		'linkedin' => array(
			'name'  => 'linkedin',
			'title' => 'Linkedin',
			'url'   => sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s&summary=%s&source=%s', $permalink, $title, strip_tags( $description ), $home ),
		),
	);

	return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons );
}

function scriptlesssocialsharing_heading() {
	$heading = apply_filters( 'scriptlesssocialsharing_heading', __( 'Share this post:', 'scriptless-social-sharing' ) );
	return '<h3>' . $heading . '</h3>';
}
