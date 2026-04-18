<?php
/**
 * Scriptless Social Sharing Fallback Button
 */

namespace ScriptlessSocialSharing\Buttons;

/**
 * Class to correctly build a fallback sharing URL.
 * Class Fallback
 *
 * @since 3.1.2
 */
class Fallback extends Button {

	/**
	 * Get the button query args.
	 * @ since 3.1.2
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array();
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.1.2
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return '';
	}
}
