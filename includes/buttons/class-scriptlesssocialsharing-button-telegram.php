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
class ScriptlessSocialSharingButtonTelegram extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for Telegram.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.3.0
	 */
	protected function get_url( $attributes ) {
		$query_args = array(
			'url'  => $this->get_permalink( 'telegram' ),
			'text' => $attributes['title'],
		);

		return add_query_arg(
			$query_args,
			'https://telegram.me/share/url'
		);
	}
}
