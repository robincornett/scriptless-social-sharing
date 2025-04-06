<?php

/**
 * Define the list of networks available for the plugin to use.
 */
return apply_filters(
	'scriptlesssocialsharing_networks',
	array(
		'twitter'   => array(
			'name'  => 'twitter',
			'label' => __( 'X (Twitter)', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'facebook'  => array(
			'name'  => 'facebook',
			'label' => __( 'Facebook', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'pinterest' => array(
			'name'  => 'pinterest',
			'label' => __( 'Pinterest', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'linkedin'  => array(
			'name'  => 'linkedin',
			'label' => __( 'LinkedIn', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'email'     => array(
			'name'  => 'email',
			'label' => __( 'Email', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'reddit'    => array(
			'name'  => 'reddit',
			'label' => __( 'Reddit', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'whatsapp'  => array(
			'name'  => 'whatsapp',
			'label' => __( 'WhatsApp', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'pocket'    => array(
			'name'  => 'pocket',
			'label' => __( 'Pocket', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'telegram'  => array(
			'name'  => 'telegram',
			'label' => __( 'Telegram', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'hatena'    => array(
			'name'  => 'hatena',
			'label' => __( 'Hatena', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'sms'       => array(
			'name'  => 'sms',
			'label' => __( 'SMS', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'bluesky'   => array(
			/**
			 * Bluesky is a new social network, added in 3.3.0
			 */
			'name'  => 'bluesky',
			'label' => __( 'Bluesky', 'scriptless-social-sharing' ),
			'order' => 0,
		),
	)
);
