<?php

/**
 * Class ScriptlessSocialSharingHelp
 * @package ScriptlessSocialSharing
 * @copyright 2016 Robin Cornett
 */
class ScriptlessSocialSharingHelp {

	/**
	 * Help tab for settings screen
	 *
	 * @since 1.0.0
	 */
	public function help() {

		$screen    = get_current_screen();
		$help_tabs = $this->define_tabs();
		if ( ! $help_tabs ) {
			return;
		}
		foreach ( $help_tabs as $tab ) {
			$screen->add_help_tab( $tab );
		}
	}

	protected function define_tabs() {
		return array(
			array(
				'id'      => 'scriptlesssocialsharing_styles-help',
				'title'   => __( 'Plugin Styles', 'scriptless-social-sharing' ),
				'content' => $this->styles(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_heading-help',
				'title'   => __( 'Heading', 'scriptless-social-sharing' ),
				'content' => $this->heading(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_buttons-help',
				'title'   => __( 'Buttons', 'scriptless-social-sharing' ),
				'content' => $this->buttons(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_twitter-help',
				'title'   => __( 'Twitter Handle', 'scriptless-social-sharing' ),
				'content' => $this->twitter(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_email-help',
				'title'   => __( 'Email Subject', 'scriptless-social-sharing' ),
				'content' => $this->email(),
			),
		);
	}

	protected function styles() {
		$help  = '<p>' . __( 'SSS loads three style related items: 1) the main stylesheet to handle the button layouts and colors; 2) Font Awesome (the font itself); and 3) a small Font Awesome related stylesheet to add the icons to the buttons.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'You can use as much or as little of the plugin styles as you like. For example, if your site already loads Font Awesome, don\'t load it again here.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Note that the button styles option will take effect only if the main stylesheet is enabled.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function heading() {

		return '<p>' . __( 'This is the heading above the sharing buttons.', 'scriptless-social-sharing' ) . '</p>';
	}

	protected function buttons() {

		$help  = '<p>' . __( 'Pick which social network buttons you would like to show. Custom buttons can be added via a filter.', 'scriptless-social-sharing' ) . '</p>';

		$help .= '<h3>' . __( 'Content Types', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'By default, sharing buttons are added only to posts, but you can add them to any custom content types on your site.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function twitter() {

		$help  = '<p>' . __( 'The Twitter username you want to be credited for each tweet/post.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function email() {

		return '<p>' . __( 'The post/page title will be added to the subject.', 'scriptless-social-sharing' ) . '</p>';
	}
}
