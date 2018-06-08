'use strict';

var gulp = require( 'gulp' );

gulp.task( 'browserSync', function () {

	var browserSync = require( 'browser-sync' ).create(),
		config      = require( '../config' ),
		files       = [
			config.output.styleDestination + '**/*.css',
			config.output.scriptDestination + '**/*.js'
		];
	browserSync.init( files, {
		proxy: config.url,
		injectChanges: true
	} )
} );
