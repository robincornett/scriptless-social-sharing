'use strict';

var gulp = require( 'gulp' );

gulp.task( 'build', [
	'sass',
	'js',
	'translate',
	'zip'
] );
