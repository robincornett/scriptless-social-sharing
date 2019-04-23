<?php

/**
 * Class to correctly build the Reddit URL.
 * Class ScriptlessSocialSharingButtonReddit
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonReddit extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'url' => $this->get_permalink(),
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://www.reddit.com/submit';
	}
}
