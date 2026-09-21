<?php
/**
 * Class for adding a new settings page to the WordPress admin, under Settings.
 *
 * @package ScriptlessSocialSharing
 */

namespace ScriptlessSocialSharing\Settings;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Settings
 */
class Settings {

	/**
	 * Option registered by plugin.
	 * @var array
	 */
	protected $setting;

	/**
	 * Slug for settings page.
	 * @var string
	 */
	protected $page = 'scriptlesssocialsharing';

	/**
	 * add a submenu page under settings
	 * @since  1.4.0
	 */
	public function do_submenu_page() {

		add_options_page(
			__( 'Scriptless Social Sharing Settings', 'scriptless-social-sharing' ),
			__( 'Scriptless Social Sharing', 'scriptless-social-sharing' ),
			'manage_options',
			$this->page,
			array( $this, 'do_settings_form' )
		);

		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( "load-settings_page_{$this->page}", array( $this, 'build_settings_page' ) );
		add_action( "load-settings_page_{$this->page}", array( $this, 'help' ) );
	}

	/**
	 * Build the settings page.
	 * @since 2.0.0
	 */
	public function build_settings_page() {
		$sections = $this->register_sections();
		$this->add_sections( $sections );
		$this->add_fields( $this->register_fields(), $sections );
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Initialize the help tabs.
	 * @since 3.0.0
	 */
	public function help() {
		$help = new Help();
		$help->help();
	}

	/**
	 * Output the plugin settings form.
	 *
	 * @since 1.0.0
	 */
	public function do_settings_form() {

		echo '<div class="wrap">';
		echo '<h1>' . esc_attr( get_admin_page_title() ) . '</h1>';
		echo '<form action="options.php" method="post">';
		settings_fields( $this->page );
		do_settings_sections( $this->page );
		wp_nonce_field( "{$this->page}_save-settings", "{$this->page}_nonce", false );
		submit_button();
		echo '</form>';
		echo '</div>';
	}

	/**
	 * Enqueue the script needed to make the buttons sortable.
	 * @since 2.3.0
	 */
	public function enqueue() {
		$asset = require SCRIPTLESSOCIALSHARING_DIR . '/assets/build/js/scriptless-sortable.asset.php';
		wp_enqueue_script(
			'scriptless-sortable',
			plugins_url( 'assets/build/js/scriptless-sortable.js', SCRIPTLESSOCIALSHARING_FILE ),
			$asset['dependencies'],
			SCRIPTLESSOCIALSHARING_VERSION,
			true
		);
		wp_localize_script(
			'scriptless-sortable',
			'scriptlessSortableL10n',
			array(
				/* translators: 1: Social network name, 2: The button's new position, 3: The total number of buttons */
				'moved' => __( '%1$s moved to position %2$d of %3$d.', 'scriptless-social-sharing' ),
			)
		);
		wp_enqueue_style(
			'scriptless-sortable',
			plugins_url( 'assets/build/css/scriptlesssocialsharing-admin.css', SCRIPTLESSOCIALSHARING_FILE ),
			array(),
			SCRIPTLESSOCIALSHARING_VERSION,
			'all'
		);
		wp_style_add_data( 'scriptless-sortable', 'rtl', 'replace' );
	}

	/**
	 * Add new fields to wp-admin/options-general.php?page=scriptlesssocialsharing
	 *
	 * @since 2.2.0
	 */
	public function register_settings() {
		register_setting( $this->page, $this->page, array( $this, 'do_validation_things' ) );
	}

	/**
	 * Returns the plugin setting, merged with defaults.
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	public function get_setting( $key = '' ) {
		if ( isset( $this->setting ) ) {
			return $key ? $this->setting[ $key ] : $this->setting;
		}
		$db_setting    = $this->get_database_setting();
		$defaults      = $this->defaults();
		$this->setting = wp_parse_args( $db_setting, $defaults );

		if ( 'icons' === $key && 'font' === $this->setting['icons'] ) {
			$this->setting['icons'] = 'svg';
		}

		return $key ? $this->setting[ $key ] : $this->setting;
	}

	/**
	 * Return the plugin setting as it is in the database.
	 * @return array
	 */
	protected function get_database_setting() {
		return get_option( $this->page, array() );
	}

	/**
	 * Define the default plugin settings.
	 * @return array
	 * @since 1.3.0
	 */
	protected function defaults() {
		return include SCRIPTLESSOCIALSHARING_DIR . '/includes/settings/defaults.php';
	}

	/**
	 * Define sections for settings page.
	 *
	 * @since 3.0.0
	 */
	protected function register_sections() {
		return include SCRIPTLESSOCIALSHARING_DIR . '/includes/settings/sections.php';
	}

	/**
	 * Add the sections to the settings page.
	 *
	 * @param $sections array
	 */
	protected function add_sections( $sections ) {
		foreach ( $sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				array( $this, 'section_description' ),
				$this->page
			);
		}
	}

