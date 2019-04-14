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
	 * The array of buttons.
	 *
	 * @var array
	 */
	protected $buttons;

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
		$post_types  = scriptlesssocialsharing_post_types();
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true );
		if ( ! is_singular( $post_types ) || is_feed() || $is_disabled ) {
			$cando = false;
		}
		global $post;
		if ( is_singular() && is_object( $post ) && has_shortcode( $post->post_content, 'scriptless' ) ) {
			$cando = true;
		}

		return apply_filters( 'scriptlesssocialsharing_can_do_buttons', $cando );
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
		$target = 'email' === $button['name'] ? '' : ' target="_blank"';

		return apply_filters(
			'scriptlesssocialsharing_link_markup',
			sprintf(
				'<a class="button %s"%s href="%s" rel="noopener" %s>%s<span class="sss-name">%s</span></a>',
				esc_attr( $button['name'] ),
				$target,
				esc_url( $button['url'] ),
				$button['data'],
				$this->get_svg( $button['name'] ),
				$button['label']
			),
			$button
		);
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
		if ( empty( $setting['svg'] ) ) {
			return '';
		}

		include_once 'class-scriptlesssocialsharing-output-svg.php';
		$svg = new ScriptlessSocialSharingOutputSVG();

		return $svg->get_svg_markup( $icon );
	}

	/**
	 * Get the permalink to be shared via the button.
	 *
	 * @param  string $button_name The name of the button, e.g. 'twitter', 'facebook'.
	 *
	 * @return string The URL to be shared.
	 */
	protected function get_permalink( $button_name ) {
		$attributes = $this->get_attributes();

		return rawurlencode(
			apply_filters( 'scriptlesssocialsharing_get_permalink',
				$attributes['permalink'],
				$button_name,
				$attributes
			)
		);
	}

	/**
	 * Create the default buttons
	 * @return array array of buttons/attributes
	 */
	protected function get_available_buttons() {
		if ( isset( $this->buttons ) && is_singular() ) {
			return $this->buttons;
		}
		$buttons     = $this->get_all_buttons();
		$setting     = $this->get_setting();
		$set_buttons = $setting['buttons'];
		if ( $set_buttons ) {
			foreach ( $buttons as $key => $value ) {
				if ( ! isset( $set_buttons[ $key ] ) || ! $set_buttons[ $key ] ) {
					unset( $buttons[ $key ] );
				}
			}
		}
		$attributes = $this->get_attributes();
		if ( ! $attributes['image'] && ! $attributes['pinterest'] ) {
			unset( $buttons['pinterest'] );
		}

		$this->buttons = $buttons;

		/**
		 * Note: scriptlesssocialsharing_buttons filter should be used instead of this
		 * filter, due to potential errors with a button being in this array, but not
		 * actually selected for output.
		 */
		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
	}

	/**
	 * Get an array of all possible sharing buttons.
	 * @since 2.2.0
	 * @return mixed
	 */
	protected function get_all_buttons() {
		$attributes = $this->get_attributes();
		$buttons    = $this->get_buttons_in_order();
		foreach ( $buttons as $button => $value ) {
			$url  = '';
			$file = plugin_dir_path( dirname( __FILE__ ) ) . "buttons/class-scriptlesssocialsharing-button-{$button}.php";
			if ( file_exists( $file ) ) {
				include_once $file;
			}
			$proper_name = 'ScriptlessSocialSharingButton' . ucfirst( $button );
			if ( class_exists( $proper_name ) && is_callable( $proper_name, 'get_url' ) ) {
				$class = new $proper_name();
				$url   = $class->get_url( $attributes );
			}

			/**
			 * Create a filter to build custom URLs for each network.
			 * @since 2.0.0
			 */
			$buttons[ $button ]['url'] = apply_filters( "scriptlesssocialsharing_{$button}_url", $url, $button, $attributes );

			/**
			 * Create a filter to add data attributes to social URLs.
			 * @since 2.0.0
			 */
			$buttons[ $button ]['data'] = apply_filters( "scriptlesssocialsharing_{$button}_data", '', $button, $attributes );
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
	 * create URL attributes for buttons
	 * @return array attributes
	 */
	protected function attributes() {
		$setting = $this->get_setting();

		return array(
			'title'     => $this->title(),
			'permalink' => get_the_permalink(),
			'home'      => home_url(),
			'image'     => $setting['buttons']['pinterest'] ? $this->featured_image() : '',
			'pinterest' => $this->pinterest_image(),
			'post_id'   => get_the_ID(),
		);
	}

	/**
	 * Get the array of attributes for sharing buttons.
	 * @return array
	 */
	protected function get_attributes() {
		if ( isset( $this->attributes ) && is_singular() ) {
			return $this->attributes;
		}
		$this->attributes = $this->attributes();

		return $this->attributes;
	}

	/**
	 * Set the post title for sharing. Decodes HTML character entities,
	 * then encodes for the URL.
	 * @return string
	 */
	protected function title() {
		$title = html_entity_decode( the_title_attribute( 'echo=0' ) );

		return rawurlencode( apply_filters( 'scriptlesssocialsharing_posttitle', $title ) );
	}

	/**
	 * retrieve the featured image
	 * @return string if there is a featured image, return the ID
	 */
	protected function featured_image() {
		return has_post_thumbnail() ? get_post_thumbnail_id() : $this->get_fallback_image();
	}

	/**
	 * If there is no featured image, use the first image attached to the post/page as the fallback.
	 * @return bool
	 */
	protected function get_fallback_image() {
		$image_ids = array_keys(
			get_children(
				array(
					'post_parent'    => get_the_ID(),
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'numberposts'    => 1,
				)
			)
		);

		if ( isset( $image_ids[0] ) ) {
			return $image_ids[0];
		}

		return false;
	}

	/**
	 * If a pinterest specific image is set, get the ID.
	 * @return string
	 */
	protected function pinterest_image() {
		return get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true );
	}

	/**
	 * Convert an image ID into a URL string.
	 *
	 * @param $id
	 *
	 * @return string
	 */
	protected function get_image_url( $id ) {
		$source = wp_get_attachment_image_src( $id, 'large', false );

		return apply_filters( 'scriptlesssocialsharing_image_url', isset( $source[0] ) ? $source[0] : '' );
	}

	/**
	 * get the post excerpt
	 *
	 * @param string $description
	 *
	 * @return string excerpt formatted for URL
	 */
	protected function description( $description = '' ) {
		if ( has_excerpt() ) {
			$description = get_the_excerpt();
		}

		return apply_filters( 'scriptlesssocialsharing_description', $description );
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

	/**
	 * replace spaces in a string with %20 for URLs
	 *
	 * @param  string $string passed through from another source
	 *
	 * @return string         same string, just %20 instead of spaces
	 */
	protected function replace( $string ) {
		return htmlentities( $string );
	}
}
