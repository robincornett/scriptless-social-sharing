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

/**
 * Enqueue CSS files
 */
add_action( 'wp_enqueue_scripts', 'scriptlesssocialsharing_style' );
function scriptlesssocialsharing_style() {
	$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'scriptlesssocialsharing-style.css' );
	if ( $css_file ) {
		wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), '0.1.0', 'screen' );
	}

	$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'scriptlesssocialsharing-fontawesome.css' );
	if ( $fa_file ) {
		wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', esc_url( $fa_file ), array(), '0.1.0', 'screen' );
	}
}

/**
 * Add buttons to the_content filter
 */
add_filter( 'the_content', 'scriptlesssocialsharing_do_buttons', 50 );
function scriptlesssocialsharing_do_buttons( $content ) {

	if ( ! is_singular( 'post' ) ) {
		return $content;
	}

	$buttons = scriptlesssocialsharing_make_buttons();
	if ( ! $buttons ) {
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

/**
 * Create the default buttons
 * @return array array of buttons/attributes
 */
function scriptlesssocialsharing_make_buttons() {

	$attributes = scriptlesssocialsharing_attributes();
	$buttons    = array(
		'twitter' => array(
			'name'  => 'twitter',
			'title' => 'Twitter',
			'url'   => sprintf( 'https://twitter.com/intent/tweet?text=%s&url=%s%s', $attributes['title'], $attributes['permalink'], $attributes['twitter'] ),
		),
		'facebook' => array(
			'name'  => 'facebook',
			'title' => 'Facebook',
			'url'   => sprintf( 'http://www.facebook.com/sharer/sharer.php?u=%s', $attributes['permalink'] ),
		),
		'google' => array(
			'name'  => 'google',
			'title' => 'Google+',
			'url'   => sprintf( 'https://plus.google.com/share?url=%s', $attributes['permalink'] ),
		),
		'pinterest' => array(
			'name'  => 'pinterest',
			'title' => 'Pinterest',
			'url'   => sprintf( 'http://pinterest.com/pin/create/button/?url=%s&description=%s%s', $attributes['permalink'], $attributes['title'], $attributes['image'] ),
		),
		'linkedin' => array(
			'name'  => 'linkedin',
			'title' => 'Linkedin',
			'url'   => sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s%s&source=%s', $attributes['permalink'], $attributes['title'], strip_tags( $attributes['description'] ), $attributes['home'] ),
		),
		'email' => array(
			'name'  => 'email',
			'title' => 'Email',
			'url'   => sprintf( 'mailto:?body=%s+%s&subject=%s+%s', scriptlesssocialsharing_email_body() , $attributes['permalink'], scriptlesssocialsharing_email_subject(), $attributes['title'] ),
		),
	);

	return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
}

/**
 * create URL attributes for buttons
 * @return array attributes
 */
function scriptlesssocialsharing_attributes() {
	$title       = the_title_attribute( 'echo=0' );
	$twitter     = scriptlesssocialsharing_twitter_handle();
	$image_url   = scriptlesssocialsharing_featuredimage();
	$description = scriptlesssocialsharing_description();
	$attributes  = array(
		'title'       => scriptlesssocialsharing_replace( $title ),
		'permalink'   => get_the_permalink(),
		'twitter'     => $twitter ? sprintf( '&via=%s', $twitter ) : '',
		'home'        => home_url(),
		'image'       => $image_url ? sprintf( '&media=%s', $image_url ) : '',
		'description' => $description ? sprintf( '&summary=%s', $description ) : '',
	);
	return $attributes;
}

/**
 * retrieve the featured image
 * @return string if there is a featured image, return the URL
 */
function scriptlesssocialsharing_featuredimage() {
	if ( ! has_post_thumbnail() ) {
		return;
	}
	$featured_image = get_post_thumbnail_id();
	$image_source   = wp_get_attachment_image_src( $featured_image, 'large', true );
	$image_url      = $image_source[0];
	return $image_url;
}

/**
 * get the post excerpt
 * @return string excerpt formatted for URL
 */
function scriptlesssocialsharing_description() {
	if ( ! has_excerpt() ) {
		return;
	}
	$description = get_the_excerpt();
	$description = scriptlesssocialsharing_replace( $description );
	return $description;
}

/**
 * add twitter handle to URL
 * @return string twitter handle (default is empty)
 */
function scriptlesssocialsharing_twitter_handle() {
	return apply_filters( 'scriptlesssocialsharing_twitter_handle', '' );
}

/**
 * Modify the heading above the buttons
 * @return string heading
 */
function scriptlesssocialsharing_heading() {
	$heading = apply_filters( 'scriptlesssocialsharing_heading', __( 'Share this post:', 'scriptless-social-sharing' ) );
	return '<h3>' . $heading . '</h3>';
}

/**
 * replace spaces in a string with + for URLs
 * @param  string $string passed through from another source
 * @return string         same string, just + instead of spaces
 */
function scriptlesssocialsharing_replace( $string ) {
	return str_replace( ' ', '+', $string );
}

/**
 * subject line for the email button
 * @return string can be modified via filter
 */
function scriptlesssocialsharing_email_subject() {
	$subject = apply_filters( 'scriptlesssocialsharing_email_subject', __( 'A post worth sharing:', 'scriptless-social-sharing' ) );
	return scriptlesssocialsharing_replace( $subject );
}

/**
 * body text for the email button
 * @return string can be modified via filter
 */
function scriptlesssocialsharing_email_body() {
	$body = apply_filters( 'scriptlesssocialsharing_email_body', __( 'I read this post and wanted to share it with you. Here\'s the link:', 'scriptless-social-sharing' ) );
	return scriptlesssocialsharing_replace( $body );
}
