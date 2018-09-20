<?php

/**
 * Class to correctly build the Google+ URL.
 * Class ScriptlessSocialSharingButtonGoogle
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonGoogle extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for Google+.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		return add_query_arg(
			'url',
			$this->get_permalink( 'google' ),
			'https://plus.google.com/share'
		);
	}
}
