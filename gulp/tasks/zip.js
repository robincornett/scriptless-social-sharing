'use strict';

var gulp = require( 'gulp' );

gulp.task( 'zip', () => {

	var chmod = require( 'gulp-chmod' ),
		zip = require( 'gulp-zip' ),
		config = require( '../config' );

	return gulp.src( config.buildInclude, { base: '../' } )
		.pipe( chmod( {
			owner: {
				read: true,
				write: true,
				execute: true
			},
			group: {
				read: true,
				write: false,
				execute: true
			},
			others: {
				read: true,
				write: false,
				execute: true
			}
		}, true ) )
		.pipe( zip( config.projectName + '.' + config.version + '.zip' ) )
		.pipe( gulp.dest( config.destination ) );
} );
