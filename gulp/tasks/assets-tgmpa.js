'use strict';

var gulp = require( 'gulp' );

gulp.task( 'assets-tgmpa', function () {
	var config = require( '../config' );
	return gulp.src( config.paths.dependencyDir + '/tgmpa/class-tgm-plugin-activation.php' )
		.pipe( gulp.dest( config.root ) );
} );
