'use strict';

var gulp = require( 'gulp' );

gulp.task( 'assets-sixtenpress', function () {
	var config = require( '../config' );
	gulp.src( config.paths.sixtenBower )
		.pipe( gulp.dest( config.output.sixtenDestination ) );
} );
