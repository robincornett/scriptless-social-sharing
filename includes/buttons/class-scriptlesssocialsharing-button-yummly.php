<?php

/**
 * Class ScriptlessSocialSharingButtonYummly
 */
class ScriptlessSocialSharingButtonYummly extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @return array|mixed
	 * @since 3.0.0
	 */
	protected function get_query_args() {
		return array(
			'url'     => $this->get_permalink(),
			'title'   => $this->attributes['title'],
			'yumtype' => 'button',
		);
	}

	/**
	 * Get the button URL base.
	 * @return mixed|string
	 * @since 3.0.0
	 */
	protected function get_url_base() {
		return 'https://www.yummly.com/urb/verify';
	}
}
