'use strict';

var gulp = require( 'gulp' );

gulp.task( 'js', function () {

	var uglify = require( 'gulp-uglify-es' ).default,
	    rename = require( 'gulp-rename' ),
	    config = require( '../config' );
	gulp.src( config.paths.jsPath )
		.pipe( uglify() )
		.pipe( rename( {
			extname: '.min.js'
		} ) )
		.pipe( gulp.dest( config.output.scriptDestination ) );
} );
