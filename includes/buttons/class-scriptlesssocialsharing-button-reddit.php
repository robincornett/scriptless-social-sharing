<?php

/**
 * Class to correctly build the Reddit URL.
 * Class ScriptlessSocialSharingButtonReddit
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonReddit extends ScriptlessSocialSharingOutput {

	/**
	 * Get the Reddit URL.
	 * @param $attributes
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		return add_query_arg(
			'url',
			$this->get_permalink( 'reddit' ),
			'https://www.reddit.com/submit'
		);
	}
}
