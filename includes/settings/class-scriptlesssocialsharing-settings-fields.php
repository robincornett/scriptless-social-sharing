<?php

/**
 * Class ScriptlessSocialSharingSettingsFields
 */
class ScriptlessSocialSharingSettingsFields {

	/**
	 * Slug for settings page.
	 * @var string
	 */
	protected $page = 'scriptlesssocialsharing';

	/**
	 * @var array
	 */
	protected $setting;

	/**
	 * ScriptlessSocialSharingSettingsFields constructor.
	 *
	 * @param $setting
	 */
	public function __construct( $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Initial field function.
	 * @param $args
	 */
	public function do_field( $args ) {
		$callback = array( $this, $args['callback'] );
		if ( is_callable( $callback ) ) {
			call_user_func( $callback, $args );
		}
		if ( ! empty( $args['description'] ) ) {
			$this->do_description( $args['description'] );
		}
	}
	/**
	 * Generic callback to create a checkbox setting.
	 *
	 * @since 1.0.0
	 */
	protected function do_checkbox( $args ) {
		$setting = $this->get_checkbox_setting( $args );
		printf( '<input type="hidden" name="%s[%s]" value="0" />', esc_attr( $this->page ), esc_attr( $args['id'] ) );
		printf( '<label for="%1$s[%2$s]" style="margin-right:12px;"><input type="checkbox" name="%1$s[%2$s]" id="%1$s[%2$s]" value="1" %3$s class="code" />%4$s</label>',
			esc_attr( $this->page ),
			esc_attr( $args['id'] ),
			checked( 1, esc_attr( $setting ), false ),
			esc_attr( $args['label'] )
		);
	}

	/**
	 * Get the current value for the checkbox.
	 *
	 * @param $args
	 *
	 * @return int
	 */
	protected function get_checkbox_setting( $args ) {
		$setting = isset( $this->setting[ $args['id'] ] ) ? $this->setting[ $args['id'] ] : 0;
		if ( isset( $args['setting_name'] ) ) {
			if ( isset( $this->setting[ $args['setting_name'] ][ $args['name'] ] ) ) {
				$setting = $this->setting[ $args['setting_name'] ][ $args['name'] ];
			}
		}

		return $setting;
	}

	/**
	 * Set up choices for checkbox array
	 *
	 * @param $args array
	 */
	protected function do_checkbox_array( $args ) {
		foreach ( $args['choices'] as $key => $label ) {
			// due to error in setting this up in v 1.0-1.2, have to do a BC check for the post_type setting.
			$setting = isset( $this->setting[ $args['id'] ][ $key ] ) ? $this->setting[ $args['id'] ][ $key ] : 0;
			if ( 'post_types' === $args['id'] && ! isset( $this->setting[ $args['id'] ][ $key ] ) ) {
				$setting = in_array( $key, $this->setting['post_types'], true );
			}
			printf(
				'<input type="hidden" name="%1$s[%2$s][%3$s]" value="0" />',
				esc_attr( $this->page ),
				esc_attr( $args['id'] ),
				esc_attr( $key )
			);
			printf(
				'<label for="%4$s[%5$s][%1$s]" style="margin-right:12px;"><input type="checkbox" name="%4$s[%5$s][%1$s]" id="%4$s[%5$s][%1$s]" value="1"%2$s class="code" data-attr="%1$s"/>%3$s</label>',
				esc_attr( $key ),
				checked( 1, $setting, false ),
				esc_html( $label ),
				esc_attr( $this->page ),
				esc_attr( $args['id'] )
			);
			echo isset( $args['clear'] ) && $args['clear'] ? '<br />' : '';
		}
	}

	/**
	 * Generic callback to create a number field setting.
	 *
	 * @since 1.0.0
	 */
	protected function do_number( $args ) {
		$setting = isset( $this->setting[ $args['id'] ] ) ? $this->setting[ $args['id'] ] : 0;
		if ( ! isset( $setting ) ) {
			$setting = 0;
		}
		printf( '<label for="%s[%s]">', esc_attr( $this->page ), esc_attr( $args['id'] ) );
		printf( '<input type="number" step="1" min="%1$s" max="%2$s" id="%5$s[%3$s]" name="%5$s[%3$s]" value="%4$s" class="small-text" />%6$s</label>',
			(int) $args['min'],
			(int) $args['max'],
			esc_attr( $args['id'] ),
			esc_attr( $setting ),
			esc_attr( $this->page ),
			esc_attr( $args['label'] )
		);

	}

	/**
	 * Generic callback to create a text field.
	 *
	 * @since 1.0.0
	 */
	protected function do_text_field( $args ) {
		printf( '<input type="text" id="%3$s[%1$s]" name="%3$s[%1$s]" value="%2$s" class="regular-text" />',
			esc_attr( $args['id'] ),
			esc_attr( $this->setting[ $args['id'] ] ),
			esc_attr( $this->page )
		);
	}

	/**
	 * Generic function to create a radio button setting
	 */
	protected function do_radio_buttons( $args ) {
		echo '<fieldset>';
		printf( '<legend class="screen-reader-text">%s</legend>', $args['legend'] );
		foreach ( $args['buttons'] as $key => $button ) {
			printf( '<label for="%5$s[%1$s][%2$s]" style="margin-right:12px !important;"><input type="radio" id="%5$s[%1$s][%2$s]" name="%5$s[%1$s]" value="%2$s"%3$s />%4$s</label>  ',
				esc_attr( $args['id'] ),
				esc_attr( $key ),
				checked( $key, $this->setting[ $args['id'] ], false ),
				esc_attr( $button ),
				esc_attr( $this->page )
			);
		}
		echo '</fieldset>';
	}

	/**
	 * Generic function to output a textarea
	 *
	 * @param $args
	 */
	protected function do_textarea_field( $args ) {
		$rows = isset( $args['rows'] ) ? $args['rows'] : 3;
		printf( '<textarea class="regular-text" rows="%4$s" id="%3$s[%1$s]" name="%3$s[%1$s]" aria-label="%3$s[%1$s]">%2$s</textarea>',
			esc_attr( $args['id'] ),
			esc_textarea( $this->setting[ $args['id'] ] ),
			esc_attr( $this->page ),
			(int) $rows
		);
	}

	/**
	 * Generic callback to display a field description.
	 *
	 * @param string $description
	 */
	protected function do_description( $description = '' ) {
		if ( ! $description ) {
			return;
		}
		printf( '<p class="description">%s</p>', wp_kses_post( $description ) );
	}

	/**
	 * Custom callback to create dropdown fields for each content type.
	 *
	 * @param $args array
	 */
	protected function do_content_types( $args ) {
		$this->do_description( $args['intro'] );
		foreach ( $this->get_post_types() as $post_type ) {
			echo '<h4 class="heading">' . esc_attr( $post_type->labels->name ) . '</h4>';
			$options = array(
				'before' => __( 'Before Content', 'scriptless-social-sharing' ),
				'after'  => __( 'After Content', 'scriptless-social-sharing' ),
				'manual' => __( 'Manual Placement', 'scriptless-social-sharing' ),
			);
			foreach ( $options as $key => $value ) {
				$setting = $this->get_content_types_location( $post_type, $key );
				printf( '<input type="hidden" name="%s[post_types][%s][%s]" value="0" />', esc_attr( $this->page ), esc_attr( $post_type->name ), esc_attr( $key ) );
				printf( '<label for="%4$s[post_types][%5$s][%1$s]" style="margin-right:12px;"><input type="checkbox" name="%4$s[post_types][%5$s][%1$s]" id="%4$s[post_types][%5$s][%1$s]" value="1"%2$s class="code"/>%3$s</label>',
					esc_attr( $key ),
					checked( 1, $setting, false ),
					esc_html( $value ),
					esc_attr( $this->page ),
					esc_attr( $post_type->name )
				);
			}
		}
	}

	/**
	 * Allow users to sort the buttons into a custom order.
	 *
	 * @since 2.3.0
	 * @param $args
	 */
	public function do_custom_order( $args ) {
		foreach ( $this->get_buttons( $args['choices'] ) as $key => $label ) {
			if ( empty( $this->setting['buttons'][ $key ] ) ) {
				continue;
			}
			$value = ! empty( $this->setting['order'][ $key ] ) ? $this->setting['order'][ $key ] : 0;
			printf(
				'<div class="button sortable-button" style="margin-right:4px;"><input type="hidden" name="%3$s[order][%4$s]" value="%2$s">%1$s</input></div>',
				esc_html( $label ),
				(int) $value,
				esc_attr( $this->page ),
				esc_attr( $key )
			);
		}
	}

	/**
	 * Get the active buttons which can be sorted into a custom order.
	 *
	 * @since 2.3.0
	 * @param $buttons
	 * @return array
	 */
	private function get_buttons( $buttons ) {
		if ( ! $this->setting['order'] ) {
			return $buttons;
		}
		$order = array_intersect_key( $this->setting['order'], $buttons );

		return array_merge( $order, $buttons );
	}
	/**
	 * Check the database setting
	 *
	 * @param $post_type
	 * @param $key
	 *
	 * @return int
	 */
	protected function get_content_types_location( $post_type, $key ) {
		$setting = 0;
		if ( isset( $this->setting['post_types'][ $post_type->name ][ $key ] ) ) {
			return $this->setting['post_types'][ $post_type->name ][ $key ];
		}
		if ( isset( $this->setting['post_types'][ $post_type->name ] ) && $this->setting['post_types'][ $post_type->name ] && isset( $this->setting['location'] ) ) {
			if ( 'manual' === $key ) {
				$setting = 1;
			}
			if ( $this->setting['location']['before'] && 'before' === $key ) {
				$setting = 1;
			} elseif ( $this->setting['location']['after'] && 'after' === $key ) {
				$setting = 1;
			}
		}

		return $setting;
	}

	/**
	 * Get all registered, public post types.
	 * @return array
	 */
	protected function get_post_types() {
		$output         = 'objects';
		$built_in       = array(
			'public'   => true,
			'_builtin' => true,
		);
		$built_in_types = get_post_types( $built_in, $output );
		unset( $built_in_types['attachment'] );
		$custom_args  = array(
			'public'   => true,
			'_builtin' => false,
		);
		$custom_types = get_post_types( $custom_args, $output );

		return array_merge( $built_in_types, $custom_types );
	}
}
