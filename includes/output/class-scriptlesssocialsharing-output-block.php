<?php

/**
 * Class ScriptlessSocialSharingOutputBlock
 */
class ScriptlessSocialSharingOutputBlock extends ScriptlessSocialSharingOutputShortcode {

	/**
	 * The block name.
	 *
	 * @var string
	 */
	protected $name = 'scriptlesssocialsharing/buttons';

	/**
	 * @var string
	 */
	protected $block = 'scriptless-social-sharing-buttons';

	/**
	 * The plugin setting.
	 * @var array
	 */
	protected $setting;

	/**
	 * Register our block type.
	 */
	public function init() {
		$this->register_script_style();
		register_block_type(
			$this->name,
			array(
				'editor_script'   => $this->block . '-block',
				'editor_style'    => $this->block . '-block',
				'attributes'      => array_merge( $this->fields(), $this->networks() ),
				'render_callback' => array( $this, 'render' ),
			)
		);
		add_action( 'enqueue_block_editor_assets', array( $this, 'localize' ) );
	}

	/**
	 * Render the widget in a container div.
	 *
	 * @param $atts
	 * @return string
	 */
	public function render( $atts ) {
		$atts = $this->parse_networks( $atts );

		$output  = '<div class="' . esc_attr( implode( ' ', $this->get_block_classes( $atts ) ) ) . '">';
		$output .= $this->shortcode( $atts );
		$output .= '</div>';

		return $output;
	}

	/**
	 * Since buttons are chosen differently than our settings, we have to compare and parse.
	 *
	 * @param $atts
	 * @return string
	 */
	private function parse_networks( &$atts ) {
		$buttons  = array();
		$networks = $this->networks();
		foreach ( $atts as $key => $value ) {
			if ( array_key_exists( $key, $networks ) ) {
				if ( $value ) {
					$buttons[] = $key;
				}
				unset( $atts[ $key ] );
			}
		}
		$atts['buttons'] = $buttons;

		return $atts;
	}

	/**
	 * Gets the block HTML classes.
	 *
	 * @since 3.2.2
	 * @param array $atts
	 * @return array
	 */
	private function get_block_classes( $atts ) {
		$classes = array(
			"wp-block-{$this->block}",
		);
		if ( ! empty( $atts['blockAlignment'] ) ) {
			$classes[] = 'align' . $atts['blockAlignment'];
		}
		if ( ! empty( $atts['className'] ) ) {
			$additional_classes = explode( ' ', $atts['className'] );
			if ( $additional_classes ) {
				$classes = array_merge( $classes, $additional_classes );
			}
		}

		return array_filter( array_unique( array_map( 'sanitize_html_class', $classes ) ) );
	}

	/**
	 * Register the block script and style.
	 */
	public function register_script_style() {
		wp_register_style( $this->block . '-block', plugin_dir_url( dirname( __FILE__ ) ) . 'css/scriptlesssocialsharing-block.css', array(), SCRIPTLESSOCIALSHARING_VERSION, 'all' );
		$minify  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$version = $minify ? SCRIPTLESSOCIALSHARING_VERSION : SCRIPTLESSOCIALSHARING_VERSION . current_time( 'gmt' );
		wp_register_script(
			$this->block . '-block',
			plugin_dir_url( dirname( __FILE__ ) ) . "js/block{$minify}.js",
			array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render' ),
			$version,
			false
		);
	}

	/**
	 * Localize.
	 */
	public function localize() {
		wp_localize_script( $this->block . '-block', 'ScriptlessBlock', $this->get_localization_data() );
	}

	/**
	 * Get the data for localizing everything.
	 * @return array
	 */
	protected function get_localization_data() {
		return array(
			'block'       => $this->name,
			'title'       => __( 'Scriptless Social Sharing', 'scriptless-social-sharing' ),
			'description' => __( 'Add sharing buttons anywhere.', 'scriptless-social-sharing' ),
			'keywords'    => array(
				__( 'Social Share', 'scriptless-social-sharing' ),
				__( 'Sharing Buttons', 'scriptless-social-sharing' ),
			),
			'panels'      => array(
				'heading' => array(
					'title'       => __( 'Optional Settings', 'scriptless-social-sharing' ),
					'initialOpen' => true,
					'attributes'  => $this->fields(),
				),
				'buttons' => array(
					'title'       => __( 'Button Settings', 'scriptless-social-sharing' ),
					'initialOpen' => false,
					'attributes'  => $this->networks(),
				),
			),
			'icon'        => 'share-alt',
			'category'    => 'widgets',
		);
	}

	/**
	 * Get the fields for the block.
	 * @return array
	 */
	private function fields() {
		return array(
			'blockAlignment' => array(
				'type'    => 'string',
				'default' => '',
			),
			'className'      => array(
				'type'    => 'string',
				'default' => '',
			),
			'heading'        => array(
				'type'    => 'string',
				'default' => $this->get_setting( 'heading' ),
				'label'   => __( 'Heading', 'scriptless-social-sharing' ),
			),
		);
	}

	/**
	 * Get the checkbox fields for the networks.
	 *
	 * @return array
	 */
	private function networks() {
		$networks = include plugin_dir_path( dirname( __FILE__ ) ) . 'settings/networks.php';
		$fields   = array();
		$i        = 0;
		$setting  = $this->get_setting( 'buttons' );
		foreach ( $networks as $network ) {
			if ( 'sms' === $network['name'] && empty( $setting['sms'] ) ) {
				continue;
			}
			$fields[ $network['name'] ] = array(
				'type'    => 'boolean',
				'default' => 0,
				'label'   => empty( $network['label'] ) ? $network['name'] : $network['label'],
				'method'  => 'checkbox',
			);
			if ( ! $i ) {
				$fields[ $network['name'] ]['heading'] = __( 'Leave all checkboxes empty to use the buttons set in the plugin settings. Use the checkboxes to override the plugin settings.', 'scriptless-social-sharing' );
				$i++;
			}
		}
		$fields['pinterest']['label'] .= __( ' (will not show if there is no image)', 'scriptless-social-sharing' );

		return $fields;
	}
}
