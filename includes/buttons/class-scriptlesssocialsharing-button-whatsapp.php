<?php

/**
 * Class to correctly build the WhatsApp URL.
 * Class ScriptlessSocialSharingButtonWhatsapp
 *
 * @since 2.3.0
 */
class ScriptlessSocialSharingButtonWhatsApp extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'text' => $this->attributes['title'] . ' &#8212; ' . $this->get_permalink(),
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://wa.me';
	}
}
