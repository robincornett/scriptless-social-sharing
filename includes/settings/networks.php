<?php

/**
 * Define the list of networks available for the plugin to use.
 */
return apply_filters(
	'scriptlesssocialsharing_networks',
	array(
		'twitter'   => array(
			'name'  => 'twitter',
			'label' => __( 'Twitter', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'facebook'  => array(
			'name'  => 'facebook',
			'label' => __( 'Facebook', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'google'    => array(
			'name'  => 'google',
			'label' => __( 'Google+', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'pinterest' => array(
			'name'  => 'pinterest',
			'label' => __( 'Pinterest', 'scriptless-social-sharing' ),
			'order' => 0,
		),
		'linkedin'  => array(
			'name'  => 'linkedin',
			'label' => __( 'Linkedin', 'scriptless-social-sharing' ),
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
	)
);
