<?php

/**
 * Class to correctly build the Twitter URL.
 * Class ScriptlessSocialSharingButtonTwitter
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonTwitter extends ScriptlessSocialSharingOutput {

	/**
	 * Get the URL for Twitter.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		$yoast         = get_post_meta( get_the_ID(), '_yoast_wpseo_twitter-title', true );
		$twitter_title = $yoast ? $yoast : $attributes['title'];
		$query_args    = array(
			'text' => $twitter_title,
			'url'  => $this->get_permalink( 'twitter' ),
		);
		if ( $this->twitter_handle() ) {
			$query_args['via']     = $this->twitter_handle();
			$query_args['related'] = $this->twitter_handle();
		}

		return add_query_arg(
			$query_args,
			'https://twitter.com/intent/tweet'
		);
	}

	/**
	 * add twitter handle to URL
	 * @return string twitter handle (default is empty)
	 */
	protected function twitter_handle() {
		$setting = $this->get_setting();

		return apply_filters( 'scriptlesssocialsharing_twitter_handle', $setting['twitter_handle'] );
	}
}
