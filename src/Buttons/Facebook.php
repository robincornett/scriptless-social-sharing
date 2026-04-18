<?php
/**
 * Scriptless Social Sharing Facebook Button
 */

namespace ScriptlessSocialSharing\Buttons;

/**
 * Class to correctly build the Facebook URL.
 * Class Facebook
 *
 * @since 2.2.0
 */
class Facebook extends Button {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'u' => $this->get_permalink(),
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://www.facebook.com/sharer/sharer.php';
	}
}
