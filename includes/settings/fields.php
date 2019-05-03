<?php

/**
 * Define all of the plugin settings.
 */
$fields = array(
	array(
		'id'          => 'icons',
		'title'       => __( 'Button Icons', 'scriptless-social-sharing' ),
		'type'        => 'radio',
		'section'     => 'icons',
		'choices'     => array(
			'svg'  => __( 'Use SVG Icons for sharing icons', 'scriptless-social-sharing' ),
			'font' => __( 'Use a webfont for sharing icons', 'scriptless-social-sharing' ),
			'none' => __( 'Use custom icons', 'scriptless-social-sharing' ),
		),
		'legend'      => __( 'Choose whether to output social media icons as SVG or icon font', 'scriptless-social-sharing' ),
		'description' => __( 'Choose how social network icons will be displayed on your sharing buttons. Choose "custom icons" if you are adding icons your own way.', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'styles',
		'title'   => __( 'Plugin Styles', 'scriptless-social-sharing' ),
		'type'    => 'checkbox_array',
		'section' => 'styles',
		'choices' => array(
			'plugin' => __( 'Load the main stylesheet? (colors and layout)', 'scriptless-social-sharing' ),
			'font'   => __( 'Load Font Awesome? (this is not needed if you are using the SVG option)', 'scriptless-social-sharing' ),
		),
		'clear'   => true,
	),
	array(
		'id'          => 'heading',
		'title'       => __( 'Heading', 'scriptless-social-sharing' ),
		'type'        => 'text',
		'section'     => 'general',
		'description' => __( 'Heading above sharing buttons', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'buttons',
		'title'   => __( 'Buttons', 'scriptless-social-sharing' ),
		'type'    => 'checkbox_array',
		'section' => 'general',
		'choices' => array( $this, 'get_buttons' ),
	),
	array(
		'id'       => 'order',
		'title'    => __( 'Button Order', 'scriptless-social-sharing' ),
		'callback' => 'do_custom_order',
		'section'  => 'general',
		'choices'  => array( $this, 'get_buttons' ),
		'intro'    => __( 'Reorder the buttons by dragging/dropping, or by using the number inputs.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'twitter_handle',
		'title'       => __( 'Twitter Handle', 'scriptless-social-sharing' ),
		'type'        => 'text',
		'section'     => 'networks',
		'description' => __( 'Do not include the @ -- just the user name.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'email_subject',
		'title'       => __( 'Email Subject', 'scriptless-social-sharing' ),
		'type'        => 'text',
		'section'     => 'networks',
		'description' => __( 'The post title will be appended to whatever you add here.', 'scriptless-social-sharing' ),
	),
	array(
		'id'          => 'email_body',
		'title'       => __( 'Email Content', 'scriptless-social-sharing' ),
		'type'        => 'textarea',
		'section'     => 'networks',
		'description' => __( 'Keep this simple--whatever you put here is added to your email button markup. The link to the post will be added at the end of the email content.', 'scriptless-social-sharing' ),
	),
	array(
		'id'       => 'post_types',
		'title'    => __( 'Content Types', 'scriptless-social-sharing' ),
		'callback' => 'do_content_types',
		'section'  => 'content_types',
		'choices'  => array( $this, 'post_type_choices' ),
		'intro'    => __( 'Leave all options unchecked for no buttons. Before/after content are the traditional Scriptless Social Sharing locations (within the post/entry content). Checking manual placement will allow the plugin styles to load as needed, if you are adding the buttons using code. You do not need to check any settings to use the shortcode.', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'button_style',
		'title'   => __( 'Button Output', 'scriptless-social-sharing' ),
		'type'    => 'radio',
		'section' => 'icons',
		'choices' => array(
			0 => __( 'Icon Only', 'scriptless-social-sharing' ),
			1 => __( 'Icon Plus Text', 'scriptless-social-sharing' ),
			2 => __( 'Text Only', 'scriptless-social-sharing' ),
		),
		'legend'  => __( 'Button styles for larger screens', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'css_style',
		'title'   => __( 'Button Container CSS', 'scriptless-social-sharing' ),
		'type'    => 'radio',
		'section' => 'styles',
		'choices' => array(
			'flex'  => __( 'Flexbox', 'scriptless-social-sharing' ),
			'table' => __( 'Table', 'scriptless-social-sharing' ),
		),
		'legend'  => __( 'CSS options for the button container', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'table_width',
		'title'   => __( 'Button Container Width', 'scriptless-social-sharing' ),
		'type'    => 'radio',
		'section' => 'styles',
		'choices' => array(
			'full' => __( 'Full Width', 'scriptless-social-sharing' ),
			'auto' => __( 'Auto', 'scriptless-social-sharing' ),
		),
		'legend'  => __( 'Width of button container', 'scriptless-social-sharing' ),
	),
	array(
		'id'      => 'button_padding',
		'title'   => __( 'Button Padding', 'scriptless-social-sharing' ),
		'type'    => 'number',
		'section' => 'styles',
		'label'   => __( ' pixels', 'scriptless-social-sharing' ),
		'min'     => 0,
		'max'     => 400,
	),
);

if ( 'genesis' === get_template() ) {
	$fields[] = array(
		'id'      => 'genesis',
		'title'   => __( 'Genesis Framework', 'scriptless-social-sharing' ),
		'type'    => 'checkbox',
		'section' => 'content_types',
		'label'   => __( 'Use Genesis Framework hooks for button locations.', 'scriptless-social-sharing' ),
	);
}

return $fields;
