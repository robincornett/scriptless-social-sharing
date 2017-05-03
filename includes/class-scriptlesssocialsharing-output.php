<?php

/**
 * Class ScriptlessSocialSharingOutput
 * Plugin class for buttons output.
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingOutput {

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
		if ( ! is_main_query() || get_the_ID() !== get_queried_object_id() ) {
			$cando = false;
		}
		$post_types  = scriptlesssocialsharing_post_types();
		$is_disabled = get_post_meta( get_the_ID(), '_scriptlesssocialsharing_disable', true ) ? true : '';
		if ( ! is_singular( $post_types ) || is_feed() || $is_disabled ) {
			$cando = false;
		}
		global $post;
		if ( is_object( $post ) && has_shortcode( $post->post_content, 'scriptless' ) ) {
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
		$enqueue = new ScriptlessSocialSharingEnqueue( $this->setting, $this->make_buttons() );
		$enqueue->load_styles();
	}

	/**
	 * Decide where to add the sharing buttons.
	 *
	 * @since 2.0.0
	 */
	public function do_location() {
		if ( ! $this->can_do_buttons() ) {
			return;
		}
		$post_type = get_post_type();
		if ( ! isset( $this->setting['post_types'][ $post_type ] ) || ! $this->setting['post_types'][ $post_type ] || ! is_array( $this->setting['post_types'][ $post_type ] ) ) {
			return;
		}
		$locations = array(
			'before'      => array(
				'hook'     => false,
				'filter'   => 'the_content',
				'priority' => 99,
			),
			'after'       => array(
				'hook'     => false,
				'filter'   => 'the_content',
				'priority' => 99,
			),
		);
		if ( 'genesis' === basename( TEMPLATEPATH ) && apply_filters( 'scriptlesssocialsharing_prefer_genesis_hooks', false ) ) {
			$locations = array(
				'before'      => array(
					'hook'     => 'genesis_entry_header',
					'filter'   => false,
					'priority' => 20,
				),
				'after'       => array(
					'hook'     => 'genesis_entry_footer',
					'filter'   => false,
					'priority' => 5,
				),
			);
		}
		$locations = apply_filters( 'scriptlesssocialsharing_locations', $locations );
		if ( isset( $this->setting['post_types'][ $post_type ]['after'] ) && $this->setting['post_types'][ $post_type ]['after'] ) {
			if ( $locations['after']['hook'] ) {
				add_action( $locations['after']['hook'], array( $this, 'print_buttons' ), $locations['after']['priority'] );
			} elseif ( $locations['after']['filter'] ) {
				add_filter( $locations['after']['filter'], array( $this, 'after_content' ), $locations['after']['priority'] );
			}
		}
		if ( isset( $this->setting['post_types'][ $post_type ]['before'] ) && $this->setting['post_types'][ $post_type ]['before'] ) {
			if ( $locations['before']['hook'] ) {
				add_action( $locations['before']['hook'], array( $this, 'print_buttons' ), $locations['before']['priority'] );
			} elseif ( $locations['after']['filter'] ) {
				add_filter( $locations['after']['filter'], array( $this, 'before_content' ), $locations['before']['priority'] );
			}
		}
	}

	/**
	 * Print the sharing buttons.
	 * @since 2.0.0
	 */
	public function print_buttons() {
		echo wp_kses_post( $this->do_buttons() );
	}

	/**
	 * Add the sharing buttons before the content.
	 * @param $content
	 *
	 * @return string
	 */
	public function before_content( $content ) {
		return $this->do_buttons() . $content;
	}

	/**
	 * Add the sharing buttons after the content.
	 * @param $content
	 *
	 * @return string
	 */
	public function after_content( $content ) {
		return $content . $this->do_buttons();
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
			$output .= $this->build_link_markup( $button );
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
			'buttons'      => '',
		);
		$atts    = shortcode_atts( $defaults, $atts, 'scriptless' );
		$buttons = $this->make_buttons();
		$passed  = $atts['buttons'] ? explode( ',', $atts['buttons'] ) : array();
		$output  = $atts['before'];
		$output .= $this->heading( $atts['heading'] );
		$output .= $atts['inner_before'];
		foreach ( $buttons as $button ) {
			if ( empty( $passed ) || in_array( $button['name'], $passed, true ) ) {
				$output .= $this->build_link_markup( $button );
			}
		}
		$output .= $atts['inner_after'];
		$output .= $atts['after'];
		return $output;
	}

	/**
	 * Create the anchor element markup.
	 * @param $button array the parameters for building the button.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	protected function build_link_markup( $button ) {
		return sprintf( '<a class="button %s" target="_blank" href="%s" %s><span class="sss-name">%s</span></a>', esc_attr( $button['name'] ), esc_url( $button['url'] ), $button['data'], $button['label'] );
	}

	/**
	 * Get the permalink to be shared via the button.
	 *
	 * @param  string $button_name The name of the button, e.g. 'twitter', 'facebook'.
	 * @return string The URL to be shared.
	 */
	protected function get_permalink( $button_name ) {
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
			 * @since 2.0.0
			 */
			$buttons[ $button ]['url']  = apply_filters( "scriptlesssocialsharing_{$button}_url", $url, $button, $attributes );

			/**
			 * Create a filter to add data attributes to social URLs.
			 * @since 2.0.0
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
	 * @since 2.0.0
	 */
	protected function get_twitter_url( $attributes ) {
		$yoast         = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		$twitter_title = $yoast ? $yoast : $attributes['title'];
		$query_args    = array(
			'text' => $twitter_title,
			'url'  => $this->get_permalink( 'twitter' ),
		);
		if ( $this->twitter_handle() ) {
			$query_args['via']     = $this->twitter_handle();
			$query_args['related'] = $this->twitter_handle();
		}

		return add_query_arg(
			$query_args,
			'https://twitter.com/intent/tweet'
		);
	}

	/**
	 * Get the URL for Facebook.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_facebook_url( $attributes ) {
		return add_query_arg(
			'u',
			$this->get_permalink( 'facebook' ),
			'https://www.facebook.com/sharer/sharer.php'
		);
	}

	/**
	 * Get the URL for Google+.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
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
	 * @since 2.0.0
	 */
	protected function get_pinterest_url( $attributes ) {
		$pinterest_img = $this->pinterest_image() ? $this->pinterest_image() : $this->featured_image();
		$pinterest_alt = get_post_meta( $pinterest_img, '_wp_attachment_image_alt', true );
		$pin_title     = $pinterest_alt ? $pinterest_alt : $attributes['title'];
		return add_query_arg(
			array(
				'url'         => $this->get_permalink( 'pinterest' ),
				'description' => $pin_title,
				'media'       => esc_url( $this->get_image_url( $pinterest_img ) ),
			),
			'https://pinterest.com/pin/create/button/'
		);
	}

	/**
	 * Add Pinterest data pin attributes to the URL markup.
	 * @return string
	 * @since 2.0.0
	 */
	public function add_pinterest_data() {
		return 'data-pin-no-hover="true" data-pin-custom="true" data-pin-do="skip"';
	}

	/**
	 * Get the Linkedin URL.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_linkedin_url( $attributes ) {
		$query_args = array(
			'mini'   => true,
			'url'    => $this->get_permalink( 'linkedin' ),
			'title'  => $attributes['title'],
			'source' => $attributes['home'],
		);
		if ( $this->description() ) {
			$query_args['summary'] = $this->description();
		}
		return add_query_arg(
			$query_args,
			'https://www.linkedin.com/shareArticle'
		);
	}

	/**
	 * Get the email URL.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
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
	 * @since 2.0.0
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
			'image'         => $this->setting['buttons']['pinterest'] ? $this->get_image_url( $this->featured_image() ) : '',
			'email_body'    => $this->email_body(),
			'email_subject' => $this->email_subject(),
			'pinterest'     => $this->get_image_url( $this->pinterest_image() ),
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
	 * add twitter handle to URL
	 * @return string twitter handle (default is empty)
	 */
	protected function twitter_handle() {
		return apply_filters( 'scriptlesssocialsharing_twitter_handle', $this->setting['twitter_handle'] );
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
