<?php

/**
 * Class for adding a new settings page to the WordPress admin, under Settings.
 *
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingSettings {

	/**
	 * Full path and filename of the root plugin file.
	 *
	 * @var string
	 */
	protected $file;

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
	 * Settings Constructor
	 *
	 * @param string $file Full path and filename of the root plugin file.
	 */
	public function __construct( string $file ) {
		$this->file = $file;
	}

	/**
	 * Get the hook name for the plugin_action_link filter for this plugin.
	 *
	 * @return string The hook name for the plugin_action_link filter.
	 */
	public function get_plugin_action_link_filter_name() {
		return sprintf(
			'plugin_action_links_%s',
			plugin_basename( $this->file )
		);
	}

	/**
	 * Append the markup for a settings link for this plugin.
	 *
	 * @param  string[] $links An array of markup links displayed for the plugin on the Plugins page.
	 * @return string[] Array of markup links modified to include a link to the settings page.
	 */
	public function append_settings_link( $links ) {
		$links[] = sprintf( '<a href="%s">%s</a>',
			esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->page ) ) ),
			__( 'Settings' )
		);
		return $links;
	}

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

		$sections     = $this->register_sections();
		$this->fields = $this->register_fields();
		$this->add_sections( $sections );
		$this->add_fields( $this->fields, $sections );
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
	}

	/**
	 * @return array Setting for plugin, or defaults.
	 */
	public function get_setting() {
		$setting = get_option( $this->page, $this->defaults() );
		return wp_parse_args( $setting, $this->defaults() );
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
			'post_types'     => array( 'post' ),
			'location'       => array(
				'before' => 0,
				'after' => 1,
			),
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
			'styles' => array(
				'id'    => 'styles',
				'title' => __( 'Style Settings', 'scriptless-social-sharing' ),
			),
			'general' => array(
				'id'    => 'general',
				'title' => __( 'Button Settings', 'scriptless-social-sharing' ),
			),
			'networks' => array(
				'id'    => 'networks',
				'title' => __( 'Network Settings', 'scriptless-social-sharing' ),
			),
		);
	}

	/**
	 * Add the sections to the settings page.
	 * @param $sections array
	 */
	protected function add_sections( $sections ) {
		foreach ( $sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				array( $this, $section['id'] . '_section_description' ),
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
			$this->location(),
			$this->heading(),
			$this->buttons(),
			$this->twitter_handle(),
			$this->email_subject(),
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
			'args'     => array(
				'setting' => 'styles',
				'choices' => $this->get_styles(),
				'clear'   => true,
			),
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
			'args'     => array( 'setting' => 'heading' ),
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
			'args'     => array( 'setting' => 'buttons', 'choices' => $this->get_buttons() ),
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
			'args'     => array( 'setting' => 'twitter_handle' ),
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
			'args'     => array( 'setting' => 'email_subject' ),
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
			'callback' => 'do_checkbox_array',
			'section'  => 'general',
			'args'     => array( 'setting' => 'post_types', 'choices' => $this->post_type_choices() ),
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
			'args'     => array(
				'setting' => 'location',
				'choices' => array(
					'before' => __( 'Before Content', 'scriptless-social-sharing' ),
					'after'  => __( 'After Content', 'scriptless-social-sharing' ),
				),
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
			'args'        => array(
				'id'      => 'button_style',
				'buttons' => array(
					0 => __( 'Icon Only', 'scriptless-social-sharing' ),
					1 => __( 'Icon Plus Text', 'scriptless-social-sharing' ),
				),
				'legend'  => __( 'Button styles for larger screens', 'scriptless-social-sharing' ),
			),
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
			'args'        => array(
				'id'      => 'table_width',
				'buttons' => array(
					'full' => __( 'Full Width', 'scriptless-social-sharing' ),
					'auto' => __( 'Auto', 'scriptless-social-sharing' ),
				),
				'legend'  => __( 'Width of button container', 'scriptless-social-sharing' ),
			),
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
			'title'    => __( 'Button Padding' , 'scriptless-social-sharing' ),
			'callback' => 'do_number',
			'section'  => 'styles',
			'args'     => array( 'setting' => 'button_padding', 'label' => __( ' pixels', 'scriptless-social-sharing' ), 'min' => 0, 'max' => 400 ),
		);
	}

	/**
	 * Add the fields to the settings page.
	 * @param $fields array
	 * @param $sections array
	 */
	protected function add_fields( $fields, $sections ) {
		foreach ( $fields as $field ) {
			add_settings_field(
				'[' . $field['id'] . ']',
				sprintf( '<label for="%s">%s</label>', $field['id'], $field['title'] ),
				array( $this, $field['callback'] ),
				$this->page,
				$sections[ $field['section'] ]['id'],
				empty( $field['args'] ) ? array() : $field['args']
			);
		}
	}

	/**
	 * Output the description for the styles section.
	 * @since 1.3.0
	 */
	public function styles_section_description() {
		$description = __( 'Choose what plugin styles you want to enable or disable.', 'scriptless-social-sharing' );
		printf( '<p>%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Callback for general plugin settings section.
	 *
	 * @since 2.4.0
	 */
	public function general_section_description() {
		$description = __( 'Scriptless Social Sharing tries to be helpful, but you can also disable whatever you need.', 'scriptless-social-sharing' );
		printf( '<p>%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Output the description for the networks section.
	 * @since 1.3.0
	 */
	public function networks_section_description() {
		$description = __( 'Some social networks need a little extra information.', 'scriptless-social-sharing' );
		printf( '<p>%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Generic callback to create a checkbox setting.
	 *
	 * @since 1.0.0
	 */
	public function do_checkbox( $args ) {
		$setting = $this->get_checkbox_setting( $args );
		printf( '<input type="hidden" name="%s[%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['setting'] ) );
		printf( '<label for="%1$s[%2$s]" style="margin-right:12px;"><input type="checkbox" name="%1$s[%2$s]" id="%1$s[%2$s]" value="1" %3$s class="code" />%4$s</label>',
			esc_attr( $this->page ),
			esc_attr( $args['setting'] ),
			checked( 1, esc_attr( $setting ), false ),
			esc_attr( $args['label'] )
		);
		$this->do_description( $args['setting'] );
	}

	/**
	 * Get the current value for the checkbox.
	 * @param $args
	 *
	 * @return int
	 */
	protected function get_checkbox_setting( $args ) {
		$setting = isset( $this->setting[ $args['setting'] ] ) ? $this->setting[ $args['setting'] ] : 0;
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
		$setting = isset( $this->setting[$args['setting']] ) ? $this->setting[$args['setting']] : 0;
		if ( ! isset( $setting ) ) {
			$setting = 0;
		}
		printf( '<label for="%s[%s]">', esc_attr( $this->page ), esc_attr( $args['setting'] ) );
		printf( '<input type="number" step="1" min="%1$s" max="%2$s" id="%5$s[%3$s]" name="%5$s[%3$s]" value="%4$s" class="small-text" />%6$s</label>',
			(int) $args['min'],
			(int) $args['max'],
			esc_attr( $args['setting'] ),
			esc_attr( $setting ),
			esc_attr( $this->page ),
			esc_attr( $args['label'] )
		);
		$this->do_description( $args['setting'] );

	}

	/**
	 * Generic callback to create a select/dropdown setting.
	 *
	 * @since 1.0.0
	 */
	public function do_select( $args ) {
		$function = 'pick_' . $args['options'];
		$options  = $this->$function(); ?>
		<label for="scriptlesssocialsharing[<?php echo esc_attr( $args['setting'] ); ?>]"></label>
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
	 * @since 1.0.0
	 */
	public function do_text_field( $args ) {
		printf( '<input type="text" id="%3$s[%1$s]" name="%3$s[%1$s]" value="%2$s" class="regular-text" />', esc_attr( $args['setting'] ), esc_attr( $this->setting[$args['setting']] ), esc_attr( $this->page ) );
		$this->do_description( $args['setting'] );
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
	 * @param $args
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
	 * @return mixed|void
	 */
	public function get_networks() {
		$networks = array(
			'twitter' => array(
				'name'  => 'twitter',
				'label' => __( 'Twitter', 'scriptless-social-sharing' ),
			),
			'facebook' => array(
				'name'  => 'facebook',
				'label' => __( 'Facebook', 'scriptless-social-sharing' ),
			),
			'google' => array(
				'name'  => 'google',
				'label' => __( 'Google+', 'scriptless-social-sharing' ),
			),
			'pinterest' => array(
				'name'  => 'pinterest',
				'label' => __( 'Pinterest', 'scriptless-social-sharing' ),
			),
			'linkedin' => array(
				'name'  => 'linkedin',
				'label' => __( 'Linkedin', 'scriptless-social-sharing' ),
			),
			'email' => array(
				'name'  => 'email',
				'label' => __( 'Email', 'scriptless-social-sharing' ),
			),
			'reddit' => array(
				'name'  => 'reddit',
				'label' => __( 'Reddit', 'scriptless-social-sharing' ),
			),
		);
		return apply_filters( 'scriptlesssocialsharing_networks', $networks );
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
		$output   = 'objects';
		$built_in = array(
			'public'   => true,
			'_builtin' => true,
		);
		$built_in_types = get_post_types( $built_in, $output );
		unset( $built_in_types['attachment'] );
		$custom_args = array(
			'public'   => true,
			'_builtin' => false,
		);
		$custom_types = get_post_types( $custom_args, $output );
		return array_merge( $built_in_types, $custom_types );
	}

	/**
	 * Set up choices for checkbox array
	 * @param $args array
	 */
	public function do_checkbox_array( $args ) {
		foreach ( $args['choices'] as $key => $label ) {
			// due to error in setting this up in v 1.0-1.2, have to do a BC check for the post_type setting.
			$setting = isset( $this->setting[ $args['setting'] ][ $key ] ) ? $this->setting[ $args['setting'] ][ $key ] : 0;
			if ( 'post_types' === $args['setting'] && ! isset( $this->setting[ $args['setting'] ][ $key ] ) ) {
				$setting = in_array( $key, $this->setting['post_types'], true );
			}
			printf( '<input type="hidden" name="%s[%s][%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['setting'] ), esc_attr( $key ) );
			printf( '<label for="%4$s[%5$s][%1$s]" style="margin-right:12px;"><input type="checkbox" name="%4$s[%5$s][%1$s]" id="%4$s[%5$s][%1$s]" value="1"%2$s class="code"/>%3$s</label>',
				esc_attr( $key ),
				checked( 1, $setting, false ),
				esc_html( $label ),
				esc_attr( $this->page ),
				esc_attr( $args['setting'] )
			);
			echo isset( $args['clear'] ) && $args['clear'] ? '<br />' : '';
		}
		$this->do_description( $args['setting'] );
	}

	/**
	 * Description for the heading.
	 * @return string|void
	 */
	protected function heading_description() {
		return __( 'Heading above sharing buttons', 'scriptless-social-sharing' );
	}

	/**
	 * Description for the twitter handle setting.
	 * @return string|void
	 */
	protected function twitter_handle_description() {
		return __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' );
	}

	/**
	 * Description for the email subject setting.
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
	 * @since 1.0.0
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

				case 'do_checkbox_array':
					$choices = $field['args']['choices'];
					foreach ( $choices as $key => $label ) {
						$new_value[ $field['id'] ][ $key ] = $this->one_zero( $new_value[ $field['id'] ][ $key ] );
					}
					break;

				case 'do_radio_buttons':
					$new_value[ $field['id'] ] = esc_attr( $new_value[ $field['id'] ] );
					break;
			}
		}
		$new_value['button_style'] = (int) $new_value['button_style'];

		return $new_value;
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
}
