'use strict';

var gulp = require( 'gulp' );

gulp.task( 'translate', function () {

	var pot    = require( 'gulp-wp-pot' ),
		config = require( '../config' );

	gulp.src( [config.paths.potSource] )
		.pipe( pot( {
			domain: config.projectName,
			package: config.projectTitle,
			headers: false
		} ) )
		.pipe( gulp.dest( config.output.potDestination + config.projectName + '.pot' ) );

} );
