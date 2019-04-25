<?php

/**
 * Class ScriptlessSocialSharingOutput
 * Plugin class for buttons output--all helper methods are here.
 * Primary implementation is now in ScriptlessSocialSharingOutputButtons.
 *
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingOutput {

	/**
	 * @var $setting ScriptlessSocialSharingSettings->get_setting
	 */
	protected $setting;

	/**
	 * The array of attributes for sharing buttons.
	 * @var array
	 */
	protected $attributes;

	/**
	 * The SVG handler class.
	 *
	 * @var \ScriptlessSocialSharingOutputSVG
	 */
	protected $svg;

	/**
	 * Function to decide whether buttons can be output or not
	 *
	 * @param  boolean $cando default true
	 *
	 * @return boolean         false if not a singular post (can be modified for other content types)
	 */
	protected function can_do_buttons( $cando = true ) {
		if ( ! is_main_query() || get_the_ID() !== get_queried_object_id() ) {
			$cando = false;
		}
		if ( is_feed() || ! is_singular() ) {
			$cando = false;
		}
		if ( is_singular() ) {
			$cando = $this->check_singular_post( $cando );
		}
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
				return true;
			}
		}

		return apply_filters( 'scriptlesssocialsharing_can_do_buttons', $cando );
	}

	/**
	 * @param $cando
	 *
	 * @return bool
	 */
	private function check_singular_post( $cando ) {
		if ( has_shortcode( get_post_field( 'post_content' ), 'scriptless' ) || has_block( 'scriptlesssocialsharing/buttons' ) ) {
			return true;
		}
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true );
		if ( $is_disabled ) {
			return false;
		}
		$post_types = scriptlesssocialsharing_post_types();
		$post_type  = get_post_type();
		if ( in_array( $post_type, $post_types, true ) ) {
			return true;
		}

		return $cando;
	}

	/**
	 * Get the current plugin setting.
	 * @return mixed|\ScriptlessSocialSharingSettings
	 */
	protected function get_setting() {
		if ( isset( $this->setting ) ) {
			return $this->setting;
		}
		$this->setting = scriptlesssocialsharing_get_setting();

		return $this->setting;
	}

	/**
	 * Create the anchor element markup.
	 *
	 * @param $button array the parameters for building the button.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function build_link_markup( $button ) {
		return apply_filters(
			'scriptlesssocialsharing_link_markup',
			sprintf(
				'<a class="button %1$s"%2$s href="%3$s" rel="noopener" %4$s>%5$s<span class="%6$s">%7$s</span></a>',
				esc_attr( $button['name'] ),
				$this->get_link_target( $button['name'] ),
				esc_url( $button['url'] ),
				$button['data'],
				$this->get_svg( $button['name'] ),
				$this->get_label_class(),
				$button['label']
			),
			$button
		);
	}

	/**
	 * All links except for email should open in a new tab.
	 * @since 3.0.0
	 *
	 * @param $button
	 * @return string
	 */
	private function get_link_target( $button ) {
		return 'email' === $button ? '' : ' target="_blank"';
	}

	/**
	 * Get the button label class. Default is sss-name, but switches to
	 * screen-reader-text if icon only output is selected.
	 * @since 3.0.0
	 *
	 * @return string
	 */
	private function get_label_class() {
		$class   = 'sss-name';
		$setting = $this->get_setting();
		if ( 0 === $setting['button_style'] ) {
			$class = 'screen-reader-text';
		}

		return $class;
	}

	/**
	 * If the SVG icons are enabled, load them in the footer.
	 * @since 2.4.0
	 */
	public function maybe_load_svg() {
		$setting = $this->get_setting();
		if ( 'svg' !== $setting['icons'] ) {
			return;
		}
		$svg = $this->svg();
		add_action( 'wp_footer', array( $svg, 'load_svg' ) );
		add_action( 'admin_footer-post.php', array( $svg, 'load_svg' ) );
		add_filter( 'wp_kses_allowed_html', array( $svg, 'filter_allowed_html' ), 10, 2 );
	}

	/**
	 * If the SVG setting is enabled, do SVG.
	 *
	 * @param $icon
	 *
	 * @return string
	 */
	protected function get_svg( $icon ) {
		$setting = $this->get_setting();
		if ( 'svg' !== $setting['icons'] ) {
			return '';
		}
		if ( 2 === $setting['button_style'] ) {
			return '';
		}

		$svg = $this->svg();

		return $svg->get_svg_markup( $icon );
	}

	/**
	 * Get an array of all possible sharing buttons.
	 * @since 2.2.0
	 * @return mixed
	 */
	protected function get_all_buttons() {
		$attributes = $this->get_attributes();
		$buttons    = $this->get_buttons_in_order();
		$setting    = $this->get_setting();
		foreach ( $buttons as $key => $button ) {
			$url  = '';
			$file = plugin_dir_path( dirname( __FILE__ ) ) . "buttons/class-scriptlesssocialsharing-button-{$button['name']}.php";
			if ( file_exists( $file ) ) {
				include_once $file;
			}
			$proper_name = 'ScriptlessSocialSharingButton' . $button['label'];
			if ( class_exists( $proper_name ) && is_callable( $proper_name, 'get_url' ) ) {
				$class = new $proper_name( $button['name'], $attributes, $setting );
				$url   = $class->get_url();
			}

			/**
			 * Create a filter to build custom URLs for each network.
			 * @since 2.0.0
			 */
			$buttons[ $button['name'] ]['url'] = apply_filters( "scriptlesssocialsharing_{$button['name']}_url", $url, $button['name'], $attributes );

			/**
			 * Create a filter to add data attributes to social URLs.
			 * @since 2.0.0
			 */
			$buttons[ $button['name'] ]['data'] = apply_filters( "scriptlesssocialsharing_{$button['name']}_data", '', $button['name'], $attributes );
		}

		return apply_filters( 'scriptlesssocialsharing_buttons', $buttons, $attributes );
	}

	/**
	 * Get the social network buttons in order. If the order has been set with
	 * code and no custom order exists, use the code value. Otherwise, use the
	 * value set via GUI (which will inherit the custom order).
	 *
	 * @since 2.3.0
	 * @return array
	 */
	protected function get_buttons_in_order() {
		$settings_class = new ScriptlessSocialSharingSettings();
		$buttons        = $settings_class->get_networks();
		$setting        = $this->get_setting();
		if ( ! $setting['order'] ) {
			return $buttons;
		}

		return array_merge( $this->setting['order'], $buttons );
	}

	/**
	 * Get the array of attributes for sharing buttons.
	 * @return array
	 */
	protected function get_attributes() {
		if ( isset( $this->attributes ) && is_singular() ) {
			return $this->attributes;
		}
		include_once 'class-scriptlesssocialsharing-output-attributes.php';
		$attributes       = new ScriptlessSocialSharingOutputAttributes( $this->get_setting() );
		$this->attributes = $attributes->get_attributes();

		return $this->attributes;
	}

	/**
	 * Instantiate the SVG class.
	 * @since 2.4.0
	 *
	 * @return \ScriptlessSocialSharingOutputSVG
	 */
	protected function svg() {
		if ( isset( $this->svg ) ) {
			return $this->svg;
		}
		$this->svg = new ScriptlessSocialSharingOutputSVG();

		return $this->svg;
	}

	/**
	 * Get the correct class for the buttons container.
	 * @since 3.0.0
	 *
	 * @param $setting
	 * @return string
	 */
	protected function get_button_container_class( $setting ) {
		$prefix    = 'scriptlesssocialsharing';
		$suffix    = 'buttons';
		$container = 'svg' === $setting['icons'] ? "{$prefix}__{$suffix}" : "{$prefix}-{$suffix}";
		if ( 2 === $setting['button_style'] ) {
			$container .= ' no-icons';
		}

		return $container;
	}

	/**
	 * Modify the heading above the buttons
	 *
	 * @param $heading
	 *
	 * @return string heading
	 */
	protected function heading( $heading ) {
		$heading = apply_filters( 'scriptlesssocialsharing_heading', $heading );
		if ( ! $heading ) {
			return '';
		}
		$heading_element = apply_filters( 'scriptlesssocialsharing_heading_element', 'h3' );

		return sprintf( '<%1$s class="scriptlesssocialsharing__heading">%2$s</%1$s>', $heading_element, $heading );
	}
}
