'use strict';

var gulp = require( 'gulp' );

gulp.task( 'clean', function () {
	var del    = require( 'del' ),
		config = require( '../config' );
	return del( [ config.destination ] );
} );
