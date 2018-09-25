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
	protected $version = '2.2.0';

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

		$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-style.css' );
		if ( $css_file && $this->setting['styles']['plugin'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), $this->version, 'all' );
			$this->add_inline_style();
		}
		$fontawesome = apply_filters( 'scriptlesssocialsharing_use_fontawesome', true );
		if ( $fontawesome && $this->setting['styles']['font'] ) {
			$fa_version = '4.7.0';
			wp_enqueue_style( 'scriptlesssocialsharing-fontawesome', '//maxcdn.bootstrapcdn.com/font-awesome/' . $fa_version . '/css/font-awesome.min.css', array(), $fa_version );
		}

		$fa_file = apply_filters( 'scriptlesssocialsharing_fontawesome', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-fontawesome.css' );
		if ( $fa_file && $this->setting['styles']['font_css'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing-fa-icons', esc_url( $fa_file ), array(), $this->version, 'screen' );
		}
	}

	/**
	 * Add the inline stylesheet to the plugin stylesheet.
	 */
	protected function add_inline_style() {
		wp_add_inline_style( 'scriptlesssocialsharing', sanitize_text_field( $this->get_inline_style() ) );
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
			if ( isset( $button['icon'] ) ) {
				$inline_style .= sprintf( '.scriptlesssocialsharing-buttons .%s:before { content: "\%s"; }', $button['name'], $button['icon'] );
			}
			if ( isset( $button['color'] ) && isset( $button['name'] ) ) {
				$rgb           = $this->hex2rgb( $button['color'] );
				$rgba          = $rgb ? sprintf( ' background-color:rgba(%s,.8);', $rgb ) : '';
				$inline_style .= sprintf( '.scriptlesssocialsharing-buttons .button.%3$s{ background-color:%1$s;%2$s } .scriptlesssocialsharing-buttons .button.%3$s:hover{ background-color:%1$s }', $button['color'], $rgba, $button['name'] );
			}
		}

		return apply_filters( 'scriptlesssocialsharing_inline_style', $inline_style );
	}

	/**
	 * Converts a hex color to rgb values, separated by commas
	 * @param $hex
	 *
	 * @return bool|string false if input is not a 6 digit hex color; string if converted
	 * @since 2.0.0
	 */
	protected function hex2rgb( $hex ) {
		$hex = '#' === $hex[0] ? substr( $hex, 1 ) : $hex;
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
