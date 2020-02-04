<?php

/**
 * Class to correctly build the SMS URL.
 * Class ScriptlessSocialSharingButtonSms
 *
 * @since 3.0.0
 */
class ScriptlessSocialSharingButtonSms extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'&amp;body' => $this->attributes['title'] . ' ' . $this->get_permalink(),
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'sms:';
	}
}
