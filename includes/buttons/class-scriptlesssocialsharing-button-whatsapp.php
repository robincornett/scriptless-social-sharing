<?php

/**
 * Class to correctly build the WhatsApp URL.
 * Class ScriptlessSocialSharingButtonWhatsapp
 *
 * @since 2.3.0
 */
class ScriptlessSocialSharingButtonWhatsapp extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for WhatsApp.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		$query_args = array(
			'text' => $attributes['title'] . ' &#8212; ' . $this->get_permalink( 'whatsapp' ),
		);

		return add_query_arg(
			$query_args,
			'https://wa.me'
		);
	}
}
