<?php

/**
 * Class ScriptlessSocialSharingOutputSVG
 * @since 2.4.0
 */
class ScriptlessSocialSharingOutputSVG {

	/**
	 * Whether the SVG file(s) have been loaded or not.
	 * @var bool
	 */
	private $loaded = false;

	/**
	 * The class instance.
	 * @var $instance
	 */
	private static $instance;

	/**
	 * @return \ScriptlessSocialSharingOutputSVG
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SixTenPressSVG ) ) {
			self::$instance = new ScriptlessSocialSharingOutputSVG();

			add_action( 'init', array( self::$instance, 'maybe_add_svg' ) );
		}

		return self::$instance;
	}

	/**
	 * If SVG icons are enabled, add them to the footer and allowed HTML.
	 * @since 3.0.0
	 */
	public function maybe_add_svg() {
		$setting = scriptlesssocialsharing_get_setting();
		if ( 'svg' !== $setting['icons'] ) {
			return;
		}

		add_action( 'wp_footer', array( self::$instance, 'load_svg' ) );
		add_action( 'admin_footer-post.php', array( self::$instance, 'load_svg' ) );
		add_filter( 'wp_kses_allowed_html', array( self::$instance, 'filter_allowed_html' ), 10, 2 );
	}

	/**
	 * Add SVG definitions to the footer.
	 *
	 * @since 2.4.0
	 */
	public function load_svg() {
		if ( $this->loaded ) {
			return;
		}
		$svg = $this->get_svg();
		if ( ! $svg['styles'] ) {
			return;
		}
		foreach ( $svg['styles'] as $style ) {
			$file = trailingslashit( $svg['path'] ) . $style . '.svg';
			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
		$this->loaded = true;
	}

	/**
	 * Add SVG to allowed KSES output.
	 * @since 2.4.0
	 *
	 * @param $allowed
	 * @param $context
	 * @return mixed
	 */
	public function filter_allowed_html( $allowed, $context ) {

		if ( 'post' === $context ) {
			$allowed['svg'] = array(
				'class' => true,
			);
			$allowed['use'] = array(
				'xlink:href' => true,
			);
		}

		return $allowed;
	}

	/**
	 * Return SVG markup.
	 * @since 2.4.0
	 *
	 * @param string $icon
	 * @param array  $args    {
	 *                        Optional parameters needed to display an SVG.
	 *
	 * @return string SVG markup.
	 */
	public function svg( $icon, $args = array() ) {
		if ( ! $icon ) {
			return false;
		}
		$icon            = $this->replace_icon_name( $icon );
		$defaults        = array(
			'title' => '',
			'desc'  => '',
		);
		$args            = wp_parse_args( $args, $defaults );
		$aria_hidden     = ' aria-hidden="true"';
		$aria_labelledby = '';
		$title           = '';
		$xlink           = apply_filters( 'scriptlesssocialsharing_svg_xlink', "#{$icon}" );

		if ( $args['title'] ) {
			$aria_hidden     = '';
			$unique_id       = uniqid();
			$aria_labelledby = ' aria-labelledby="title-' . $unique_id . '"';
			$title           = sprintf(
				'<title id="title-%s">%s</title>',
				$unique_id,
				esc_html( $args['title'] )
			);
			if ( $args['desc'] ) {
				$aria_labelledby = ' aria-labelledby="title-' . $unique_id . ' desc-' . $unique_id . '"';
				$title          .= sprintf(
					'<desc id="desc-%s">%s</desc>',
					$unique_id,
					esc_html( $args['desc'] )
				);
			}
		}

		/**
		 * Add a filter on the SVG icon output (all icons).
		 *
		 * @param string $output
		 * @param string $icon
		 * @param array  $args
		 * @param string $aria_hidden
		 * @param string $aria_labelledby
		 * @param string $title
		 * @param string $xlink
		 */
		return apply_filters(
			'scriptlesssocialsharing_svg_output',
			sprintf(
				'<svg class="scriptlesssocialsharing__icon %1$s" role="img"%2$s%3$s>%4$s <use href="#%1$s" xlink:href="%5$s"></use> </svg>',
				esc_attr( $icon ),
				$aria_hidden,
				$aria_labelledby,
				$title,
				$xlink
			),
			$icon,
			$args,
			$aria_hidden,
			$aria_labelledby,
			$title,
			$xlink
		);
	}

	/**
	 * Since some icons have different names than their networks, check and replace if needed.
	 * @since 2.4.0
	 *
	 * @param $icon
	 * @return string
	 */
	private function replace_icon_name( $icon ) {

		$icons = array(
			'pocket'   => 'get-pocket',
			'email'    => 'envelope',
			'reddit'   => 'reddit-alien',
			'telegram' => 'telegram-plane',
		);

		if ( array_key_exists( $icon, $icons ) ) {
			$icon = $icons[ $icon ];
		}

		return apply_filters( "scriptlesssocialsharing_svg_icon_{$icon}", $icon );
	}

	/**
	 * Get the correct path for the SVG icons.
	 * @since 2.4.0
	 *
	 * @return mixed|array
	 */
	protected function get_svg() {

		return apply_filters(
			'scriptlesssocialsharing_svg',
			array(
				'styles' => array( 'brands' ),
				'path'   => plugin_dir_path( dirname( __FILE__ ) ) . 'svg',
			)
		);
	}

	/**
	 * Get the list of brand icons in Font Awesome.
	 * @return array
	 */
	protected function brands() {
		return include plugin_dir_path( dirname( __FILE__ ) ) . 'svg/brands.php';
	}
}

/**
 * Instantiate the SVG class.
 * @return \ScriptlessSocialSharingOutputSVG
 * @since 3.0.0
 */
function scriptlesssocialsharing_svg() {
	return ScriptlessSocialSharingOutputSVG::instance();
}

scriptlesssocialsharing_svg();
