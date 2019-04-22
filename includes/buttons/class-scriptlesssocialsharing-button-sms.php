<?php

/**
 * Class to correctly build the SMS URL.
 * Class ScriptlessSocialSharingButtonSMS
 *
 * @since 3.0.0
 */
class ScriptlessSocialSharingButtonSMS extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for SMS.
	 *
	 * @param $attributes
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		$query_args = array(
			'body' => $attributes['title'] . ' ' . $attributes['permalink'],
		);

		return add_query_arg(
			$query_args,
			'sms:'
		);
	}
}
