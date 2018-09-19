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

	/**
	 * Post meta key for custom Pinterest description.
	 *
	 * @var string
	 */
	protected $description = '_scriptlesssocialsharing_description';

	/**
	 * Post types which can show buttons
	 * @var array
	 */
	protected $post_types;

	/**
	 * Add a custom post metabox.
	 */
	public function add_meta_box() {
		add_meta_box(
			'scriptless_social_sharing',
			__( 'Scriptless Social Sharing', 'scriptless-social-sharing' ),
			array( $this, 'do_metabox' ),
			$this->post_types(),
			'side',
			'low'
		);
	}

	/**
	 * Enqueue javascript for image uploader.
	 */
	public function enqueue() {
		$screen = get_current_screen();
		if ( ! in_array( $screen->post_type, $this->post_types(), true ) ) {
			return;
		}
		$minify = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script(
			'scriptless-upload',
			plugins_url( "/includes/js/image-upload{$minify}.js", dirname( __FILE__ ) ),
			array( 'jquery', 'media-upload', 'thickbox' ),
			'2.2.0',
			true
		);

		wp_enqueue_media();
		wp_enqueue_script( 'scriptless-upload' );
		wp_localize_script( 'scriptless-upload', 'scriptlessL10n', array(
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
		$this->do_image(
			$this->image,
			__( 'Custom Pinterest Image', 'scriptless-social-sharing' )
		);
		$this->do_textarea(
			$this->description,
			__( 'Custom Pinterest Description', 'scriptless-social-sharing' ),
			__( 'Optionally set a custom description for Pinterest pins. This can be used with a custom Pinterest image, or on its own.', 'scriptless-social-sharing' )
		);
		$this->do_checkbox(
			$this->disable,
			__( 'Don\'t show sharing buttons for this post', 'scriptless-social-sharing' )
		);
	}

	/**
	 * Print out the image preview and buttons.
	 *
	 * @param $id
	 * @param $label
	 */
	protected function do_image( $id, $label ) {
		printf(
			'<p><label for="%s">%s</label></p>',
			esc_attr( $id ),
			esc_html( $label )
		);
		$meta = get_post_meta( get_the_ID(), $this->image, true );
		$this->render_image_preview( $label, $meta );
		$this->render_buttons( $id, $meta );
	}

	/**
	 * display image preview
	 *
	 * @param      $label
	 * @param      $meta
	 *
	 * @since 1.5.0
	 */
	protected function render_image_preview( $label, $meta ) {
		if ( ! $meta ) {
			return;
		}
		$preview = wp_get_attachment_image_src( (int) $meta, 'medium' );
		printf(
			'<div class="scriptless-image-preview"><img src="%s" alt="%s" style="%s" /></div>',
			esc_url( $preview[0] ),
			esc_attr( $label ),
			'max-width:100%;'
		);
	}

	/**
	 * show image select/delete buttons
	 *
	 * @param  int   $id image ID
	 *
	 * @param string $meta
	 *
	 * @since 1.5.0
	 */
	protected function render_buttons( $id, $meta ) {
		printf(
			'<input type="hidden" class="scriptless-image-id" name="%1$s" value="%2$s" />',
			esc_attr( $id ),
			esc_attr( $meta )
		);
		printf(
			'<input id="%s" type="button" class="scriptless-upload button-secondary hide-if-no-js" value="%s" />',
			esc_attr( $id ),
			esc_attr__( 'Select Pinterest Image', 'scriptless-social-sharing' )
		);
		if ( ! empty( $meta ) ) {
			printf(
				' <input type="button" class="scriptless-delete button-secondary hide-if-no-js" value="%s" />',
				esc_attr__( 'Delete Image', 'scriptless-social-sharing' )
			);
		}
	}

	/**
	 * Create the description textarea for the metabox.
	 *
	 * @since 2.2.0
	 *
	 * @param        $id    string
	 * @param        $label string
	 * @param string $description
	 */
	protected function do_textarea( $id, $label, $description = '' ) {
		printf(
			'<p><label for="%s">%s</label></p>',
			esc_attr( $id ),
			esc_attr( $label )
		);
		printf(
			'<textarea class="large-text" rows="3" id="%1$s" name="%1$s" aria-label="%3$s">%2$s</textarea>',
			esc_attr( $id ),
			esc_textarea( get_post_meta( get_the_ID(), $id, true ) ),
			esc_attr( $label )
		);
		if ( ! $description ) {
			return;
		}
		printf(
			'<p class="description">%s</p>',
			esc_html( $description )
		);
	}

	/**
	 * Add the checkbox to the publishing metabox.
	 */
	protected function do_checkbox( $id, $label ) {
		$check = (bool) get_post_meta( get_the_ID(), $id, true );
		printf(
			'<p><label for="%1$s"><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s/>%3$s</label>',
			esc_attr( $id ),
			checked( $check, 1, false ),
			esc_html( $label )
		);
	}

	/**
	 * Get the allowed post types.
	 *
	 * @since 2.2.0
	 * @return array
	 */
	protected function post_types() {
		if ( isset( $this->post_types ) ) {
			return $this->post_types;
		}
		$this->post_types = scriptlesssocialsharing_post_types();

		return $this->post_types;
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
				$value = esc_textarea( filter_input( INPUT_POST, $m, FILTER_SANITIZE_STRING ) );
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
