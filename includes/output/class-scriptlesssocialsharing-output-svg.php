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
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof ScriptlessSocialSharingOutputSVG ) ) {
			self::$instance = new ScriptlessSocialSharingOutputSVG();
		}
		add_filter( 'wp_kses_allowed_html', array( self::$instance, 'filter_allowed_html' ), 10, 2 );

		return self::$instance;
	}

	/**
	 * Gets the SVG icon directly from an SVG file (new) or sprite file.
	 *
	 * @since 3.2.0
	 * @param string $icon
	 * @param array $args
	 * @return string
	 */
	public function svg( $icon, $args = array() ) {
		$contents = $this->get_icon_file_contents( $icon );
		if ( $contents ) {
			return $this->update_svg(
				$contents,
				$this->get_icon_args( $icon, $args )
			);
		}

		add_action( 'wp_footer', array( $this, 'load_svg' ) );
		add_action( 'admin_footer-post.php', array( $this, 'load_svg' ) );

		return $this->get_svg_from_sprite( $icon, $args );
	}

	/**
	 * Get the icon args, merged with defaults.
	 *
	 * @since 3.2.0
	 * @param array $args
	 * @return array
	 */
	private function get_icon_args( $icon, $args ) {
		$defaults = array(
			'class' => "scriptlesssocialsharing__icon {$icon}",
		);

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Get the icon file to include.
	 *
	 * @since 3.2.0
	 * @param string $icon
	 * @return string
	 */
	private function get_icon_file_contents( $icon ) {
		$icon         = $this->replace_icon_name( $icon );
		$located_icon = $this->locate_icon( $icon );

		return file_exists( $located_icon ) ? file_get_contents( $located_icon ) : false;
	}

	/**
	 * Get the path for the icons.
	 * To use custom icons from your theme, add two folders to the theme's /images directory:
	 * /sprites/icons.svg (containing custom icons)
	 * /svg  with all custom icon svg files
	 * The filter should return a directory which contains the following directory structure:
	 * /sprites/ which contains sprite files
	 * /svg/ which contains subdirectories for the chosen weight(s) and brands
	 *
	 * @since 3.2.0
	 * @return array
	 */
	public function get_icon_paths() {
		return array(
			trailingslashit( get_stylesheet_directory() . '/assets' ),
			trailingslashit( get_stylesheet_directory() . '/images' ),
			trailingslashit( plugin_dir_path( dirname( __FILE__ ) ) ),
		);
	}

	/**
	 * Locate a specific icon.
	 *
	 * @since 3.2.0
	 * @param string $icon
	 * @return string|boolean
	 */
	private function locate_icon( $icon ) {
		$located   = false;
		$locations = $this->get_icon_paths();
		foreach ( $locations as $location ) {
			$path = trailingslashit( "{$location}svg" );
			if ( file_exists( "{$path}{$icon}.svg" ) ) {
				$located = "{$path}{$icon}.svg";
				break;
			}
		}

		return $located;
	}

	/**
	 * Update the SVG icon with class, style, a11y attributes.
	 *
	 * @since 3.2.0
	 * @param string $svg  The SVG.
	 * @param array  $args
	 * @return string
	 */
	private function update_svg( $svg, $args ) {
		$html = '';
		if ( ! $svg ) {
			return $html;
		}
		$dom = $this->get_document( $svg );

		foreach ( $dom->getElementsByTagName( 'svg' ) as $item ) {
			foreach ( $this->svg_attributes( $args ) as $key => $value ) {
				if ( $value ) {
					$item->setAttribute( $key, $value );
				}
			}

			return $dom->saveHTML();
		}

		return $html;
	}

	/**
	 * Get all of the attributes to be added to the SVG.
	 *
	 * @since 3.2.0
	 * @param array $args
	 * @return array
	 */
	private function svg_attributes( $args ) {
		return array(
			'class'       => $args['class'],
			'fill'        => 'currentcolor',
			'height'      => '1em',
			'width'       => '1em',
			'aria-hidden' => 'true',
			'focusable'   => 'false',
			'role'        => 'img',
		);
	}

	/**
	 * Get the SVG content as an object.
	 *
	 * @since 3.2.0
	 * @param string $svg The SVG.
	 * @return object
	 */
	private function get_document( $svg ) {
		$doc = new DOMDocument();

		libxml_use_internal_errors( true ); // turn off errors for HTML5
		$currentencoding = mb_internal_encoding();
		$content         = mb_convert_encoding( $svg, 'HTML-ENTITIES', $currentencoding );
		if ( defined( 'LIBXML_HTML_NOIMPLIED' ) && defined( 'LIBXML_HTML_NODEFDTD' ) ) {
			$doc->LoadHTML( $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		} else {
			$doc->LoadHTML( $content );
		}
		libxml_clear_errors(); // now that it's loaded, go ahead

		return $doc;
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
		if ( empty( $svg['styles'] ) ) {
			return;
		}
		foreach ( (array) $svg['styles'] as $style ) {
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
			$allowed['svg']  = array(
				'class'           => true,
				'role'            => true,
				'aria-hidden'     => true,
				'aria-labelledby' => true,
				'style'           => true,
				'fill'            => true,
				'height'          => true,
				'width'           => true,
				'focusable'       => true,
				'xmlns'           => true,
				'viewbox'         => true,
			);
			$allowed['use']  = array(
				'xlink:href' => true,
			);
			$allowed['path'] = array(
				'd' => true,
			);
		}

		return $allowed;
	}

	/**
	 * Returns SVG markup. This is the old/original way of retrieving the SVG icons from
	 * a sprite file loaded in the footer. The new way (individual SVGs) is preferred.
	 *
	 * Originally named svg(); in 3.2.0 updated to get_svg_from_sprite() and used as a fallback.
	 *
	 * @since 2.4.0
	 * @since 3.2.0
	 *
	 * @param string $icon
	 * @param array  $args    {
	 *                        Optional parameters needed to display an SVG.
	 *
	 * @return string SVG markup.
	 */
	public function get_svg_from_sprite( $icon, $args = array() ) {
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

		$icons = $this->get_alternate_icons_list();
		if ( array_key_exists( $icon, $icons ) ) {
			$icon = $icons[ $icon ];
		}

		return apply_filters( "scriptlesssocialsharing_svg_icon_{$icon}", $icon );
	}

	/**
	 * Build the list of alternate SVG icons.
	 * @return mixed|void|null
	 * @since 3.0.0
	 */
	private function get_alternate_icons_list() {
		return apply_filters(
			'scriptlesssocialsharing_svg_icons',
			array(
				'pocket'   => 'get-pocket',
				'email'    => 'envelope',
				'reddit'   => 'reddit-alien',
				'telegram' => 'telegram-plane',
			)
		);
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
				'styles' => 'brands',
				'path'   => plugin_dir_path( dirname( __FILE__ ) ) . 'svg',
			)
		);
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
