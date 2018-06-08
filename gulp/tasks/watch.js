'use strict';

var gulp = require( 'gulp' );

gulp.task( 'watch', ['sass','browserSync'], function () {
	var config = require( '../config' );
	gulp.watch( config.paths.jsPath, ['js'] );
	gulp.watch( config.paths.sassPath, ['sass'] );
} );
