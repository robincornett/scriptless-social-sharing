<?php

/**
 * Class to correctly build the Pocket URL.
 * Class ScriptlessSocialSharingButtonPocket
 *
 * @since 2.3.0
 */
class ScriptlessSocialSharingButtonPocket extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for Pocket.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.3.0
	 */
	protected function get_url( $attributes ) {
		$query_args = array(
			'url'   => $this->get_permalink( 'pocket' ),
			'title' => $attributes['title'],
		);

		return add_query_arg(
			$query_args,
			'https://getpocket.com/save'
		);
	}
}
