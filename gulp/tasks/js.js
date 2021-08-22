'use strict';

var gulp = require( 'gulp' );

gulp.task( 'js', () => {

	var uglify = require( 'gulp-uglify-es' ).default,
		rename = require( 'gulp-rename' ),
		config = require( '../config' );

	return gulp.src( config.paths.jsPath )
		.pipe( uglify() )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( config.output.scriptDestination ) );
} );
