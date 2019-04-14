<?php

class ScriptlessSocialSharingOutputSVG {

	private $loaded = false;

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'load_svg' ) );
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
	 * Return SVG markup.
	 *
	 * @param string $icon
	 * @param array  $args    {
	 *                        Optional parameters needed to display an SVG.
	 *
	 * @return string SVG markup.
	 */
	public function get_svg_markup( $icon, $args = array() ) {
		if ( ! $icon ) {
			return false;
		}
		$defaults        = array(
			'title'    => '',
			'desc'     => '',
			'fallback' => false,
		);
		$args            = wp_parse_args( $args, $defaults );
		$aria_hidden     = ' aria-hidden="true"';
		$aria_labelledby = '';
		$title           = '';
		$fallback        = '';
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

		if ( $args['fallback'] ) {
			$fallback = '<span class="svg-fallback icon-' . esc_attr( $icon ) . '"></span>';
		}

		return apply_filters(
			'scriptlesssocialsharing_svg_icon',
			sprintf(
				'<svg class="icon scriptless-icon %1$s" role="img"%2$s%3$s>%4$s <use href="#%1$s" xlink:href="%6$s"></use> %5$s</svg>',
				esc_attr( $icon ),
				$aria_hidden,
				$aria_labelledby,
				$title,
				$fallback,
				$xlink
			),
			$icon,
			$args,
			$aria_hidden,
			$aria_labelledby,
			$title,
			$fallback,
			$xlink
		);
	}

	/**
	 * Get the correct path for the SVG icons.
	 * @return mixed|array
	 */
	protected function get_svg() {

		return apply_filters(
			'scriptlesssocialsharing_svg',
			array(
				'styles' => array( 'brands' ),
				'path'   => plugin_dir_path( __FILE__ ) . 'svg',
			)
		);
	}

	/**
	 * Get the list of brand icons in Font Awesome.
	 * @return array
	 */
	protected function brands() {
		return include plugin_dir_path( __FILE__ ) . 'svg/brands.php';
	}
}
