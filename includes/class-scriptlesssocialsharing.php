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
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class ScriptlessSocialSharing
 * main plugin class
 */
class ScriptlessSocialSharing {

	protected $post_meta;

	/**
	 * @var $settings ScriptlessSocialSharingSettings
	 */
	protected $settings;

	/**
	 * @var string current plugin version
	 */
	protected $version = '1.2.2';

	/**
	 * @var $setting ScriptlessSocialSharingSettings->get_setting
	 */
	protected $setting;

	/**
	 * ScriptlessSocialSharing constructor.
	 *
	 * @param $settings
	 */
	public function __construct( $post_meta, $settings ) {
		$this->post_meta = $post_meta;
		$this->settings  = $settings;
	}

	/**
	 * Run all the things.
	 */
	public function run() {
		add_action( 'admin_menu', array( $this->settings, 'do_submenu_page' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'post_submitbox_misc_actions', array( $this->post_meta, 'do_checkbox' ) );
		add_action( 'save_post' , array( $this->post_meta, 'save_meta' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
		add_filter( 'scriptlesssocialsharing_get_setting', array( $this->settings, 'get_setting' ) );
		add_filter( 'scriptlesssocialsharing_get_buttons', array( $this, 'do_buttons' ), 10, 2 );
	}

	/**
	 * Set up text domain for translations
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'scriptless-social-sharing' );
	}

	/**
	 * Get the post types that can display the buttons.
	 * @return mixed
	 */
	public function get_post_types() {
		$post_types = array( 'post' );
		$setting = $this->settings->get_setting();
		if ( isset( $setting['post_types'] ) ) {
			$post_types = $setting['post_types'];
		}
		return apply_filters( 'scriptlesssocialsharing_post_types', $post_types );
	}

	/**
	 * Function to decide whether buttons can be output or not
	 * @param  boolean $cando default true
	 * @return boolean         false if not a singular post (can be modified for other content types)
	 */
	protected function can_do_buttons( $cando = true ) {
		if ( ! is_main_query() ) {
			$cando = false;
		}
		$post_types  = $this->get_post_types();
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
		if ( ! is_singular( $post_types ) || is_feed() || $is_disabled ) {
			$cando = false;
		}
		return apply_filters( 'scriptlesssocialsharing_can_do_buttons', $cando );
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
			wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), $this->version, 'screen' );
		}
		$fontawesome = apply_filters( 'scriptlesssocialsharing_use_fontawesome', true );
		if ( $fontawesome && $this->setting['styles']['font'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css', array(), '4.6.3' );
		}

		$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-fontawesome.css' );
		if ( $fa_file && $this->setting['styles']['font_css'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fa-icons', esc_url( $fa_file ), array(), $this->version, 'screen' );
		}
	}

	/**
	 * Return buttons
	 *
	 * @param string $output
	 * @param bool $heading set the bool to false to output buttons with no heading
	 *
	 * @return string|void
	 */
	public function do_buttons( $output, $heading = true ) {

		if ( ! $this->can_do_buttons() ) {
			return '';
		}

		$buttons = $this->make_buttons();

		if ( ! $buttons ) {
			return '';
		}

		$output = '<div class="scriptlesssocialsharing">';
		if ( $heading ) {
			$output .= $this->heading();
		}
		$output .= '<div class="scriptlesssocialsharing-buttons">';
		foreach ( $buttons as $button ) {
			$url      = 'email' === $button['name'] ? $button['url'] : $this->replace( $button['url'] );
			$data_pin = 'pinterest' === $button['name'] ? ' data-pin-no-hover="true" data-pin-custom="true" data-pin-do="skip"' : '';
			$output .= sprintf( '<a class="button %s" target="_blank" href="%s"%s><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), esc_url( $url ), $data_pin, $button['label'] );
		}
		$output .= '</div>';
		$output .= '</div>';

		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html' ), 10, 2 );
		return wp_kses_post( $output );
	}

	/**
	 * Create the default buttons
	 * @return array array of buttons/attributes
	 */
	protected function make_buttons() {

		$attributes    = $this->attributes();
		$yoast         = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		$twitter_title = $yoast ? $yoast : $attributes['title'];

		// Add URLs to the array of buttons
		$buttons                     = $this->settings->get_networks();
		$buttons['twitter']['url']   = sprintf( 'https://twitter.com/intent/tweet?text=%s&url=%s%s', $twitter_title, $attributes['permalink'], $attributes['twitter'] );
		$buttons['facebook']['url']  = sprintf( 'http://www.facebook.com/sharer/sharer.php?u=%s', $attributes['permalink'] );
		$buttons['google']['url']    = sprintf( 'https://plus.google.com/share?url=%s', $attributes['permalink'] );
		$buttons['pinterest']['url'] = sprintf( 'http://pinterest.com/pin/create/button/?url=%s&description=%s&media=%s', $attributes['permalink'], $attributes['title'], esc_url( $attributes['image'] ) );
		$buttons['linkedin']['url']  = sprintf( 'http://www.linkedin.com/shareArticle?mini=true&url=%s&title=%s%s&source=%s', $attributes['permalink'], $attributes['title'], strip_tags( $attributes['description'] ), $attributes['home'] );
		$buttons['email']['url']     = sprintf( 'mailto:?body=%s+%s&subject=%s+%s', $attributes['email_body'], $attributes['permalink'], $attributes['email_subject'], $attributes['title'] );
		$buttons['reddit']['url']    = sprintf( 'https://www.reddit.com/submit?url=%s', $attributes['permalink'] );

		$settings_buttons = $this->setting['buttons'];
		if ( $settings_buttons ) {
			foreach ( $settings_buttons as $settings_button => $value ) {
				if ( ! $value ) {
					unset( $buttons[$settings_button] );
				}
			}
		}
		if ( ! $attributes['image'] ) {
			unset( $buttons['pinterest'] );
		}

		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
	}

	/**
	 * create URL attributes for buttons
	 * @return array attributes
	 */
	protected function attributes() {
		$twitter     = $this->twitter_handle();
		$description = $this->description();
		$attributes  = array(
			'title'         => $this->title(),
			'permalink'     => get_the_permalink(),
			'twitter'       => $twitter ? sprintf( '&via=%s', $twitter ) : '',
			'home'          => home_url(),
			'image'         => $this->featured_image(),
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
		$featured_image = has_post_thumbnail() ? get_post_thumbnail_id() : $this->get_fallback_image();
		$image          = wp_get_attachment_image_src( $featured_image, 'large', false );
		$url            = isset( $image[0] ) ? $image[0] : '';
		return apply_filters( 'scriptlesssocialsharing_image_url', $url );
	}

	/**
	 * If there is no featured image, use the first image attached to the post/page as the fallback.
	 * @return bool
	 */
	protected function get_fallback_image() {
		$image_ids = array_keys(
			get_children(
				array(
					'post_parent'    => get_the_ID(),
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'numberposts'    => 1,
				)
			)
		);

		if ( isset( $image_ids[0] ) ) {
			return $image_ids[0];
		}

		return false;
	}

	/**
	 * get the post excerpt
	 *
	 * @param string $description
	 *
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
		if ( ! $heading ) {
			return '';
		}
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

	/**
	 * Allow pinterest data attributes on our links.
	 *
	 * @param $allowed array
	 * @param $context string
	 *
	 * @return mixed
	 */
	public function filter_allowed_html( $allowed, $context ) {

		if ( 'post' === $context ) {
			$allowed['a']['data-pin-custom']   = true;
			$allowed['a']['data-pin-no-hover'] = true;
			$allowed['a']['data-pin-do']       = true;
		}

		return $allowed;
	}
}
