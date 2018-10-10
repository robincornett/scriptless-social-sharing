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
		),
		'facebook'  => array(
			'name'  => 'facebook',
			'label' => __( 'Facebook', 'scriptless-social-sharing' ),
		),
		'google'    => array(
			'name'  => 'google',
			'label' => __( 'Google+', 'scriptless-social-sharing' ),
		),
		'pinterest' => array(
			'name'  => 'pinterest',
			'label' => __( 'Pinterest', 'scriptless-social-sharing' ),
		),
		'linkedin'  => array(
			'name'  => 'linkedin',
			'label' => __( 'Linkedin', 'scriptless-social-sharing' ),
		),
		'email'     => array(
			'name'  => 'email',
			'label' => __( 'Email', 'scriptless-social-sharing' ),
		),
		'reddit'    => array(
			'name'  => 'reddit',
			'label' => __( 'Reddit', 'scriptless-social-sharing' ),
		),
	) );
