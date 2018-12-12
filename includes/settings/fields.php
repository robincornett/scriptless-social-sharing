<?php

/**
 * Define all of the plugin settings.
 */
return array(
	array(
		'id'       => 'styles',
		'title'    => __( 'Plugin Styles', 'scriptless-social-sharing' ),
		'callback' => 'do_checkbox_array',
		'section'  => 'styles',
		'choices'  => array(
			'plugin'   => __( 'Load the main stylesheet? (colors and layout)', 'scriptless-social-sharing' ),
			'font'     => __( 'Load Font Awesome? (just the font)', 'scriptless-social-sharing' ),
			'font_css' => __( 'Use plugin Font Awesome CSS? (adds the icons to the buttons)', 'scriptless-social-sharing' ),
		),
		'clear'    => true,
	),
	array(
		'id'          => 'heading',
		'title'       => __( 'Heading', 'scriptless-social-sharing' ),
		'callback'    => 'do_text_field',
		'section'     => 'general',
		'description' => __( 'Heading above sharing buttons', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'buttons',
		'title'    => __( 'Buttons', 'scriptless-social-sharing' ),
		'callback' => 'do_checkbox_array',
		'section'  => 'general',
		'choices'  => $this->get_buttons(),
	),
	array(
		'id'       => 'order',
		'title'    => __( 'Button Order', 'scriptless-social-sharing' ),
		'callback' => 'do_custom_order',
		'section'  => 'general',
		'choices'  => $this->get_buttons(),
		'intro'    => __( 'Reorder the buttons by dragging/dropping, or by using the number inputs.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'twitter_handle',
		'title'       => __( 'Twitter Handle', 'scriptless-social-sharing' ),
		'callback'    => 'do_text_field',
		'section'     => 'networks',
		'description' => __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'email_subject',
		'title'       => __( 'Email Subject', 'scriptless-social-sharing' ),
		'callback'    => 'do_text_field',
		'section'     => 'networks',
		'description' => __( 'The post title will be appended to whatever you add here.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'email_body',
		'title'       => __( 'Email Content', 'scriptless-social-sharing' ),
		'callback'    => 'do_textarea_field',
		'section'     => 'networks',
		'description' => __( 'Keep this simple--whatever you put here is added to your email button markup. The link to the post will be added at the end of the email content.', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'post_types',
		'title'    => __( 'Content Types', 'scriptless-social-sharing' ),
		'callback' => 'do_content_types',
		'section'  => 'content_types',
		'choices'  => $this->post_type_choices(),
		'intro'    => __( 'Leave all options unchecked for no buttons. Before/after content are the traditional Scriptless Social Sharing locations (within the post/entry content). Checking manual placement will allow the plugin styles to load as needed, if you are adding the buttons using code. You do not need to check any settings to use the shortcode.', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'button_style',
		'title'    => __( 'Button Styles', 'scriptless-social-sharing' ),
		'callback' => 'do_radio_buttons',
		'section'  => 'styles',
		'buttons'  => array(
			0 => __( 'Icon Only', 'scriptless-social-sharing' ),
			1 => __( 'Icon Plus Text', 'scriptless-social-sharing' ),
		),
		'legend'   => __( 'Button styles for larger screens', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'table_width',
		'title'    => __( 'Button Container Width', 'scriptless-social-sharing' ),
		'callback' => 'do_radio_buttons',
		'section'  => 'styles',
		'buttons'  => array(
			'full' => __( 'Full Width', 'scriptless-social-sharing' ),
			'auto' => __( 'Auto', 'scriptless-social-sharing' ),
		),
		'legend'   => __( 'Width of button container', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'button_padding',
		'title'    => __( 'Button Padding', 'scriptless-social-sharing' ),
		'callback' => 'do_number',
		'section'  => 'styles',
		'label'    => __( ' pixels', 'scriptless-social-sharing' ),
		'min'      => 0,
		'max'      => 400,
	),
);
