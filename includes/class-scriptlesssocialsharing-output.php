<?php

/**
 * Class ScriptlessSocialSharingOutput
 * Plugin class for buttons output.
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingOutput {

	/**
	 * @var string current plugin version
	 */
	protected $version = '1.5.2';

	/**
	 * @var $setting ScriptlessSocialSharingSettings->get_setting
	 */
	protected $setting;

	/**
	 * Function to decide whether buttons can be output or not
	 * @param  boolean $cando default true
	 * @return boolean         false if not a singular post (can be modified for other content types)
	 */
	protected function can_do_buttons( $cando = true ) {
		if ( ! is_main_query() ) {
			$cando = false;
		}
		$post_types  = scriptlesssocialsharing_post_types();
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
		if ( ! is_singular( $post_types ) || is_feed() || $is_disabled ) {
			$cando = false;
		}
		global $post;
		if ( has_shortcode( $post->post_content, 'scriptless' ) ) {
			$cando = true;
		}
		return apply_filters( 'scriptlesssocialsharing_can_do_buttons', $cando );
	}

	/**
	 * Enqueue CSS files
	 */
	public function load_styles() {
		$this->setting = scriptlesssocialsharing_get_setting();
		if ( false === $this->can_do_buttons() ) {
			return;
		}

		$css_file = apply_filters( 'scriptlesssocialsharing_default_css', plugin_dir_url( __FILE__ ) . 'css/scriptlesssocialsharing-style.css' );
		if ( $css_file && $this->setting['styles']['plugin'] ) {
			wp_enqueue_style( 'scriptlesssocialsharing', esc_url( $css_file ), array(), $this->version, 'screen' );
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
		$buttons       = $this->make_buttons();
		$table_width   = 'auto' === $this->setting['table_width'] ? 'auto' : '100%';
		$inline_style  = sprintf( '.scriptlesssocialsharing-buttons { width: %s }', $table_width );
		$button_width  = 100 / count( $buttons ) . '%;';
		$inline_style .= sprintf( '.scriptlesssocialsharing-buttons a.button { padding: %spx; width: %s }', (int) $this->setting['button_padding'], esc_attr( $button_width ) );
		if ( $this->setting['button_style'] ) {
			$inline_style .= '@media only screen and (min-width: 800px) { .scriptlesssocialsharing-buttons .sss-name { position: relative; height: auto; width: auto; } }';
		}
		foreach ( $buttons as $button ) {
			if ( isset( $button['icon'] ) ) {
				$inline_style .= sprintf( '.scriptlesssocialsharing-buttons .%s:before { content: "\%s"; }', $button['name'], $button['icon'] );
			}
			if ( isset( $button['color'] ) && isset( $button['name'] ) ) {
				$rgb  = $this->hex2rgb( $button['color'] );
				$rgba = $rgb ? sprintf( ' background-color:rgba(%s,.8);', $rgb ) : '';
				$inline_style .= sprintf( '.scriptlesssocialsharing-buttons .button.%3$s{ background-color:%1$s;%2$s } .scriptlesssocialsharing-buttons .button.%3$s:hover{ background-color:%1$s }', $button['color'], $rgba, $button['name'] );
			}
		}
		/**
		 * Allows user to filter/modify the inline style.
		 */
		$inline_style = apply_filters( 'scriptlesssocialsharing_inline_style', $inline_style );
		wp_add_inline_style( 'scriptlesssocialsharing', sanitize_text_field( $inline_style ) );
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

	/**
	 * Return buttons
	 *
	 * @param string $output
	 * @param bool $heading set the bool to false to output buttons with no heading
	 *
	 * @return string
	 */
	public function do_buttons( $output = '', $heading = true ) {

		if ( ! $this->can_do_buttons() ) {
			return $output;
		}

		$buttons = $this->make_buttons();

		if ( ! $buttons ) {
			return $output;
		}

		$output = '<div class="scriptlesssocialsharing">';
		if ( $heading ) {
			$output .= $this->heading( $this->setting['heading'] );
		}
		$output .= '<div class="scriptlesssocialsharing-buttons">';
		foreach ( $buttons as $button ) {
			$output .= sprintf( '<a class="button %s" target="_blank" href="%s" %s><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), esc_url( $button['url'] ), $button['data'], $button['label'] );
		}
		$output .= '</div>';
		$output .= '</div>';

		add_filter( 'wp_kses_allowed_html', array( $this, 'filter_allowed_html' ), 10, 2 );
		return wp_kses_post( $output );
	}

	/**
	 * Create a shortcode to insert sharing buttons within the post content.
	 * @param $atts
	 *
	 * @return string
	 */
	public function shortcode( $atts ) {
		$defaults = array(
			'before'       => '<div class="scriptlesssocialsharing">',
			'after'        => '</div>',
			'inner_before' => '<div class="scriptlesssocialsharing-buttons">',
			'inner_after'  => '</div>',
			'heading'      => $this->setting['heading'],
		);
		$atts    = shortcode_atts( $defaults, $atts, 'scriptless' );
		$buttons = $this->make_buttons();
		$output  = $atts['before'];
		$output .= $this->heading( $atts['heading'] );
		$output .= $atts['inner_before'];
		foreach ( $buttons as $button ) {
			$output .= sprintf( '<a class="button %s" target="_blank" href="%s" %s><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), esc_url( $button['url'] ), $button['data'], $button['label'] );
		}
		$output .= $atts['inner_after'];
		$output .= $atts['after'];
		return $output;
	}

	/**
	 * Get the permalink to be shared via the button.
	 *
	 * @param  string $button_name The name of the button, e.g. 'twitter', 'facebook'.
	 * @return string The URL to be shared.
	 */
	protected function get_permalink( string $button_name ) {
		$attributes = $this->attributes();
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
	protected function make_buttons() {

		$attributes     = $this->attributes();
		$settings_class = new ScriptlessSocialSharingSettings();
		$buttons        = $settings_class->get_networks();
		add_filter( 'scriptlesssocialsharing_pinterest_data', array( $this, 'add_pinterest_data' ) );
		foreach ( $buttons as $button => $value ) {
			$method = "get_{$button}_url";
			$url    = method_exists( $this, $method ) ? $this->$method( $attributes ) : '';

			/**
			 * Create a filter to build custom URLs for each network.
			 * @since x.y.z
			 */
			$buttons[ $button ]['url']  = apply_filters( "scriptlesssocialsharing_{$button}_url", $url, $button, $attributes );

			/**
			 * Create a filter to add data attributes to social URLs.
			 * @since x.y.z
			 */
			$buttons[ $button ]['data'] = apply_filters( "scriptlesssocialsharing_{$button}_data", '', $button, $attributes );
		}

		$buttons = apply_filters( 'scriptlesssocialsharing_buttons', $buttons, $attributes );

		$set_buttons = $this->setting['buttons'];
		if ( $set_buttons ) {
			foreach ( $buttons as $key => $value ) {
				if ( ! isset( $set_buttons[ $key ] ) || ! $set_buttons[ $key ] ) {
					unset( $buttons[ $key ] );
				}
			}
		}
		if ( ! $attributes['image'] && ! $attributes['pinterest'] ) {
			unset( $buttons['pinterest'] );
		}

		/**
		 * Note: scriptlesssocialsharing_buttons filter should be used instead of this
		 * filter, due to potential errors with a button being in this array, but not
		 * actually selected for output.
		 */
		return apply_filters( 'scriptlesssocialsharing_default_buttons', $buttons, $attributes );
	}

	/**
	 * Get the URL for Twitter.
	 * @param $attributes array
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_twitter_url( $attributes ) {
		$yoast         = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		$twitter_title = $yoast ? $yoast : $attributes['title'];

		return add_query_arg(
			array(
				'text'    => $twitter_title,
				'url'     => $this->get_permalink( 'twitter' ),
				'via'     => $this->twitter_handle(),
				'related' => $this->twitter_handle(),
			),
			'https://twitter.com/intent/tweet'
		);
	}

	/**
	 * Get the URL for Facebook.
	 * @param $attributes array
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_facebook_url( $attributes ) {
		return add_query_arg(
			'u',
			$this->get_permalink( 'facebook' ),
			'http://www.facebook.com/sharer/sharer.php'
		);
	}

	/**
	 * Get the URL for Google+.
	 * @param $attributes array
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_google_url( $attributes ) {
		return add_query_arg(
			'url',
			$this->get_permalink( 'google' ),
			'https://plus.google.com/share'
		);
	}

	/**
	 * Get the URL for Pinterest.
	 * @param $attributes
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_pinterest_url( $attributes ) {
		$pinterest_url = $attributes['pinterest'] ? $attributes['pinterest'] : $attributes['image'];
		$pinterest_img = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true );
		$pinterest_alt = get_post_meta( $pinterest_img, '_wp_attachment_image_alt', true );
		$pin_title     = $pinterest_alt ? $pinterest_alt : $attributes['title'];
		return add_query_arg(
			array(
				'url'         => $this->get_permalink( 'pinterest' ),
				'description' => $pin_title,
				'media'       => esc_url( $pinterest_url ),
			),
			'http://pinterest.com/pin/create/button/'
		);
	}

	/**
	 * Add Pinterest data pin attributes to the URL markup.
	 * @return string
	 * @since x.y.z
	 */
	public function add_pinterest_data() {
		return 'data-pin-no-hover="true" data-pin-custom="true" data-pin-do="skip"';
	}

	/**
	 * Get the Linkedin URL.
	 * @param $attributes array
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_linkedin_url( $attributes ) {
		return add_query_arg(
			array(
				'mini'    => true,
				'url'     => $this->get_permalink( 'linkedin' ),
				'title'   => $attributes['title'],
				'summary' => $this->description(),
				'source'  => $attributes['home'],
			),
			'http://www.linkedin.com/shareArticle'
		);
	}

	/**
	 * Get the email URL.
	 * @param $attributes array
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_email_url( $attributes ) {
		return add_query_arg(
			array(
				'body'    => $attributes['email_body'] . ' ' . $this->get_permalink( 'email' ),
				'subject' => $attributes['email_subject'] . ' ' . $attributes['title'],
			),
			'mailto:'
		);
	}

	/**
	 * Get the Reddit URL.
	 * @param $attributes
	 *
	 * @return string
	 * @since x.y.z
	 */
	protected function get_reddit_url( $attributes ) {
		return add_query_arg(
			'url',
			$this->get_permalink( 'reddit' ),
			'https://www.reddit.com/submit'
		);
	}

	/**
	 * create URL attributes for buttons
	 * @return array attributes
	 */
	protected function attributes() {
		return array(
			'title'         => $this->title(),
			'permalink'     => get_the_permalink(),
			'home'          => home_url(),
			'image'         => $this->setting['buttons']['pinterest'] ? $this->featured_image() : '',
			'email_body'    => $this->email_body(),
			'email_subject' => $this->email_subject(),
			'pinterest'     => $this->pinterest_image(),
			'post_id'       => get_the_ID(),
		);
	}

	/**
	 * set the post title for sharing
	 * @return string uses Yoast title if it exists, post title otherwise
	 */
	protected function title() {
		return apply_filters( 'scriptlesssocialsharing_posttitle', the_title_attribute( 'echo=0' ) );
	}

	/**
	 * retrieve the featured image
	 * @return string if there is a featured image, return the URL
	 */
	protected function featured_image() {
		$featured_image = has_post_thumbnail() ? get_post_thumbnail_id() : $this->get_fallback_image();
		return apply_filters( 'scriptlesssocialsharing_image_url', $this->get_image_url( $featured_image ) );
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
	 * If a pinterest specific image is set, get the URL.
	 * @return string
	 */
	protected function pinterest_image() {
		$pinterest = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true );
		return $this->get_image_url( $pinterest );
	}

	/**
	 * Convert an image ID into a URL string.
	 * @param $id
	 *
	 * @return string
	 */
	protected function get_image_url( $id ) {
		$source    = wp_get_attachment_image_src( $id, 'large', false );
		return isset( $source[0] ) ? $source[0] : '';
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
	 * add twitter handle to URL
	 * @return string twitter handle (default is empty)
	 */
	protected function twitter_handle() {
		return apply_filters( 'scriptlesssocialsharing_twitter_handle', $this->setting['twitter_handle'] );
	}

	/**
	 * Modify the heading above the buttons
	 * @return string heading
	 */
	protected function heading( $heading ) {
		$heading = apply_filters( 'scriptlesssocialsharing_heading', $heading );
		if ( ! $heading ) {
			return '';
		}
		return '<h3>' . $heading . '</h3>';
	}

	/**
	 * replace spaces in a string with %20 for URLs
	 * @param  string $string passed through from another source
	 * @return string         same string, just %20 instead of spaces
	 */
	protected function replace( $string ) {
		return htmlentities( $string );
	}

	/**
	 * subject line for the email button
	 * @return string can be modified via filter
	 */
	protected function email_subject() {
		return apply_filters( 'scriptlesssocialsharing_email_subject', $this->setting['email_subject'] );
	}

	/**
	 * body text for the email button
	 * @return string can be modified via filter
	 */
	protected function email_body() {
		return apply_filters( 'scriptlesssocialsharing_email_body', __( 'I read this post and wanted to share it with you. Here\'s the link:', 'scriptless-social-sharing' ) );
	}

	/**
	 * If a Pinterest specific image is set, add it to the content, but hidden.
	 * @param $content
	 *
	 * @return string
	 */
	public function hide_pinterest_image( $content ) {
		$pinterest_image  = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true );
		$pinterest_button = $this->setting['buttons']['pinterest'];
		if ( ! $pinterest_button || ! $pinterest_image ) {
			return $content;
		}
		$alt_text = get_post_meta( $pinterest_image, '_wp_attachment_image_alt', true );
		return $content . wp_get_attachment_image( $pinterest_image, 'large', false, array(
			'data-pin-media' => 'true',
			'style'          => 'display:none;',
			'alt'            => $alt_text ? $alt_text : the_title_attribute( 'echo=0' ),
		) );
	}

	/**
	 * Allow pinterest data attributes on our links.
	 *
	 * @param $allowed array
	 * @param $context string
	 *
	 * @return mixed
	 */
	public function filter_allowed_html( $allowed, $context ) {

		if ( 'post' === $context ) {
			$allowed['a']['data-pin-custom']   = true;
			$allowed['a']['data-pin-no-hover'] = true;
			$allowed['a']['data-pin-do']       = true;
		}

		return $allowed;
	}
}
