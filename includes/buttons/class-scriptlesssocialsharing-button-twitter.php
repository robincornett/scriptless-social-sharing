<?php

/**
 * Class to correctly build the Twitter URL.
 * Class ScriptlessSocialSharingButtonTwitter
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonTwitter extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$query_args = array(
			'text' => $this->get_twitter_title( $this->attributes['title'] ),
			'url'  => $this->get_permalink(),
		);
		if ( $this->twitter_handle() ) {
			$query_args['via']     = $this->twitter_handle();
			$query_args['related'] = $this->twitter_handle();
		}

		return $query_args;
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'https://twitter.com/intent/tweet';
	}

	/**
	 * Get the twitter title/text.
	 * @since 3.0.0
	 *
	 * @param $title
	 * @return mixed|void|null
	 */
	private function get_twitter_title( $title ) {
		$yoast = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true ) ?? '';
		if ( $yoast ) {
			$title = $yoast;
		}

		return apply_filters( 'scriptlesssocialsharing_twitter_text', $title ?? '' );
	}

	/**
	 * add twitter handle to URL
	 * @return string twitter handle (default is empty)
	 */
	private function twitter_handle() {
		return apply_filters( 'scriptlesssocialsharing_twitter_handle', $this->setting['twitter_handle'] );
	}
}
