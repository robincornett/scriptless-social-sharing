<?php

/**
 * Define all of the defaults for the plugin.
 */
return array(
	'styles'         => array(
		'plugin'   => 1,
		'font'     => 1,
		'font_css' => 1,
	),
	'heading'        => __( 'Share this post:', 'scriptless-social-sharing' ),
	'buttons'        => array(
		'twitter'   => 1,
		'facebook'  => 1,
		'google'    => 0,
		'pinterest' => 1,
		'linkedin'  => 1,
		'email'     => 1,
		'reddit'    => 0,
	),
	'twitter_handle' => '',
	'email_subject'  => __( 'A post worth sharing:', 'scriptless-social-sharing' ),
	'email_body'     => __( 'I read this post and wanted to share it with you. Here\'s the link:', 'scriptless-social-sharing' ),
	'post_types'     => array(
		'post' => array(
			'before' => 0,
			'after'  => 1,
			'manual' => 0,
		),
	),
	'location'       => false,
	'button_style'   => 1,
	'button_padding' => 12,
	'table_width'    => 'full',
);
