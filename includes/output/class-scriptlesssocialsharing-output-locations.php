<?php

/**
 * Class ScriptlessSocialSharingOutputLocations
 * @since 2.2.0
 */
class ScriptlessSocialSharingOutputLocations extends ScriptlessSocialSharingOutputButtons {

	/**
	 * Decide where to add the sharing buttons.
	 *
	 * @since 2.0.0
	 */
	public function do_location() {
		if ( $this->is_disabled() ) {
			return;
		}
		if ( ! $this->can_do_buttons() ) {
			return;
		}
		$post_type = get_post_type();
		$setting   = $this->get_setting();
		$locations = $this->get_locations();
		foreach ( $locations as $location => $args ) {
			if ( ! in_array( $location, array( 'before', 'after' ), true ) ) {
				continue;
			}
			if ( isset( $setting['post_types'][ $post_type ][ $location ] ) && $setting['post_types'][ $post_type ][ $location ] ) {
				if ( $args['hook'] ) {
					add_action( $args['hook'], array( $this, 'print_buttons' ), $args['priority'] );
				} elseif ( $args['filter'] ) {
					add_filter( $args['filter'], array( $this, "{$location}_content" ), $args['priority'] );
				}
			}
		}
	}

	/**
	 * Define the hook/filter locations for sharing buttons.
	 * @return array
	 */
	protected function get_locations() {
		$locations = array(
			'before' => array(
				'hook'     => false,
				'filter'   => 'the_content',
				'priority' => 99,
			),
			'after'  => array(
				'hook'     => false,
				'filter'   => 'the_content',
				'priority' => 99,
			),
		);
		$genesis   = $this->genesis_hooks();
		if ( $genesis ) {
			$locations = $genesis;
		}

		return apply_filters( 'scriptlesssocialsharing_locations', $locations );
	}

	/**
	 * If the Genesis Framework is active, check to see if Genesis hooks should be preferred.
	 * @since 3.0.0
	 *
	 * @return array|bool
	 */
	private function genesis_hooks() {
		if ( 'genesis' !== get_template() ) {
			return false;
		}
		$setting     = $this->get_setting();
		$use_genesis = apply_filters( 'scriptlesssocialsharing_prefer_genesis_hooks', $setting['genesis'] );
		if ( ! $use_genesis ) {
			return false;
		}

		return array(
			'before' => array(
				'hook'     => 'genesis_entry_header',
				'filter'   => false,
				'priority' => 20,
			),
			'after'  => array(
				'hook'     => 'genesis_entry_footer',
				'filter'   => false,
				'priority' => 5,
			),
		);
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
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function before_content( $content ) {
		return $this->do_buttons() . $content;
	}

	/**
	 * Add the sharing buttons after the content.
	 *
	 * @param $content
	 *
	 * @return string
	 */
	public function after_content( $content ) {
		return $content . $this->do_buttons();
	}
}
