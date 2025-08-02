<?php

/**
 * Class ScriptlessSocialSharingOutputAttributes
 * @since 3.0.0
 */
class ScriptlessSocialSharingOutputAttributes {

	/**
	 * The plugin setting.
	 * @var array $setting
	 */
	protected $setting;

	/**
	 * The attributes for the sharing button.
	 * @var array $attributes
	 */
	protected $attributes;

	/**
	 * ScriptlessSocialSharingOutputAttributes constructor.
	 *
	 * @param $setting
	 */
	public function __construct( $setting ) {
		$this->setting = $setting;
	}

	/**
	 * Get the array of attributes for sharing buttons.
	 * @return array
	 */
	public function get_attributes() {
		if ( isset( $this->attributes ) && is_singular() ) {
			return $this->attributes;
		}
		$this->attributes = $this->attributes();

		return $this->attributes;
	}

	/**
	 * create URL attributes for buttons
	 * @return array attributes
	 */
	protected function attributes() {
		return array(
			'title'     => $this->title(),
			'permalink' => get_the_permalink(),
			'home'      => home_url(),
			'image'     => $this->setting['buttons']['pinterest'] ? $this->featured_image() : '',
			'pinterest' => get_post_meta( get_the_ID(), '_scriptlesssocialsharing_pinterest', true ),
			'post_id'   => get_the_ID(),
		);
	}

	/**
	 * Set the post title for sharing. Decodes HTML character entities,
	 * then encodes for the URL.
	 * @return string
	 */
	protected function title() {
		$title = the_title_attribute( array( 'echo' => false ) ) ?? '';
		if ( $title ) {
			$title = html_entity_decode( $title );
		}
		return apply_filters( 'scriptlesssocialsharing_posttitle', $title );
	}

	/**
	 * retrieve the featured image
	 * @return string if there is a featured image, return the ID
	 */
	protected function featured_image() {
		return has_post_thumbnail() ? get_post_thumbnail_id() : $this->get_fallback_image();
	}

	/**
	 * If there is no featured image, use the first image attached to the post/page as the fallback.
	 * @return bool
	 */
	protected function get_fallback_image() {
		$image_ids = array_keys(
			get_children(
				array(
					'post_parent'    => get_the_ID(),
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'orderby'        => 'menu_order',
					'order'          => 'ASC',
					'numberposts'    => 1,
				)
			)
		);

		if ( isset( $image_ids[0] ) ) {
			return $image_ids[0];
		}

		return false;
	}
}
