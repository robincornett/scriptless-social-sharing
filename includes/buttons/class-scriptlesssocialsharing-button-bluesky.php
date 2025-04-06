<?php

/**
 * Class to correctly build the Bluesky URL.
 * Class ScriptlessSocialSharingButtonBluesky
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonBluesky extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @since 3.3.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$query_args = array(
			'text' => $this->get_twitter_title( $this->attributes['title'] ) . ': ' . $this->get_permalink(),
		);

		return $query_args;
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.3.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://bsky.app/intent/compose';
	}

	/**
	 * Get the title/text.
	 * @since 3.3.0
	 *
	 * @param $title
	 * @return mixed|void|null
	 */
	private function get_twitter_title( $title ) {
		$yoast = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		if ( $yoast ) {
			$title = $yoast;
		}

		return apply_filters( 'scriptlesssocialsharing_twitter_text', $title );
	}
}
