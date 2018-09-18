<?php

class ScriptlessSocialSharingButtonFacebook extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for Facebook.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		return add_query_arg(
			'u',
			$this->get_permalink( 'facebook' ),
			'https://www.facebook.com/sharer/sharer.php'
		);
	}
}
