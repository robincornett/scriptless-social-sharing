<?php

/**
 * Class to correctly build the Hatena Bookmark URL.
 * Class ScriptlessSocialSharingButtonHatena
 *
 * @since 3.2.0
 */
class ScriptlessSocialSharingButtonHatena extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @since 3.2.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$query_args = array(
			'url'    => $this->get_permalink(),
			'btitle' => $this->attributes['title'],
		);
		if ( $this->description() ) {
			$query_args['summary'] = $this->description();
		}

		return $query_args;
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.2.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://b.hatena.ne.jp/entry/panel/';
	}
}
