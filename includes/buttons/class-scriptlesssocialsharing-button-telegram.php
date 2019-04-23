<?php
/**
 * Copyright (c) 2019 Robin Cornett
 */

/**
 * Class to correctly build the Telegram URL.
 * Class ScriptlessSocialSharingButtonTelegram
 *
 * @since 2.3.0
 */
class ScriptlessSocialSharingButtonTelegram extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'url'  => $this->get_permalink(),
			'text' => $this->attributes['title'],
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://telegram.me/share/url';
	}
}
