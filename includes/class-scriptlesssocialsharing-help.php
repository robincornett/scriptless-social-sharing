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
				'id'      => 'scriptlesssocialsharing_buttons-help',
				'title'   => __( 'Button Settings', 'scriptless-social-sharing' ),
				'content' => $this->buttons() . $this->heading(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_networks-help',
				'title'   => __( 'Network Settings', 'scriptless-social-sharing' ),
				'content' => $this->twitter() . $this->email(),
			),
		);
	}

	protected function styles() {
		$help  = '<p>' . __( 'SSS loads three style related items: 1) the main stylesheet to handle the button layouts and colors; 2) Font Awesome (the font itself); and 3) a small Font Awesome related stylesheet to add the icons to the buttons.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'You can use as much or as little of the plugin styles as you like. For example, if your site already loads Font Awesome, don\'t load it again here.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Note that the button styles options--text/icons, container width, and padding--will take effect only if the main stylesheet is enabled.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'The buttons are output as a table. The default is for them to span the width of the content space, but you can set it to automatically be just the size of the buttons instead. Note that on sites with many buttons and not much space, this option may result in buttons that overflow the content area assigned to them.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function heading() {

		$help  = '<h3>' . __( 'Heading', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'This is the heading above the sharing buttons.', 'scriptless-social-sharing' ) . '</p>';
		return $help;
	}

	protected function buttons() {

		$help  = '<p>' . __( 'Pick which social network buttons you would like to show. Custom buttons can be added via a filter.', 'scriptless-social-sharing' ) . '</p>';

		$help .= '<h3>' . __( 'Content Types', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'By default, sharing buttons are added only to posts, but you can add them to any custom content types on your site.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function twitter() {

		$help  = '<h3>' . __( 'Twitter Handle', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'The Twitter username you want to be credited for each tweet/post.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	protected function email() {

		$help  = '<h3>' . __( 'Email Subject', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'The post/page title will be added to the subject.', 'scriptless-social-sharing' ) . '</p>';
		return $help;
	}
}
