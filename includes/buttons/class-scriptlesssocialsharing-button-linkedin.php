<?php

/**
 * Class to correctly build the Linkedin URL.
 * Class ScriptlessSocialSharingButtonLinkedin
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonLinkedin extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		$query_args = array(
			'mini'   => true,
			'url'    => $this->get_permalink(),
			'title'  => $this->attributes['title'],
			'source' => $this->attributes['home'],
		);
		if ( $this->description() ) {
			$query_args['summary'] = $this->description();
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
		return 'https://www.linkedin.com/shareArticle';
	}

	/**
	 * Override parent description method to ensure string return.
	 * 
	 * @param string $description Optional description text
	 * @return string
	 */
	protected function description( $description = '' ) {
		return parent::description( $description ) ?? '';
	}
}
