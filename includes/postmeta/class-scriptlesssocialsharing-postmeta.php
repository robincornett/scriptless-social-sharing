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
			plugins_url( "/js/image-upload{$minify}.js", dirname( __FILE__ ) ),
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
		include_once plugin_dir_path( __FILE__ ) . 'class-scriptlesssocialsharing-postmeta-fields.php';
		$fields_class = new ScriptlessSocialSharingPostMetaFields();
		$fields       = $this->get_fields();
		foreach ( $fields as $field ) {
			$fields_class->do_field( $field );
		}
	}

	/**
	 * Define the post meta fields.
	 *
	 * @since 2.2.0
	 * @return array
	 */
	protected function get_fields() {
		return array(
			array(
				'id'    => $this->image,
				'type'  => 'image',
				'label' => __( 'Custom Pinterest Image', 'scriptless-social-sharing' ),
			),
			array(
				'id'          => $this->description,
				'type'        => 'textarea',
				'label'       => __( 'Custom Pinterest Description', 'scriptless-social-sharing' ),
				'description' => __( 'Optionally set a custom description for Pinterest pins. This can be used with a custom Pinterest image, or on its own.', 'scriptless-social-sharing' ),
			),
			array(
				'id'    => $this->disable,
				'type'  => 'checkbox',
				'label' => __( 'Don\'t show sharing buttons for this post', 'scriptless-social-sharing' ),
			),
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

		$this->validate( $post_id, $this->get_fields() );
	}

	/**
	 * Validate the post meta fields.
	 *
	 * @param $post_id int
	 * @param $fields  array
	 */
	protected function validate( $post_id, $fields ) {
		foreach ( $fields as $field ) {
			switch ( $field['type'] ) {
				case 'textarea':
					$value = esc_textarea( filter_input( INPUT_POST, $field['id'], FILTER_SANITIZE_STRING ) );
					break;

				default:
					$value = (int) filter_input( INPUT_POST, $field['id'], FILTER_SANITIZE_STRING );
			}

			if ( $value ) {
				update_post_meta( $post_id, $field['id'], $value );
			} else {
				delete_post_meta( $post_id, $field['id'] );
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
