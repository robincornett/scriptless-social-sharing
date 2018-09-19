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
	}

	/**
	 * Build the settings page.
	 * @since 2.0.0
	 */
	public function build_settings_page() {
		$this->setting = $this->get_setting();
		$sections      = $this->register_sections();
		$this->fields  = $this->register_fields();
		$this->add_sections( $sections );
		$this->add_fields( $this->fields, $sections );
		add_action( 'admin_notices', array( $this, 'notice' ) );
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
	 * Add new fields to wp-admin/options-general.php?page=scriptlesssocialsharing
	 *
	 * @since 2.2.0
	 */
	public function register_settings() {
		register_setting( $this->page, $this->page, array( $this, 'do_validation_things' ) );
	}

	/**
	 * Returns the plugin setting, merged with defaults.
	 * @return array
	 */
	public function get_setting() {
		return wp_parse_args( $this->get_database_setting(), $this->defaults() );
	}

	/**
	 * Return the plugin setting as it is in the database (or with defaults if the setting does not exist)
	 * @return array
	 */
	protected function get_database_setting() {
		return get_option( $this->page, $this->defaults() );
	}

	/**
	 * Define the default plugin settings.
	 * @return array
	 * @since 1.3.0
	 */
	protected function defaults() {
		return array(
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
				'reddit'    => 0,
			),
			'twitter_handle' => '',
			'email_subject'  => __( 'A post worth sharing:', 'scriptless-social-sharing' ),
			'email_body'     => __( 'I read this post and wanted to share it with you. Here\'s the link:', 'scriptless-social-sharing' ),
			'post_types'     => array(
				'post' => array(
					'before' => 0,
					'after'  => 1,
					'manual' => 0,
				),
			),
			'location'       => false,
			'button_style'   => 1,
			'button_padding' => 12,
			'table_width'    => 'full',
		);
	}

	/**
	 * Define sections for settings page.
	 *
	 * @since 3.0.0
	 */
	protected function register_sections() {
		return array(
			'styles'        => array(
				'id'    => 'styles',
				'title' => __( 'Style Settings', 'scriptless-social-sharing' ),
			),
			'general'       => array(
				'id'    => 'general',
				'title' => __( 'Button Settings', 'scriptless-social-sharing' ),
			),
			'content_types' => array(
				'id'    => 'content_types',
				'title' => __( 'Content Types', 'scriptless-social-sharing' ),
			),
			'networks'      => array(
				'id'    => 'networks',
				'title' => __( 'Network Settings', 'scriptless-social-sharing' ),
			),
		);
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

		return array(
			$this->styles(),
			$this->button_style(),
			$this->post_types(),
			$this->heading(),
			$this->buttons(),
			$this->twitter_handle(),
			$this->email_subject(),
			$this->email_body(),
			$this->table_width(),
			$this->button_padding(),
		);
	}

	/**
	 * Define settings field for styles.
	 * @return array
	 */
	protected function styles() {
		return array(
			'id'       => 'styles',
			'title'    => __( 'Plugin Styles', 'scriptless-social-sharing' ),
			'callback' => 'do_checkbox_array',
			'section'  => 'styles',
			'choices'  => $this->get_styles(),
			'clear'    => true,
		);
	}

	/**
	 * Define settings field for buttons heading.
	 * @return array
	 */
	protected function heading() {
		return array(
			'id'       => 'heading',
			'title'    => __( 'Heading', 'scriptless-social-sharing' ),
			'callback' => 'do_text_field',
			'section'  => 'general',
		);
	}

	/**
	 * Define settings field for buttons.
	 * @return array
	 */
	protected function buttons() {
		return array(
			'id'       => 'buttons',
			'title'    => __( 'Buttons', 'scriptless-social-sharing' ),
			'callback' => 'do_checkbox_array',
			'section'  => 'general',
			'choices'  => $this->get_buttons(),
		);
	}

	/**
	 * Define settings field for default twitter handle.
	 * @return array
	 */
	protected function twitter_handle() {
		return array(
			'id'       => 'twitter_handle',
			'title'    => __( 'Twitter Handle', 'scriptless-social-sharing' ),
			'callback' => 'do_text_field',
			'section'  => 'networks',
		);
	}

	/**
	 * Define settings field for default email subject.
	 * @return array
	 */
	protected function email_subject() {
		return array(
			'id'       => 'email_subject',
			'title'    => __( 'Email Subject', 'scriptless-social-sharing' ),
			'callback' => 'do_text_field',
			'section'  => 'networks',
		);
	}

	/**
	 * Define the email body setting.
	 *
	 * @return array
	 */
	protected function email_body() {
		return array(
			'id'       => 'email_body',
			'title'    => __( 'Email Content', 'scriptless-social-sharing' ),
			'callback' => 'do_textarea_field',
			'section'  => 'networks',
		);
	}

	/**
	 * Define settings field for post types.
	 * @return array
	 */
	protected function post_types() {
		return array(
			'id'       => 'post_types',
			'title'    => __( 'Content Types', 'scriptless-social-sharing' ),
			'callback' => 'do_content_types',
			'section'  => 'content_types',
			'choices'  => $this->post_type_choices(),
		);
	}

	/**
	 * Define settings field for button location.
	 * @return array
	 */
	protected function location() {
		return array(
			'id'       => 'location',
			'title'    => __( 'Sharing Buttons Location', 'scriptless-social-sharing' ),
			'callback' => 'do_checkbox_array',
			'section'  => 'general',
			'choices'  => array(
				'before' => __( 'Before Content', 'scriptless-social-sharing' ),
				'after'  => __( 'After Content', 'scriptless-social-sharing' ),
			),
		);
	}

	/**
	 * @return array
	 */
	protected function button_style() {
		return array(
			'id'       => 'button_style',
			'title'    => __( 'Button Styles', 'scriptless-social-sharing' ),
			'callback' => 'do_radio_buttons',
			'section'  => 'styles',
			'buttons' => array(
				0 => __( 'Icon Only', 'scriptless-social-sharing' ),
				1 => __( 'Icon Plus Text', 'scriptless-social-sharing' ),
			),
			'legend'  => __( 'Button styles for larger screens', 'scriptless-social-sharing' ),
		);
	}

	/**
	 * Setting for table width.
	 * @return array
	 * @since 1.4.0
	 */
	protected function table_width() {
		return array(
			'id'       => 'table_width',
			'title'    => __( 'Button Container Width', 'scriptless-social-sharing' ),
			'callback' => 'do_radio_buttons',
			'section'  => 'styles',
			'buttons' => array(
				'full' => __( 'Full Width', 'scriptless-social-sharing' ),
				'auto' => __( 'Auto', 'scriptless-social-sharing' ),
			),
			'legend'  => __( 'Width of button container', 'scriptless-social-sharing' ),
		);
	}

	/**
	 * Define args for the button padding setting.
	 * @return array
	 * @since 1.4.0
	 */
	protected function button_padding() {
		return array(
			'id'       => 'button_padding',
			'title'    => __( 'Button Padding', 'scriptless-social-sharing' ),
			'callback' => 'do_number',
			'section'  => 'styles',
			'label'    => __( ' pixels', 'scriptless-social-sharing' ),
			'min'      => 0,
			'max'      => 400,
		);
	}

	/**
	 * Add the fields to the settings page.
	 *
	 * @param $fields   array
	 * @param $sections array
	 */
	protected function add_fields( $fields, $sections ) {
		foreach ( $fields as $field ) {
			add_settings_field(
				$field['id'],
				sprintf( '<label for="%s[%s]">%s</label>', $this->page, $field['id'], $field['title'] ),
				array( $this, $field['callback'] ),
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
		$method = "{$args['id']}_section_description";
		if ( ! method_exists( $this, $method ) ) {
			return;
		}
		echo wp_kses_post( wpautop( $this->$method() ) );
	}

	/**
	 * Output the description for the styles section.
	 * @since 1.3.0
	 */
	public function styles_section_description() {
		return __( 'Choose what plugin styles you want to enable or disable.', 'scriptless-social-sharing' );
	}

	/**
	 * Callback for general plugin settings section.
	 *
	 * @since 2.4.0
	 */
	public function general_section_description() {
		return __( 'Include an optional heading with your buttons and select which social network buttons to show.', 'scriptless-social-sharing' );
	}

	/**
	 * Output the description for the networks section.
	 * @since 1.3.0
	 */
	public function networks_section_description() {
		return __( 'Some social networks need a little extra information.', 'scriptless-social-sharing' );
	}

	/**
	 * Return the description for the content types section.
	 * @return string
	 */
	public function content_types_section_description() {
		return __( 'You now have granular control over sharing buttons for each type of content on your site.', 'scriptless-social-sharing' );
	}

	/**
	 * Generic callback to create a checkbox setting.
	 *
	 * @since 1.0.0
	 */
	public function do_checkbox( $args ) {
		$setting = $this->get_checkbox_setting( $args );
		printf( '<input type="hidden" name="%s[%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['id'] ) );
		printf( '<label for="%1$s[%2$s]" style="margin-right:12px;"><input type="checkbox" name="%1$s[%2$s]" id="%1$s[%2$s]" value="1" %3$s class="code" />%4$s</label>',
			esc_attr( $this->page ),
			esc_attr( $args['id'] ),
			checked( 1, esc_attr( $setting ), false ),
			esc_attr( $args['label'] )
		);
		$this->do_description( $args['id'] );
	}

	/**
	 * Get the current value for the checkbox.
	 *
	 * @param $args
	 *
	 * @return int
	 */
	protected function get_checkbox_setting( $args ) {
		$setting = isset( $this->setting[ $args['id'] ] ) ? $this->setting[ $args['id'] ] : 0;
		if ( isset( $args['setting_name'] ) ) {
			if ( isset( $this->setting[ $args['setting_name'] ][ $args['name'] ] ) ) {
				$setting = $this->setting[ $args['setting_name'] ][ $args['name'] ];
			}
		}

		return $setting;
	}

	/**
	 * Generic callback to create a number field setting.
	 *
	 * @since 1.0.0
	 */
	public function do_number( $args ) {
		$setting = isset( $this->setting[ $args['id'] ] ) ? $this->setting[ $args['id'] ] : 0;
		if ( ! isset( $setting ) ) {
			$setting = 0;
		}
		printf( '<label for="%s[%s]">', esc_attr( $this->page ), esc_attr( $args['id'] ) );
		printf( '<input type="number" step="1" min="%1$s" max="%2$s" id="%5$s[%3$s]" name="%5$s[%3$s]" value="%4$s" class="small-text" />%6$s</label>',
			(int) $args['min'],
			(int) $args['max'],
			esc_attr( $args['id'] ),
			esc_attr( $setting ),
			esc_attr( $this->page ),
			esc_attr( $args['label'] )
		);
		$this->do_description( $args['id'] );

	}

	/**
	 * Custom callback to create dropdown fields for each content type.
	 *
	 * @param $args array
	 */
	public function do_content_types( $args ) {
		$this->do_description( $args['id'] );
		foreach ( $this->get_post_types() as $post_type ) {
			echo '<h4 class="heading">' . esc_attr( $post_type->labels->name ) . '</h4>';
			$options = array(
				'before' => __( 'Before Content', 'scriptless-social-sharing' ),
				'after'  => __( 'After Content', 'scriptless-social-sharing' ),
				'manual' => __( 'Manual Placement', 'scriptless-social-sharing' ),
			);
			foreach ( $options as $key => $value ) {
				$setting = $this->get_content_types_location( $post_type, $key );
				printf( '<input type="hidden" name="%s[post_types][%s][%s]" value="0" />', esc_attr( $this->page ), esc_attr( $post_type->name ), esc_attr( $key ) );
				printf( '<label for="%4$s[post_types][%5$s][%1$s]" style="margin-right:12px;"><input type="checkbox" name="%4$s[post_types][%5$s][%1$s]" id="%4$s[post_types][%5$s][%1$s]" value="1"%2$s class="code"/>%3$s</label>',
					esc_attr( $key ),
					checked( 1, $setting, false ),
					esc_html( $value ),
					esc_attr( $this->page ),
					esc_attr( $post_type->name )
				);
			}
		}
	}

	/**
	 * Check the database setting
	 * @param $post_type
	 * @param $key
	 *
	 * @return int
	 */
	protected function get_content_types_location( $post_type, $key ) {
		$setting = 0;
		if ( isset( $this->setting['post_types'][ $post_type->name ][ $key ] ) ) {
			return $this->setting['post_types'][ $post_type->name ][ $key ];
		}
		if ( isset( $this->setting['post_types'][ $post_type->name ] ) && $this->setting['post_types'][ $post_type->name ] && isset( $this->setting['location'] ) ) {
			if ( 'manual' === $key ) {
				$setting = 1;
			}
			if ( $this->setting['location']['before'] && 'before' === $key ) {
				$setting = 1;
			} elseif ( $this->setting['location']['after'] && 'after' === $key ) {
				$setting = 1;
			}
		}
		return $setting;
	}

	/**
	 * Generic callback to create a text field.
	 *
	 * @since 1.0.0
	 */
	public function do_text_field( $args ) {
		printf( '<input type="text" id="%3$s[%1$s]" name="%3$s[%1$s]" value="%2$s" class="regular-text" />',
			esc_attr( $args['id'] ),
			esc_attr( $this->setting[ $args['id'] ] ),
			esc_attr( $this->page )
		);
		$this->do_description( $args['id'] );
	}

	/**
	 * Generic function to create a radio button setting
	 */
	public function do_radio_buttons( $args ) {
		echo '<fieldset>';
		printf( '<legend class="screen-reader-text">%s</legend>', $args['legend'] );
		foreach ( $args['buttons'] as $key => $button ) {
			printf( '<label for="%5$s[%1$s][%2$s]" style="margin-right:12px !important;"><input type="radio" id="%5$s[%1$s][%2$s]" name="%5$s[%1$s]" value="%2$s"%3$s />%4$s</label>  ',
				esc_attr( $args['id'] ),
				esc_attr( $key ),
				checked( $key, $this->setting[ $args['id'] ], false ),
				esc_attr( $button ),
				esc_attr( $this->page )
			);
		}
		echo '</fieldset>';
		$this->do_description( $args['id'] );
	}

	/**
	 * Generic function to output a textarea
	 * @param $args
	 */
	public function do_textarea_field( $args ) {
		$rows = isset( $args['rows'] ) ? $args['rows'] : 3;
		printf( '<textarea class="regular-text" rows="%4$s" id="%3$s[%1$s]" name="%3$s[%1$s]" aria-label="%3$s[%1$s]">%2$s</textarea>',
			esc_attr( $args['id'] ),
			esc_textarea( $this->setting[ $args['id'] ] ),
			esc_attr( $this->page ),
			(int) $rows
		);
		$this->do_description( $args['id'] );
	}

	/**
	 * Generic callback to display a field description.
	 *
	 * @param  string $args setting name used to identify description callback
	 */
	protected function do_description( $args ) {
		$function = $args . '_description';
		if ( ! method_exists( $this, $function ) ) {
			return;
		}
		$description = $this->$function();
		printf( '<p class="description">%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Define the array of style settings.
	 * @return array
	 */
	protected function get_styles() {
		return array(
			'plugin'   => __( 'Load the main stylesheet? (colors and layout)', 'scriptless-social-sharing' ),
			'font'     => __( 'Load Font Awesome? (just the font)', 'scriptless-social-sharing' ),
			'font_css' => __( 'Use plugin Font Awesome CSS? (adds the icons to the buttons)', 'scriptless-social-sharing' ),
		);
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
		return apply_filters( 'scriptlesssocialsharing_networks', array(
			'twitter'   => array(
				'name'  => 'twitter',
				'label' => __( 'Twitter', 'scriptless-social-sharing' ),
			),
			'facebook'  => array(
				'name'  => 'facebook',
				'label' => __( 'Facebook', 'scriptless-social-sharing' ),
			),
			'google'    => array(
				'name'  => 'google',
				'label' => __( 'Google+', 'scriptless-social-sharing' ),
			),
			'pinterest' => array(
				'name'  => 'pinterest',
				'label' => __( 'Pinterest', 'scriptless-social-sharing' ),
			),
			'linkedin'  => array(
				'name'  => 'linkedin',
				'label' => __( 'Linkedin', 'scriptless-social-sharing' ),
			),
			'email'     => array(
				'name'  => 'email',
				'label' => __( 'Email', 'scriptless-social-sharing' ),
			),
			'reddit'    => array(
				'name'  => 'reddit',
				'label' => __( 'Reddit', 'scriptless-social-sharing' ),
			),
		) );
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
	 * Set up choices for checkbox array
	 *
	 * @param $args array
	 */
	public function do_checkbox_array( $args ) {
		foreach ( $args['choices'] as $key => $label ) {
			// due to error in setting this up in v 1.0-1.2, have to do a BC check for the post_type setting.
			$setting = isset( $this->setting[ $args['id'] ][ $key ] ) ? $this->setting[ $args['id'] ][ $key ] : 0;
			if ( 'post_types' === $args['id'] && ! isset( $this->setting[ $args['id'] ][ $key ] ) ) {
				$setting = in_array( $key, $this->setting['post_types'], true );
			}
			printf( '<input type="hidden" name="%s[%s][%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['id'] ), esc_attr( $key ) );
			printf( '<label for="%4$s[%5$s][%1$s]" style="margin-right:12px;"><input type="checkbox" name="%4$s[%5$s][%1$s]" id="%4$s[%5$s][%1$s]" value="1"%2$s class="code"/>%3$s</label>',
				esc_attr( $key ),
				checked( 1, $setting, false ),
				esc_html( $label ),
				esc_attr( $this->page ),
				esc_attr( $args['id'] )
			);
			echo isset( $args['clear'] ) && $args['clear'] ? '<br />' : '';
		}
		$this->do_description( $args['id'] );
	}

	/**
	 * Description for the heading.
	 * @return string
	 */
	protected function heading_description() {
		return __( 'Heading above sharing buttons', 'scriptless-social-sharing' );
	}

	/**
	 * Description for the twitter handle setting.
	 * @return string
	 */
	protected function twitter_handle_description() {
		return __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' );
	}

	/**
	 * Description for the email subject setting.
	 * @return string
	 */
	protected function email_subject_description() {
		return __( 'The post title will be appended to whatever you add here.', 'scriptless-social-sharing' );
	}

	/**
	 * Description for the post types setting.
	 * @return string
	 */
	protected function post_types_description() {
		return __( 'Leave all options unchecked for no buttons. Before/after content are the traditional Scriptless Social Sharing locations (within the post/entry content). Checking manual placement will allow the plugin styles to load as needed, if you are adding the buttons using code. You do not need to check any settings to use the shortcode.', 'scriptless-social-sharing' );
	}

	/**
	 * Add an admin notice for users who have upgraded from 1.x
	 *
	 * @since 2.0.0
	 */
	public function notice() {
		if ( ! $this->setting['location'] ) {
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

		foreach ( $this->register_fields() as $field ) {
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

				case 'do_checkbox_array':
					foreach ( $field['choices'] as $key => $label ) {
						$new_value[ $field['id'] ][ $key ] = $this->one_zero( $new_value[ $field['id'] ][ $key ] );
					}
					break;

				case 'do_radio_buttons':
					$new_value[ $field['id'] ] = is_numeric( $new_value[ $field['id'] ] ) ? (int) $new_value[ $field['id'] ] : esc_attr( $new_value[ $field['id'] ] );
					break;

				case 'do_content_types':
					array_walk_recursive( $new_value[ $field['id'] ], array( $this, 'validate_content_types' ) );
					break;
			}
		}
		$new_value['location'] = false;

		return $new_value;
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

	/**
	 * Returns a 1 or 0, for all truthy / falsy values.
	 *
	 * Uses double casting. First, we cast to bool, then to integer.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $new_value Should ideally be a 1 or 0 integer passed in
	 *
	 * @return integer 1 or 0.
	 */
	protected function one_zero( $new_value ) {
		return (int) (bool) $new_value;
	}

	/**
	 * Validate multidimensional arrays.
	 * @param $new_value
	 * @param $key
	 */
	protected function validate_content_types( &$new_value, $key ) {
		$new_value = $this->one_zero( $new_value );
	}
}
