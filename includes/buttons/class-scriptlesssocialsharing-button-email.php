<?php

/**
 * Class to correctly build the email URL.
 * Class ScriptlessSocialSharingButtonEmail
 *
 * @since 2.2.0
 */
class ScriptlessSocialSharingButtonEmail extends ScriptlessSocialSharingButton {

	/**
	 * Get the button query args.
	 * @ since 3.0.0
	 *
	 * @return array
	 */
	protected function get_query_args() {
		return array(
			'body'    => $this->email_body() . ' ' . $this->get_permalink(),
			'subject' => $this->email_subject() . ' ' . $this->attributes['title'],
		);
	}

	/**
	 * Get the base part of the URL.
	 * @since 3.0.0
	 *
	 * @return mixed
	 */
	protected function get_url_base() {
		return 'mailto:';
	}

	/**
	 * body text for the email button
	 * @return string can be modified via filter
	 */
	protected function email_body() {
		return apply_filters( 'scriptlesssocialsharing_email_body', $this->setting['email_body'] );
	}

	/**
	 * subject line for the email button
	 * @return string can be modified via filter
	 */
	protected function email_subject() {
		return apply_filters( 'scriptlesssocialsharing_email_subject', $this->setting['email_subject'] );
	}
}
