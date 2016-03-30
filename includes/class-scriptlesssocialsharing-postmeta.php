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
	 * Add a metabox to the post editor, only if that post type allows sharing buttons.
	 */
	public function add_meta_box() {

		$this->setting = get_option( 'scriptlesssocialsharing', false );
		$post_types    = isset( $this->setting['post_types'] ) ? $this->setting['post_types'] : array( 'post' );
		foreach ( $post_types as $type ) {
			add_meta_box(
				'scriptlesssocialsharing-entry-meta',
				__( 'Social Sharing', 'scriptless-social-sharing' ),
				array( $this, 'meta_box' ),
				$type,
				'side',
				'default'
			);
		}

	}

	/**
	 * Build the metabox with the checkbox setting.
	 */
	public function meta_box() {

		$check = get_post_meta( get_the_ID(), $this->disable, true ) ? 1 : '';

		wp_nonce_field( 'scriptlesssocialsharing_post_save', 'scriptlesssocialsharing_post_nonce' );
		echo '<p>';
		printf( '<input type="checkbox" id="%1$s" name="%1$s" %2$s/>', $this->disable, checked( $check, 1, false ) );
		printf( '<label for="%s">%s</label>', $this->disable, __( 'Don\'t show sharing buttons for this post', 'scriptless-social-sharing' ) );
		echo '</p>';
	}

	/**
	 * Update the post meta.
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

		if ( isset( $_POST[ $this->disable ] ) ) {
			update_post_meta( $post_id, $this->disable, 1 );
		} else {
			delete_post_meta( $post_id, $this->disable );
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
