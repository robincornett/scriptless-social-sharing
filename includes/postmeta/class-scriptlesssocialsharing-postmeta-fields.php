<?php

/**
 * Post meta fields helper class.
 * Class ScriptlessSocialSharingPostMetaFields
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingPostMetaFields {

	/**
	 * Generic field function.
	 *
	 * @param $args array
	 */
	public function do_field( $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'id'          => false,
				'type'        => false,
				'label'       => false,
				'description' => '',
			)
		);
		if ( in_array( false, $args, true ) ) {
			return;
		}
		$method = "do_{$args['type']}";
		$this->$method( $args['id'], $args['label'] );
		if ( ! $args['description'] ) {
			return;
		}
		printf(
			'<p class="description">%s</p>',
			esc_html( $args['description'] )
		);
	}

	/**
	 * Print out the image preview and buttons.
	 *
	 * @param        $id
	 * @param        $label
	 */
	public function do_image( $id, $label ) {
		printf(
			'<p><label for="%s">%s</label></p>',
			esc_attr( $id ),
			esc_html( $label )
		);
		$meta = get_post_meta( get_the_ID(), $id, true );
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
	 */
	protected function do_textarea( $id, $label ) {
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
	}

	/**
	 * Add the checkbox to the publishing metabox.
	 *
	 * @param        $id
	 * @param        $label
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
}
