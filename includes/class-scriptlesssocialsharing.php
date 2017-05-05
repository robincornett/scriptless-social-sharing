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

	/**
	 * @var $help ScriptlessSocialSharingHelp
	 */
	protected $help;

	/**
	 * @var $output ScriptlessSocialSharingOutput
	 */
	protected $output;

	/**
	 * @var $post_meta ScriptlessSocialSharingPostMeta
	 */
	protected $post_meta;

	/**
	 * @var $settings ScriptlessSocialSharingSettings
	 */
	protected $settings;

	/**
	 * ScriptlessSocialSharing constructor.
	 *
	 * @param $help
	 * @param $output
	 * @param $post_meta
	 * @param $settings
	 */
	public function __construct( $help, $output, $post_meta, $settings ) {
		$this->help      = $help;
		$this->output    = $output;
		$this->post_meta = $post_meta;
		$this->settings  = $settings;
	}

	/**
	 * Run all the things.
	 */
	public function run() {

		// Admin
		add_action( 'admin_menu', array( $this->settings, 'do_submenu_page' ) );
		// Hook into the display of the Plugins page to add a settings link.
		add_filter(
			$this->settings->get_plugin_action_link_filter_name(),
			array( $this->settings, 'append_settings_link' )
		);
		add_action( 'load-settings_page_scriptlesssocialsharing', array( $this->help, 'help' ) );
		add_filter( 'plugin_action_links_' . SCRIPTLESSOCIALSHARING_BASENAME, array( $this, 'add_settings_link' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Post Meta
		add_action( 'add_meta_boxes', array( $this->post_meta, 'add_meta_box' ), 20 );
		add_action( 'save_post' , array( $this->post_meta, 'save_meta' ) );

		// Output
		add_action( 'wp_enqueue_scripts', array( $this->output, 'load_styles' ) );
		add_action( 'wp_head', array( $this->output, 'do_location' ) );
		add_filter( 'the_content', array( $this->output, 'hide_pinterest_image' ), 99 );
		add_shortcode( 'scriptless', array( $this->output, 'shortcode' ) );

		// Filters
		add_filter( 'scriptlesssocialsharing_get_setting', array( $this->settings, 'get_setting' ) );
		add_filter( 'scriptlesssocialsharing_get_buttons', array( $this->output, 'do_buttons' ), 10, 2 );
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
	 * Add link to plugin settings page in plugin table
	 * @param $links array
	 *
	 * @return array
	 * @since 2.0.0
	 */
	public function add_settings_link( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=scriptlesssocialsharing' ) ), esc_attr__( 'Settings', 'scriptless-social-sharing' ) );
		return $links;
	}
}
