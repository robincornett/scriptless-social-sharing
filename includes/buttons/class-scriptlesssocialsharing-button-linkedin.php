<?php

/**
 * Class to correctly build the Linkedin URL.
 * Class ScriptlessSocialSharingButtonLinkedin
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonLinkedin extends ScriptlessSocialSharingOutput {

	/**
	 * Get the Linkedin URL.
	 * @param $attributes array
	 *
	 * @return string
	 * @since 2.0.0
	 */
	protected function get_url( $attributes ) {
		$query_args = array(
			'mini'   => true,
			'url'    => $this->get_permalink( 'linkedin' ),
			'title'  => $attributes['title'],
			'source' => $attributes['home'],
		);
		if ( $this->description() ) {
			$query_args['summary'] = $this->description();
		}
		return add_query_arg(
			$query_args,
			'https://www.linkedin.com/shareArticle'
		);
	}
}
