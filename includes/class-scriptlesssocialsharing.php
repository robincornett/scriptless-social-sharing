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
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ScriptlessSocialSharing {

	/**
	 * @var $settings ScriptlessSocialSharingSettings
	 */
	protected $settings;

	/**
	 * @var $setting ScriptlessSocialSharingSettings->get_setting
	 */
	protected $setting;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function run() {
		add_action( 'admin_menu', array( $this->settings, 'do_submenu_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
		add_filter( 'scriptlesssocialsharing_get_buttons', array( $this, 'do_buttons' ) );
	}

	/**
	 * Enqueue CSS files
	 */
	public function load_styles() {
		if ( false === $this->can_do_buttons() ) {
			return;
		}

		$this->setting = $this->settings->get_setting();
		$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-style.css' );
		if ( $css_file && $this->setting['styles']['plugin'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), '0.1.1', 'screen' );
		}
		$fontawesome = apply_filters( 'scriptlesssocialsharing_use_fontawesome', true );
		if ( $fontawesome && $this->setting['styles']['font'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), '4.4.0' );
		}

		$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-fontawesome.css' );
		if ( $fa_file && $this->setting['styles']['font_css'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fa-icons', esc_url( $fa_file ), array(), '0.1.0', 'screen' );
		}
	}

	/**
	 * Return buttons
	 */
	public function do_buttons() {

		if ( ! $this->can_do_buttons() ) {
			return;
		}

		$buttons = $this->make_buttons();

		if ( ! $buttons ) {
			return;
		}

		$output  = '<div class="scriptlesssocialsharing">';
		$output .= $this->heading();
		$output .= '<div class="scriptlesssocialsharing-buttons">';
		foreach ( $buttons as $button ) {
			$data_pin = 'pinterest' === $button['name'] ? ' data-pin-no-hover="true" data-pin-do="skip"' : '';
			$output .= sprintf( '<a class="button %s" target="_blank" href="%s"%s><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), $this->replace( $button['url'] ), $data_pin, $button['label'] );
		}
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Function to decide whether buttons can be output or not
	 * @param  boolean $cando default true
	 * @return boolean         false if not a singular post (can be modified for other content types)
	 */
	protected function can_do_buttons( $cando = true ) {
		if ( ! is_main_query() ) {
			return false;
		}
		$post_types = apply_filters( 'scriptlesssocialsharing_post_types', array( 'post' ) );
		if ( ! is_singular( $post_types ) || is_feed() ) {
			$cando = false;
		}
		return apply_filters( 'scriptlesssocialsharing_can_do_buttons', $cando );
	}

	/**
	 * Create the default buttons
	 * @return array array of buttons/attributes
	 */
	protected function make_buttons() {

		$attributes    = $this->attributes();
		$yoast         = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		$twitter_title = $yoast ? $yoast : $attributes['title'];
		$buttons    = array(
			'twitter' => array(
				'url' => sprintf( 'https://twitter.com/intent/tweet?text=%s&url=%s%s', $twitter_title, $attributes['permalink'], $attributes['twitter'] ),
			),
			'facebook' => array(
				'url' => sprintf( 'http://www.facebook.com/sharer/sharer.php?u=%s', $attributes['permalink'] ),
			),
			'google' => array(
				'url' => sprintf( 'https://plus.google.com/share?url=%s', $attributes['permalink'] ),
			),
			'pinterest' => array(
				'url' => sprintf( 'http://pinterest.com/pin/create/button/?url=%s&description=%s%s', $attributes['permalink'], $attributes['title'], $attributes['image'] ),
			),
			'linkedin' => array(
				'url' => sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s%s&source=%s', $attributes['permalink'], $attributes['title'], strip_tags( $attributes['description'] ), $attributes['home'] ),
			),
			'email' => array(
				'url' => sprintf( 'mailto:?body=%s+%s&subject=%s+%s', $attributes['email_body'], $attributes['permalink'], $attributes['email_subject'], $attributes['title'] ),
			),
		);
		$buttons = array_merge_recursive( $this->settings->get_networks(), $buttons );

		$settings_buttons = $this->setting['buttons'];
		foreach ( $settings_buttons as $settings_button => $value ) {
			if ( ! $value ) {
				unset( $buttons[$settings_button] );
			}
		}

		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
	}

	/**
	 * create URL attributes for buttons
	 * @return array attributes
	 */
	protected function attributes() {
		$twitter     = $this->twitter_handle();
		$image_url   = $this->featured_image();
		$description = $this->description();
		$attributes  = array(
			'title'         => $this->title(),
			'permalink'     => get_the_permalink(),
			'twitter'       => $twitter ? sprintf( '&via=%s', $twitter ) : '',
			'home'          => home_url(),
			'image'         => $image_url ? sprintf( '&media=%s', $image_url ) : '',
			'description'   => $description ? sprintf( '&summary=%s', $description ) : '',
			'email_body'    => $this->email_body(),
			'email_subject' => $this->email_subject(),
		);
		return $attributes;
	}

	/**
	 * set the post title for sharing
	 * @return string uses Yoast title if it exists, post title otherwise
	 */
	protected function title() {
		return apply_filters( 'scriptlesssocialsharing_posttitle', the_title_attribute( 'echo=0' ) );
	}

	/**
	 * retrieve the featured image
	 * @return string if there is a featured image, return the URL
	 */
	protected function featured_image() {
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
	protected function description( $description = '' ) {
		if ( has_excerpt() ) {
			$description = get_the_excerpt();
		}
		return apply_filters( 'scriptlesssocialsharing_description', $description );
	}

	/**
	 * add twitter handle to URL
	 * @return string twitter handle (default is empty)
	 */
	protected function twitter_handle() {
		$handle = $this->setting['twitter_handle'];

		return apply_filters( 'scriptlesssocialsharing_twitter_handle', $handle );
	}

	/**
	 * Modify the heading above the buttons
	 * @return string heading
	 */
	protected function heading() {
		$heading = $this->setting['heading'];
		$heading = apply_filters( 'scriptlesssocialsharing_heading', $heading );
		return '<h3>' . $heading . '</h3>';
	}

	/**
	 * replace spaces in a string with + for URLs
	 * @param  string $string passed through from another source
	 * @return string         same string, just + instead of spaces
	 */
	protected function replace( $string ) {
		return str_replace( ' ', '+', $string );
	}

	/**
	 * subject line for the email button
	 * @return string can be modified via filter
	 */
	protected function email_subject() {
		$subject = $this->setting['email_subject'];

		return apply_filters( 'scriptlesssocialsharing_email_subject', $subject );
	}

	/**
	 * body text for the email button
	 * @return string can be modified via filter
	 */
	protected function email_body() {
		return apply_filters( 'scriptlesssocialsharing_email_body', __( 'I read this post and wanted to share it with you. Here\'s the link:', 'scriptless-social-sharing' ) );
	}
}
