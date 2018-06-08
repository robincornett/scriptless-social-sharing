'use strict';

var gulp = require( 'gulp' );

gulp.task( 'js', function () {

	var uglify = require( 'gulp-uglify' ),
		rename = require( 'gulp-rename' ),
		config = require( '../config' );
	gulp.src( config.paths.jsPath )
		.pipe( uglify( {preserveComments: 'some'} ) )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( config.output.scriptDestination ) );
} );