	/**
	 * Register settings fields
	 *
	 * @return array     settings fields
	 *
	 */
	protected function register_fields() {
		return include SCRIPTLESSOCIALSHARING_DIR . '/includes/settings/fields.php';
	}

	/**
	 * Add the fields to the settings page.
	 *
	 * @param $fields   array
	 * @param $sections array
	 */
	protected function add_fields( $fields, $sections ) {
		$fields_class = new Fields( $this->get_setting() );
		foreach ( $fields as $field ) {
			add_settings_field(
				$field['id'],
				sprintf( '<label for="%s[%s]">%s</label>', $this->page, $field['id'], $field['title'] ),
				array( $fields_class, 'do_field' ),
				$this->page,
				$sections[ $field['section'] ]['id'],
				$field
			);
		}
	}

	/**
	 * Echo the section description.
	 *
	 * @param $args
	 *
	 * @since 2.0.0
	 */
	public function section_description( $args ) {
		$sections = $this->register_sections();
		if ( empty( $sections[ $args['id'] ]['description'] ) ) {
			return;
		}
		echo wp_kses_post( wpautop( $sections[ $args['id'] ]['description'] ) );
	}

	/**
	 * Get the available buttons.
	 *
	 * @param array $choices
	 *
	 * @return array
	 * @internal param $args
	 */
	public function get_buttons( $choices = array() ) {
		$networks = $this->get_networks();
		foreach ( $networks as $network ) {
			$choices[ $network['name'] ] = $network['label'];
		}

		return $choices;
	}

	/**
	 * Build the array of networks for choices.
	 * Using the filter to add/remove networks will change the settings page and the output.
	 * @return array
	 */
	public function get_networks() {
		return include SCRIPTLESSOCIALSHARING_DIR . '/includes/settings/networks.php';
	}

	/**
	 * Define the choices for the content types setting.
	 * @return array
	 * @since 1.3.0
	 */
	public function post_type_choices() {
		$choices = array();
		foreach ( $this->get_post_types() as $post_type ) {
			$choices[ $post_type->name ] = $post_type->labels->name;
		}

		return $choices;
	}

	/**
	 * Get all registered, public post types.
	 * @return array
	 */
	protected function get_post_types() {
		$output         = 'objects';
		$built_in       = array(
			'public'   => true,
			'_builtin' => true,
		);
		$built_in_types = get_post_types( $built_in, $output );
		unset( $built_in_types['attachment'] );
		$custom_args  = array(
			'public'   => true,
			'_builtin' => false,
		);
		$custom_types = get_post_types( $custom_args, $output );

		return array_merge( $built_in_types, $custom_types );
	}

	/**
	 * Add an admin notice for users who have upgraded from 1.x
	 *
	 * @since 2.0.0
	 */
	public function notice() {
		$location = $this->get_setting( 'location' );
		if ( ! $location ) {
			return;
		}
		$message  = '<p>' . __( 'Scriptless Social Sharing 2.0 makes <strong>significant</strong> changes to how buttons are managed for each type of content on your site. Settings for button locations and content types have changed. If you\'ve removed the default buttons and replaced them with code, you\'ll want to check the Manual option for affected content types, and uncheck the specific locations for those content types.', 'scriptless-social-sharing' ) . '</p>';
		$message .= '<p>' . __( 'The buttons on your site will not change until you have updated the settings here. Once you\'ve saved the new settings, this notice will not show again.', 'scriptless-social-sharing' ) . '</p>';
		printf( '<div class="notice notice-warning">%s</div>', wp_kses_post( $message ) );
	}

	/**
	 * Validate all settings.
	 *
	 * @param  array $new_value new values from settings page
	 *
	 * @return array            validated values
	 *
	 * @since 1.0.0
	 */
	public function do_validation_things( $new_value ) {

		if ( ! $this->user_can_save( "{$this->page}_save-settings", "{$this->page}_nonce" ) ) {
			wp_die( esc_attr__( 'Something unexpected happened. Please try again.', 'scriptless-social-sharing' ) );
		}
		check_admin_referer( "{$this->page}_save-settings", "{$this->page}_nonce" );

		$validate = new Validate();

		return $validate->validate( $this->register_fields(), $new_value );
	}

	/**
	 * Determines if the user has permission to save the information from the submenu
	 * page.
	 *
	 * @since    2.0.0
	 * @access   protected
	 *
	 * @param    string    $action   The name of the action specified on the submenu page
	 * @param    string    $nonce    The nonce specified on the submenu page
	 *
	 * @return   bool                True if the user has permission to save; false, otherwise.
	 * @author   Tom McFarlin (https://tommcfarlin.com/save-wordpress-submenu-page-options/)
	 */
	protected function user_can_save( $action, $nonce ) {
		$is_nonce_set   = isset( $_POST[ $nonce ] );
		$is_valid_nonce = false;

		if ( $is_nonce_set ) {
			$is_valid_nonce = wp_verify_nonce( $_POST[ $nonce ], $action );
		}
		return ( $is_nonce_set && $is_valid_nonce );
	}
}
