<?php
/**
 * Scriptless Social Sharing Reddit Button
 */

namespace ScriptlessSocialSharing\Buttons;

/**
 * Class to correctly build the Reddit URL.
 * Class Reddit
 *
 * @since 2.2.0
 */
class Reddit extends Button {

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
