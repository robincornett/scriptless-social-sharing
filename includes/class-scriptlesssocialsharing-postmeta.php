<?php

/**
 * Class ScriptlessSocialSharingPostMeta
 * @package ScriptlessSocialSharing
 */
class ScriptlessSocialSharingPostMeta {

	/**
	 * The plugin setting
	 * @var $setting
	 */
	protected $setting;

	/**
	 * Post meta key to disable buttons
	 * @var string
	 */
	protected $disable = '_scriptlesssocialsharing_disable';

	/**
	 * Post meta key for custom Pinterest image
	 * @var string
	 */
	protected $image = '_scriptlesssocialsharing_pinterest';

	protected $description = '_scriptlesssocialsharing_description';

	/**
	 * Post types which can show buttons
	 * @var array
	 */
	protected $post_types = array();

	/**
	 * Add a custom post metabox.
	 */
	public function add_meta_box() {
		$this->post_types = scriptlesssocialsharing_post_types();
		add_meta_box(
			'scriptless_social_sharing',
			__( 'Scriptless Social Sharing', 'scriptless-social-sharing' ),
			array( $this, 'do_metabox' ),
			$this->post_types,
			'side',
			'low'
		);
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue javascript for image uploader.
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->post_type, $this->post_types, true ) ) {
			return;
		}
		wp_register_script(
			'scriptless-upload',
			plugins_url( '/includes/js/image-upload.js', dirname( __FILE__ ) ),
			array( 'jquery', 'media-upload', 'thickbox' ),
			'2.0.0',
			false
		);

		wp_enqueue_media();
		wp_enqueue_script( 'scriptless-upload' );
		wp_localize_script( 'scriptless-upload', 'objectL10n', array(
			'text' => __( 'Select Image', 'scriptless-social-sharing' ),
		) );
	}

	/**
	 * Fill the metabox.
	 *
	 * @param $post object
	 */
	public function do_metabox( $post ) {
		wp_nonce_field( 'scriptlesssocialsharing_post_save', 'scriptlesssocialsharing_post_nonce' );
		printf( '<label for="%s">%s</label>', esc_attr( $this->image ), esc_html__( 'Custom Pinterest Settings', 'scriptless-social-sharing' ) );
		echo '<p>';
		$id = get_post_meta( $post->ID, $this->image, true );
		echo wp_kses_post( $this->render_image_preview( $id ) );
		$this->render_buttons( $id );
		echo '</p>';
		$this->do_textarea(
			$this->description,
			__( 'Custom Pinterest Description', 'scriptless-social-sharing' )
		);
		$this->do_checkbox();
	}

	/**
	 * display image preview
	 *
	 * @param  int $id featured image ID
	 *
	 * @return string
	 *
	 * @since 1.5.0
	 */
	public function render_image_preview( $id ) {
		if ( ! $id ) {
			return '';
		}
		$alt_text = __( 'Custom Pinterest Image', 'scriptless-social-sharing' );
		$preview  = wp_get_attachment_image_src( (int) $id, 'medium' );
		$image    = sprintf( '<div class="upload_logo_preview"><img src="%s" alt="%s" style="%s" /></div>', esc_url( $preview[0] ), esc_attr( $alt_text ), esc_attr( 'max-width:100%;' ) );

		return $image;
	}

	/**
	 * show image select/delete buttons
	 *
	 * @param  int $id image ID
	 *
	 * @since 1.5.0
	 */
	public function render_buttons( $id ) {
		$name = $this->image;
		printf( '<input type="hidden" class="upload_image_id" name="%1$s" value="%2$s" />', esc_attr( $name ), esc_attr( $id ) );
		printf( '<input id="%s" type="button" class="upload_default_image button-secondary" value="%s" />',
			esc_attr( $name ),
			esc_attr__( 'Select Pinterest Image', 'scriptless-social-sharing' )
		);
		if ( ! empty( $id ) ) {
			printf( ' <input type="button" class="delete_image button-secondary" value="%s" />',
				esc_attr__( 'Delete Image', 'scriptless-social-sharing' )
			);
		}
	}

	/**
	 * Create the description textarea for the metabox.
	 *
	 * @since 2.2.0
	 *
	 * @param $id    string
	 * @param $label string
	 */
	protected function do_textarea( $id, $label ) {
		$value = get_post_meta( get_the_ID(), $id, true );
		echo '<p>';
		printf( '<label for="%s">%s</label>',
			esc_attr( $id ),
			esc_attr( $label )
		);
		printf( '<textarea class="large-text" rows="3" id="%1$s" name="%1$s" aria-label="%3$s">%2$s</textarea>',
			esc_attr( $id ),
			sanitize_textarea_field( $value ),
			esc_attr( $label )
		);
		echo '</p>';
	}

	/**
	 * Add the checkbox to the publishing metabox.
	 */
	public function do_checkbox() {

		$screen = get_current_screen();
		if ( ! in_array( $screen->post_type, $this->post_types, true ) ) {
			return;
		}
		$check = get_post_meta( get_the_ID(), $this->disable, true ) ? 1 : '';
		printf( '<p><label for="%1$s"><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s/>%3$s</label>', esc_attr( $this->disable ), checked( $check, 1, false ), esc_html__( 'Don\'t show sharing buttons for this post', 'scriptless-social-sharing' ) );
	}

	/**
	 * Update the post meta.
	 *
	 * @param $post_id
	 */
	public function save_meta( $post_id ) {

		// Bail if we're doing an auto save
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// if our nonce isn't there, or we can't verify it, bail
		if ( ! $this->user_can_save( 'scriptlesssocialsharing_post_save', 'scriptlesssocialsharing_post_nonce' ) ) {
			return;
		}

		// if our current user can't edit this post, bail
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$meta = array(
			$this->disable,
			$this->image,
			$this->description,
		);

		foreach ( $meta as $m ) {
			if ( $this->description === $m ) {
				$value = sanitize_textarea_field( filter_input( INPUT_POST, $m, FILTER_SANITIZE_STRING ) );
			} else {
				$value = (int) filter_input( INPUT_POST, $m, FILTER_SANITIZE_STRING );
			}
			if ( $value ) {
				update_post_meta( $post_id, $m, $value );
			} else {
				delete_post_meta( $post_id, $m );
			}
		}
	}

	/**
	 * Determines if the user has permission to save the information from the submenu
	 * page.
	 *
	 * @since    1.2.0
	 * @access   protected
	 *
	 * @param    string $action The name of the action specified on the submenu page
	 * @param    string $nonce  The nonce specified on the submenu page
	 *
	 * @return   bool                True if the user has permission to save; false, otherwise.
	 * @author   Tom McFarlin (https://tommcfarlin.com/save-wordpress-submenu-page-options/)
	 */
	protected function user_can_save( $action, $nonce ) {
		$is_nonce_set   = isset( $_POST[ $nonce ] );
		$is_valid_nonce = false;

		if ( $is_nonce_set ) {
			$is_valid_nonce = wp_verify_nonce( $_POST[ $nonce ], $action );
		}

		return ( $is_nonce_set && $is_valid_nonce );
	}
}
