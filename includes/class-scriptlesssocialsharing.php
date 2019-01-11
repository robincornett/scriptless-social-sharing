<?php
/**
 * Scriptless Social Sharing
 *
 * @package           ScriptlessSocialSharing
 * @author            Robin Cornett
 * @link              https://github.com/robincornett/scriptless-social-sharing
 * @copyright         2015-2019 Robin Cornett
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
	 * @var $locations \ScriptlessSocialSharingOutputLocations
	 */
	protected $locations;

	/**
	 * @var $output \ScriptlessSocialSharingOutputButtons
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
	 * @var $shortcode \ScriptlessSocialSharingOutputShortcode
	 */
	protected $shortcode;

	/**
	 * ScriptlessSocialSharing constructor.
	 *
	 * @param $help
	 * @param $locations
	 * @param $output
	 * @param $post_meta
	 * @param $settings
	 * @param $shortcode
	 */
	public function __construct( $help, $locations, $output, $post_meta, $settings, $shortcode ) {
		$this->help      = $help;
		$this->locations = $locations;
		$this->output    = $output;
		$this->post_meta = $post_meta;
		$this->settings  = $settings;
		$this->shortcode = $shortcode;
	}

	/**
	 * Run all the things.
	 */
	public function run() {

		// Admin
		add_action( 'admin_menu', array( $this->settings, 'do_submenu_page' ) );
		add_action( 'load-settings_page_scriptlesssocialsharing', array( $this->help, 'help' ) );
		add_filter( 'plugin_action_links_' . SCRIPTLESSOCIALSHARING_BASENAME, array( $this, 'add_settings_link' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Post Meta
		add_action( 'add_meta_boxes', array( $this->post_meta, 'add_meta_box' ), 20 );
		add_action( 'save_post', array( $this->post_meta, 'save_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this->post_meta, 'enqueue' ) );

		// Output
		add_action( 'wp_enqueue_scripts', array( $this->output, 'load_styles' ) );
		add_action( 'wp_head', array( $this->locations, 'do_location' ) );
		add_shortcode( 'scriptless', array( $this->shortcode, 'shortcode' ) );

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
	 *
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
