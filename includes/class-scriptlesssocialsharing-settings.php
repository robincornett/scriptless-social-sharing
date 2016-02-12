<?php

/**
 * Class for adding a new settings page to the WordPress admin, under Settings.
 *
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingSettings {

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
	 * Settings fields registered by plugin.
	 * @var array
	 */
	protected $fields;

	/**
	 * add a submenu page under settings
	 * @return submenu Scriptless Social Sharing settings page
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
	}

	/**
	 * Output the plugin settings form.
	 *
	 * @since 1.0.0
	 */
	public function do_settings_form() {

		$this->setting = $this->get_setting();
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
	 * Add new fields to wp-admin/options-general.php?page=scriptlesssocialsharing
	 *
	 * @since 2.2.0
	 */
	public function register_settings() {

		register_setting( $this->page, $this->page, array( $this, 'do_validation_things' ) );

		$this->register_sections();

	}

	/**
	 * @return array Setting for plugin, or defaults.
	 */
	public function get_setting() {

		$defaults = array(
			'styles'         => array(
				'plugin'   => 1,
				'font'     => 1,
				'font_css' => 1,
			),
			'heading'        => __( 'Share this post:', 'scriptless-social-sharing' ),
			'buttons'        => array(
				'twitter'   => 1,
				'facebook'  => 1,
				'google'    => 1,
				'pinterest' => 1,
				'linkedin'  => 1,
				'email'     => 1,
			),
			'twitter_handle' => '',
			'email_subject'  => __( 'A post worth sharing:', 'scriptless-social-sharing' ),
		);

		$setting = get_option( $this->page, $defaults );

		return $setting;
	}

	/**
	 * Register sections for settings page.
	 *
	 * @since 3.0.0
	 */
	protected function register_sections() {

		$sections = array(
			'general' => array(
				'id'    => 'general',
				'title' => __( 'General Settings', 'scriptless-social-sharing' ),
			),
		);

		foreach ( $sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				array( $this, $section['id'] . '_section_description' ),
				$this->page
			);
		}

		$this->register_fields( $sections );

	}

	/**
	 * Register settings fields
	 *
	 * @param  settings sections $sections
	 *
	 * @return fields           settings fields
	 *
	 * @since 3.0.0
	 */
	protected function register_fields( $sections ) {

		$this->fields = array(
			array(
				'id'       => 'styles',
				'title'    => __( 'Plugin Styles', 'scriptless-social-sharing' ),
				'callback' => 'do_styles',
				'section'  => 'general',
				'args'     => array(
					'setting' => 'styles',
				),
			),
			array(
				'id'       => 'heading',
				'title'    => __( 'Heading', 'scriptless-social-sharing' ),
				'callback' => 'do_text_field',
				'section'  => 'general',
				'args'     => array( 'setting' => 'heading' ),
			),
			array(
				'id'       => 'buttons',
				'title'    => __( 'Buttons', 'scriptless-social-sharing' ),
				'callback' => 'do_buttons',
				'section'  => 'general',
				'args'     => array( 'setting' => 'buttons' ),
			),
			array(
				'id'       => 'twitter_handle',
				'title'    => __( 'Twitter Handle', 'scriptless-social-sharing' ),
				'callback' => 'do_text_field',
				'section'  => 'general',
				'args'     => array( 'setting' => 'twitter_handle' ),
			),
			array(
				'id'       => 'email_subject',
				'title'    => __( 'Email Subject', 'scriptless-social-sharing' ),
				'callback' => 'do_text_field',
				'section'  => 'general',
				'args'     => array( 'setting' => 'email_subject' ),
			),
		);

		foreach ( $this->fields as $field ) {
			add_settings_field(
				'[' . $field['id'] . ']',
				sprintf( '<label for="%s">%s</label>', $field['id'], $field['title'] ),
				array( $this, $field['callback'] ),
				$this->page,
				$sections[$field['section']]['id'],
				empty( $field['args'] ) ? array() : $field['args']
			);
		}
	}

	/**
	 * Callback for general plugin settings section.
	 *
	 * @since 2.4.0
	 */
	public function general_section_description() {
		$description = __( 'You can set the default isotope settings here.', 'scriptless-social-sharing' );
		printf( '<p>%s</p>', wp_kses_post( $description ) );
	}

	/**
	 *
	 */
	public function cpt_section_description() {
		$description = __( 'Set the isotope settings for each post type.', 'scriptless-social-sharing' );
		printf( '<p>%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Generic callback to create a checkbox setting.
	 *
	 * @since 3.0.0
	 */
	public function do_checkbox( $args ) {
		$setting = isset( $this->setting[ $args['setting'] ] ) ? $this->setting[ $args['setting'] ] : 0;
		if ( isset( $args['setting_name'] ) && 'buttons' === $args['setting_name'] ) {
			$setting = $this->setting[ $args['setting_name'] ][ $args['network'] ];
		}
		if ( isset( $args['setting_name'] ) && 'styles' === $args['setting_name'] ) {
			$setting = $this->setting[ $args['setting_name'] ][ $args['name'] ];
		}
		printf( '<input type="hidden" name="%s[%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['setting'] ) );
		printf( '<label for="%1$s[%2$s]"><input type="checkbox" name="%1$s[%2$s]" id="%1$s[%2$s]" value="1" %3$s class="code" />%4$s</label>',
			esc_attr( $this->page ),
			esc_attr( $args['setting'] ),
			checked( 1, esc_attr( $setting ), false ),
			esc_attr( $args['label'] )
		);
		$this->do_description( $args['setting'] );
	}

	/**
	 * Generic callback to create a number field setting.
	 *
	 * @since 3.0.0
	 */
	public function do_number( $args ) {
		$setting = isset( $this->setting[$args['setting']] ) ? $this->setting[$args['setting']] : 0;
		if ( ! isset( $setting ) ) {
			$setting = 0;
		}
		printf( '<label for="%s[%s]">%s</label>', esc_attr( $this->page ), esc_attr( $args['setting'] ), esc_attr( $args['label'] ) );
		printf( '<input type="number" step="1" min="%1$s" max="%2$s" id="%5$s[%3$s]" name="%5$s[%3$s]" value="%4$s" class="small-text" />',
			(int) $args['min'],
			(int) $args['max'],
			esc_attr( $args['setting'] ),
			esc_attr( $setting ),
			esc_attr( $this->page )
		);
		$this->do_description( $args['setting'] );

	}

	/**
	 * Generic callback to create a select/dropdown setting.
	 *
	 * @since 3.0.0
	 */
	public function do_select( $args ) {
		$function = 'pick_' . $args['options'];
		$options  = $this->$function(); ?>
		<select id="scriptlesssocialsharing[<?php echo esc_attr( $args['setting'] ); ?>]"
		        name="scriptlesssocialsharing[<?php echo esc_attr( $args['setting'] ); ?>]">
			<?php
			foreach ( (array) $options as $name => $key ) {
				printf( '<option value="%s" %s>%s</option>', esc_attr( $name ), selected( $name, $this->setting[$args['setting']], false ), esc_attr( $key ) );
			} ?>
		</select> <?php
		$this->do_description( $args['setting'] );
	}

	/**
	 * Generic callback to create a text field.
	 *
	 * @since 3.0.0
	 */
	public function do_text_field( $args ) {
		printf( '<input type="text" id="%3$s[%1$s]" name="%3$s[%1$s]" value="%2$s" class="regular-text" />', esc_attr( $args['setting'] ), esc_attr( $this->setting[$args['setting']] ), esc_attr( $this->page ) );
		$this->do_description( $args['setting'] );
	}

	/**
	 * Generic callback to display a field description.
	 *
	 * @param  string $args setting name used to identify description callback
	 *
	 * @return string       Description to explain a field.
	 */
	protected function do_description( $args ) {
		$function = $args . '_description';
		if ( ! method_exists( $this, $function ) ) {
			return;
		}
		$description = $this->$function();
		printf( '<p class="description">%s</p>', wp_kses_post( $description ) );
	}

	public function do_styles( $args ) {
		$styles = $this->get_styles();
		foreach ( $styles as $style ) {
			$style_args = array(
				'setting'      => "{$args['setting']}][{$style['name']}",
				'label'        => $style['label'],
				'setting_name' => $args['setting'],
				'name'         => $style['name'],
			);
			$this->do_checkbox( $style_args );
			echo '<br />';
		}

	}

	protected function get_styles() {
		$styles = array(
			array(
				'name'  => 'plugin',
				'label' => __( 'Load the main stylesheet? (colors and layout)', 'scriptless-social-sharing' ),
			),
			array(
				'name'  =>'font',
				'label' => __( 'Load Font Awesome? (just the font)', 'scriptless-social-sharing' ),
			),
			array(
				'name'  => 'font_css',
				'label' => __( 'Use plugin Font Awesome CSS? (adds the icons to the buttons)', 'scriptless-social-sharing' ),
			)
		);
		return $styles;
	}

	/**
	 * @param $args
	 */
	public function do_buttons( $args ) {
		$networks = $this->get_networks();
		foreach ( $networks as $network ) {
			$network_args = array(
				'setting'      => "{$args['setting']}][{$network['name']}",
				'label'        => $network['label'],
				'setting_name' => $args['setting'],
				'network'      => $network['name'],
			);
			$this->do_checkbox( $network_args );
			echo '<br />';
		}

	}

	/**
	 * @return mixed|void
	 */
	public function get_networks() {
		$networks = array(
			'twitter' => array(
				'name' => 'twitter',
				'label'   => __( 'Twitter', 'scriptless-social-sharing' ),
			),
			'facebook' => array(
				'name' => 'facebook',
				'label'   => __( 'Facebook', 'scriptless-social-sharing' ),
			),
			'google' => array(
				'name' => 'google',
				'label'   => __( 'Google+', 'scriptless-social-sharing' ),
			),
			'pinterest' => array(
				'name' => 'pinterest',
				'label'   => __( 'Pinterest', 'scriptless-social-sharing' ),
			),
			'linkedin' => array(
				'name' => 'linkedin',
				'label'   => __( 'Linkedin', 'scriptless-social-sharing' ),
			),
			'email' => array(
				'name' => 'email',
				'label'   => __( 'Email', 'scriptless-social-sharing' ),
			),
		);
		return apply_filters( 'scriptlesssocialsharing_networks', $networks );
	}

	/**
	 * @return string|void
	 */
	protected function heading_description() {
		return __( 'Heading above sharing buttons', 'scriptless-social-sharing' );
	}

	/**
	 * @return string|void
	 */
	protected function email_subject_description() {
		return __( 'The post title will be appended to whatever you add here.', 'scriptless-social-sharing' );
	}

	/**
	 * Validate all settings.
	 *
	 * @param  array $new_value new values from settings page
	 *
	 * @return array            validated values
	 *
	 * @since 3.0.0
	 */
	public function do_validation_things( $new_value ) {

		if ( empty( $_POST[ $this->page . '_nonce' ] ) ) {
			wp_die( esc_attr__( 'Something unexpected happened. Please try again.', 'scriptless-social-sharing' ) );
		}

		check_admin_referer( "{$this->page}_save-settings", "{$this->page}_nonce" );

		foreach ( $this->fields as $field ) {
			switch ( $field['callback'] ) {
				case 'do_checkbox':
					$new_value[ $field['id'] ] = $this->one_zero( $new_value[ $field['id'] ] );
					break;

				case 'do_select':
					$new_value[ $field['id'] ] = esc_attr( $new_value[ $field['id'] ] );
					break;

				case 'do_number':
					$new_value[ $field['id'] ] = (int) $new_value[ $field['id'] ];
					break;
			}
		}

		$networks = $this->get_networks();
		foreach ( $networks as $network ) {
			$new_value['buttons'][ $network['name'] ] = $this->one_zero( $new_value['buttons'][ $network['name'] ] );
		}

		$styles = $this->get_styles();
		foreach ( $styles as $style ) {
			$new_value['styles'][ $style['name'] ] = $this->one_zero( $new_value['styles'][ $style['name'] ] );
		}

		return $new_value;

	}

	/**
	 * Returns a 1 or 0, for all truthy / falsy values.
	 *
	 * Uses double casting. First, we cast to bool, then to integer.
	 *
	 * @since 2.4.0
	 *
	 * @param mixed $new_value Should ideally be a 1 or 0 integer passed in
	 *
	 * @return integer 1 or 0.
	 */
	protected function one_zero( $new_value ) {
		return (int) (bool) $new_value;
	}
}
