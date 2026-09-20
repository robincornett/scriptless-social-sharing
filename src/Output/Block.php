<?php
/**
 * Scriptless Social Sharing Output Block
 */

namespace ScriptlessSocialSharing\Output;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Class Block
 */
class Block extends Shortcode {

	/**
	 * The block name.
	 *
	 * @var string
	 */
	protected $name = 'scriptlesssocialsharing/buttons';

	/**
	 * The block slug.
	 *
	 * @var string
	 */
	protected $block = 'scriptless-social-sharing-buttons';

	/**
	 * The plugin setting.
	 *
	 * @var array
	 */
	protected $setting;

	/**
	 * Register our block type.
	 */
	public function init() {
		add_filter( 'block_type_metadata', array( $this, 'set_heading_default' ) );
		register_block_type(
			SCRIPTLESSOCIALSHARING_DIR . '/assets/build/blocks/buttons',
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
		add_filter( 'render_block_data', array( $this, 'migrate_block_alignment' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'localize' ) );
	}

	/**
	 * Carry the alignment saved by earlier versions over to the align support.
	 *
	 * Blocks saved before 4.0.0 stored alignment in their own attribute,
	 * which the align support does not know about, so those posts would lose their
	 * alignment until they were opened and saved again.
	 *
	 * @since 4.0.0
	 * @param array $parsed_block The parsed block.
	 * @return array
	 */
	public function migrate_block_alignment( $parsed_block ) {
		if ( $this->name !== $parsed_block['blockName'] ) {
			return $parsed_block;
		}
		if ( ! empty( $parsed_block['attrs']['blockAlignment'] ) && empty( $parsed_block['attrs']['align'] ) ) {
			$parsed_block['attrs']['align'] = $parsed_block['attrs']['blockAlignment'];
		}

		return $parsed_block;
	}

	/**
	 * Take the heading default from the plugin settings.
	 *
	 * Passing attributes to register_block_type() would replace the whole block.json
	 * set, which breaks the block renderer endpoint, so the one dynamic default is
	 * filtered into the metadata instead.
	 *
	 * @since 4.0.0
	 * @param array $metadata The block metadata.
	 * @return array
	 */
	public function set_heading_default( $metadata ) {
		if ( $this->name === $metadata['name'] ) {
			$metadata['attributes']['heading']['default'] = $this->get_setting( 'heading' );
		}

		return $metadata;
	}

	/**
	 * Render the widget in a container div.
	 *
	 * @param array $atts The block attributes.
	 * @return string
	 */
	public function render( $atts ) {
		$atts = $this->parse_networks( $atts );
		if ( ! wp_style_is( 'scriptlesssocialsharing' ) ) {
			$enqueue = new \ScriptlessSocialSharing\Enqueue( $this->get_setting(), $atts['buttons'], $this->can_do_buttons() );
			$enqueue->load_styles();
		}

		// The shortcode prints the stylesheet, which would corrupt the block renderer's JSON response.
		ob_start();
		$buttons = $this->shortcode( $atts );
		$styles  = ob_get_clean();

		$output  = '<div ' . get_block_wrapper_attributes( array( 'class' => "wp-block-{$this->block}" ) ) . '>';
		$output .= $styles . $buttons;
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
	 * Localize.
	 */
	public function localize() {
		wp_add_inline_script(
			generate_block_asset_handle( $this->name, 'editorScript' ),
			'var ScriptlessBlock = ' . wp_json_encode( $this->get_localization_data() ) . ';',
			'before'
		);
	}

	/**
	 * Get the data for localizing everything.
	 * @return array
	 */
	protected function get_localization_data() {
		return array(
			'panels' => array(
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
		);
	}

	/**
	 * Get the fields for the block.
	 * @return array
	 */
	private function fields() {
		return array(
			'heading' => array(
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
		$networks = include SCRIPTLESSOCIALSHARING_DIR . '/includes/settings/networks.php';
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
				++$i;
			}
		}
		$fields['pinterest']['label'] .= __( ' (will not show if there is no image)', 'scriptless-social-sharing' );

		return $fields;
	}
}
