<?php

/**
 * Class ScriptlessSocialSharingHelp
 * @package   ScriptlessSocialSharing
 * @copyright 2016-2019 Robin Cornett
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

	/**
	 * Define the help tabs.
	 * @return array
	 */
	protected function define_tabs() {
		return array(
			array(
				'id'      => 'scriptlesssocialsharing_styles-help',
				'title'   => __( 'Style Settings', 'scriptless-social-sharing' ),
				'content' => $this->styles(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_buttons-help',
				'title'   => __( 'Button Settings', 'scriptless-social-sharing' ),
				'content' => $this->heading() . $this->buttons() . $this->button_order(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_types-help',
				'title'   => __( 'Content Types', 'scriptless-social-sharing' ),
				'content' => $this->content_types(),
			),
			array(
				'id'      => 'scriptlesssocialsharing_networks-help',
				'title'   => __( 'Network Settings', 'scriptless-social-sharing' ),
				'content' => $this->twitter() . $this->email(),
			),
		);
	}

	/**
	 * Description for the styles tab.
	 * @return string
	 */
	protected function styles() {
		$help  = '<p>' . __( 'SSS loads three style related items: 1) the main stylesheet to handle the button layouts and colors; 2) Font Awesome (the font itself); and 3) a small Font Awesome related stylesheet to add the icons to the buttons.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'You can use as much or as little of the plugin styles as you like. For example, if your site already loads Font Awesome, don\'t load it again here.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Note that the button styles options--text/icons, container width, and padding--will take effect only if the main stylesheet is enabled.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'The buttons are output as a table. The default is for them to span the width of the content space, but you can set it to automatically be just the size of the buttons instead. Note that on sites with many buttons and not much space, this option may result in buttons that overflow the content area assigned to them.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	/**
	 * Description for the heading setting.
	 * @return string
	 */
	protected function heading() {

		$help  = '<h3>' . __( 'Heading', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'This is the heading above the sharing buttons.', 'scriptless-social-sharing' ) . '</p>';
		return $help;
	}

	/**
	 * Description for the social network buttons.
	 * @return string
	 */
	protected function buttons() {
		$help  = '<h3>' . __( 'Sharing Buttons', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'Pick which social network buttons you would like to show. Custom buttons can be added via a filter.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	/**
	 * Description for the button order.
	 * @return string
	 */
	protected function button_order() {
		$help  = '<h3>' . __( 'Button Order', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'Buttons can be reordered either by changing the number input values, or by dragging and dropping the buttons. If the number input values are changed, drag/drop functionality will be disabled until the settings have been saved.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	/**
	 * Description for the content types setting.
	 * @return string
	 */
	protected function content_types() {
		$help  = '<p>' . __( 'By default, sharing buttons are added only to posts, but you can add them to any custom content types on your site. For each content type to which you plan to add sharing buttons via code, select manual placement.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'If you want to place sharing buttons manually via the shortcode, you can do that regardless of what location settings are checked or not.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	/**
	 * Description for the twitter handle setting.
	 * @return string
	 */
	protected function twitter() {

		$help  = '<h3>' . __( 'Twitter Handle', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'The Twitter username you want to be credited for each tweet/post.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<p>' . __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}

	/**
	 * Description for the email subject setting.
	 * @return string
	 */
	protected function email() {

		$help  = '<h3>' . __( 'Email Subject', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'The post/page title will be added to the subject.', 'scriptless-social-sharing' ) . '</p>';
		$help .= '<h3>' . __( 'Email Content', 'scriptless-social-sharing' ) . '</h3>';
		$help .= '<p>' . __( 'Keep this simple--whatever you put here is added to your email button markup. The link to the post will be added at the end of the email content.', 'scriptless-social-sharing' ) . '</p>';

		return $help;
	}
}
