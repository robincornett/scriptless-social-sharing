<?php

/**
 * Class to correctly build the Pocket URL.
 * Class ScriptlessSocialSharingButtonPocket
 *
 * @since 2.3.0
 */
class ScriptlessSocialSharingButtonPocket extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'url'   => $this->get_permalink(),
			'title' => $this->attributes['title'],
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://getpocket.com/save';
	}
}
