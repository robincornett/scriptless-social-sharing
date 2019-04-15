<?php

/**
 * Class ScriptlessSocialSharingEnqueue
 */
class ScriptlessSocialSharingEnqueue {

	/**
	 * The plugin setting.
	 * @var $setting array
	 */
	protected $setting;

	/**
	 * The buttons available for output.
	 * @var
	 */
	protected $buttons;

	/**
	 * @var string current plugin version
	 */
	protected $version = SCRIPTLESSOCIALSHARING_VERSION;

	/**
	 * ScriptlessSocialSharingEnqueue constructor.
	 *
	 * @param $setting array
	 * @param $buttons array
	 */
	public function __construct( $setting, $buttons ) {
		$this->setting = $setting;
		$this->buttons = $buttons;
	}

	/**
	 * Enqueue our styles.
	 */
	public function load_styles() {
		$this->load_plugin_style();
		$this->load_fontawesome_font();
		$this->load_fontawesome_icons();
	}

	/**
	 * If it's enabled, load the plugin styles.
	 * @since 2.4.0
	 */
	protected function load_plugin_style() {
		if ( ! $this->setting['styles']['plugin'] ) {
			return;
		}
		$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-style.css' );
		if ( $css_file ) {
			wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), $this->version, 'all' );
			$this->add_inline_style();
		}
	}

	/**
	 * Add the inline stylesheet to the plugin stylesheet.
	 */
	protected function add_inline_style() {
		wp_add_inline_style( 'scriptlesssocialsharing', sanitize_text_field( $this->get_inline_style() ) );
	}

	/**
	 * If it's enabled, enqueue Font Awesome 4.7.0
	 * @since 2.4.0
	 */
	protected function load_fontawesome_font() {
		if ( ! $this->setting['styles']['font'] ) {
			return;
		}
		$fontawesome = apply_filters( 'scriptlesssocialsharing_use_fontawesome', true );
		if ( $fontawesome ) {
			$fa_version = '4.7.0';
			wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/' . $fa_version . '/css/font-awesome.min.css', array(), $fa_version );
		}
	}

	/**
	 * If SVG is not enabled and Font Awesome is, load the Font Awesome CSS.
	 * @since 2.4.0
	 */
	protected function load_fontawesome_icons() {
		if ( ! empty( $this->setting['svg'] ) ) {
			return;
		}
		if ( empty( $this->setting['styles']['font_css'] ) ) {
			return;
		}
		$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-fontawesome.css' );
		if ( $fa_file ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fa-icons', esc_url( $fa_file ), array(), $this->version, 'screen' );
		}
	}

	/**
	 * Build the inline style.
	 *
	 * @return string
	 */
	public function get_inline_style() {
		$table_width   = 'auto' === $this->setting['table_width'] ? 'auto' : '100%';
		$inline_style  = sprintf( '.scriptlesssocialsharing-buttons { width: %s }', $table_width );
		$count         = count( $this->buttons ) > 0 ? count( $this->buttons ) : 1;
		$button_width  = 100 / $count . '%;';
		$inline_style .= sprintf( '.scriptlesssocialsharing-buttons a.button { padding: %spx; width: %s }', (int) $this->setting['button_padding'], esc_attr( $button_width ) );
		if ( $this->setting['button_style'] ) {
			$inline_style .= '@media only screen and (min-width: 800px) { .scriptlesssocialsharing-buttons .sss-name { position: relative; height: auto; width: auto; } }';
		}
		foreach ( $this->buttons as $button ) {
			if ( isset( $button['icon'] ) && $this->setting['styles']['font_css'] ) {
				$inline_style .= sprintf( '.scriptlesssocialsharing-buttons .%s:before { content: "\%s"; }', $button['name'], $button['icon'] );
			}
			if ( isset( $button['color'] ) && isset( $button['name'] ) ) {
				$inline_style .= $this->get_button_color( $button );
			}
		}

		return apply_filters( 'scriptlesssocialsharing_inline_style', $inline_style );
	}

	/**
	 * Get the button color with an RGBA value.
	 *
	 * @param $button
	 *
	 * @return string
	 */
	protected function get_button_color( $button ) {
		$rgb = $this->hex2rgb( $button['color'] );
		if ( ! $rgb ) {
			return '';
		}

		return sprintf(
			'.scriptlesssocialsharing-buttons .button.%3$s{ background-color:%1$s;background-color:rgba(%2$s,.8); } .scriptlesssocialsharing-buttons .button.%3$s:hover{ background-color:%1$s }',
			$button['color'],
			$rgb,
			$button['name']
		);
	}

	/**
	 * Converts a hex color to rgb values, separated by commas
	 * @param $hex
	 *
	 * @return bool|string false if input is not a 6 digit hex color; string if converted
	 * @since 2.0.0
	 */
	protected function hex2rgb( $hex ) {
		// Remove "#" if it was added
		$hex = trim( $hex, '#' );

		// If the color is three characters, convert it to six.
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) ) {
			return false;
		}
		$r   = hexdec( substr( $hex, 0, 2 ) );
		$g   = hexdec( substr( $hex, 2, 2 ) );
		$b   = hexdec( substr( $hex, 4, 2 ) );
		$rgb = array( $r, $g, $b );

		return implode( ',', $rgb ); // returns the rgb values separated by commas
	}
}
