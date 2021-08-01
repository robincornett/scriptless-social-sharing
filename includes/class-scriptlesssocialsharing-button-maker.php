<?php

/**
 * The ScriptlessSocialSharingButtonMaker class registers a
 * new sharing button/network and builds the button.
 */
class ScriptlessSocialSharingButtonMaker {

	/**
	 * The network slug serves as the ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The array of parameters to define the sharing button.
	 *
	 * @var array
	 */
	private $args;

	/**
	 * The class constructor.
	 *
	 * @since 3.2
	 * @param string $id
	 * @param array  $args
	 */
	public function __construct( $id, $args ) {

		if ( empty( $id ) || empty( $args['label'] ) || empty( $args['url_base'] ) ) {
			return;
		}

		$this->id   = $id;
		$this->args = $this->get_args( $args );

		add_filter( 'scriptlesssocialsharing_networks', array( $this, 'add_network' ) );
		add_filter( "scriptlesssocialsharing_{$this->id}_query_args", array( $this, 'query_args' ), 10, 4 );
		add_filter( "scriptlesssocialsharing_{$this->id}_url_base", array( $this, 'url_base' ) );
		if ( ! empty( $this->args['svg'] ) ) {
			add_filter( 'scriptlesssocialsharing_svg_icons', array( $this, 'add_svg' ) );
		}
		if ( ! empty( $this->args['target_self'] ) ) {
			add_filter( 'scriptlesssocialsharing_link_target', '__return_empty_string' );
		}
	}

	/**
	 * Gets the button parameters, parsed with the defaults.
	 *
	 * @since 3.2
	 * @param  array $args
	 * @return array
	 */
	private function get_args( $args ) {
		return wp_parse_args(
			$args,
			array(
				'label'      => '',
				'icon'       => '',
				'color'      => '#333',
				'url_base'   => '',
				'query_args' => array(),
			)
		);
	}

	/**
	 * Adds a new network to the sharing settings.
	 *
	 * @since 3.2
	 * @param  array $networks The array of networks.
	 * @return array
	 */
	public function add_network( $networks ) {
		$networks[ $this->id ] = array(
			'name'  => $this->id,
			'label' => $this->args['label'],
			'icon'  => $this->args['icon'],
			'color' => $this->args['color'],
		);

		return $networks;
	}

	/**
	 * Customizes the query args for the new button.
	 *
	 * @since 3.2
	 * @param  array  $query_args The array of query parameters to be added to the URL.
	 * @param  string $id         The button name.
	 * @param  array  $attributes The array of attributes specific to the post being shared.
	 * @param  array  $setting    The plugin setting.
	 * @return array
	 */
	public function query_args( $query_args, $id, $attributes, $setting ) {

		if ( empty( $this->args['query_args'] ) ) {
			return $query_args;
		}

		foreach ( $this->args['query_args'] as $key => $arg ) {
			if ( is_array( $arg ) ) {
				$string = '';
				foreach ( $arg as $variable ) {
					$updated_arg = strtolower( str_replace( '%%', '', $variable ) );
					if ( ! empty( $attributes[ $updated_arg ] ) ) {
						$string .= ' ' . $attributes[ $updated_arg ];
					}
				}
				if ( ! empty( $string ) ) {
					$query_args[ $key ] = $string;
				}
			} else {
				$updated_arg = strtolower( str_replace( '%%', '', $arg ) );
				if ( ! empty( $attributes[ $updated_arg ] ) ) {
					$query_args[ $key ] = $attributes[ $updated_arg ];
				} else {
					$query_args[ $key ] = $arg;
				}
			}
		}

		return $query_args;
	}

	/**
	 * Gets the base URL for the sharing button.
	 *
	 * @since 3.2
	 * @return string
	 */
	public function url_base() {
		return $this->args['url_base'];
	}

	/**
	 * If an alternative SVG icon has been passed to the class, use that.
	 *
	 * @param  array $icons
	 * @return array
	 */
	public function add_svg( $icons ) {
		$icons[ $this->id ] = $this->args['svg'];

		return $icons;
	}
}

/**
 * Helper function to create a new sharing button in one go.
 *
 * @since 3.2
 * @param  string $id   The slug for the sharing network/service.
 * @param  array  $args The array of parameters to define the button.
 * @return void
 */
function scriptlesssocialsharing_create_button( $id, $args ) {
	new ScriptlessSocialSharingButtonMaker( $id, $args );
}
