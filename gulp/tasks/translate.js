'use strict';

var gulp = require( 'gulp' );

gulp.task( 'translate', () => {

	var pot = require( 'gulp-wp-pot' ),
		config = require( '../config' );

	return gulp.src( [ config.paths.potSource ] )
		.pipe( pot( {
			domain: config.projectName,
			package: config.projectTitle,
			headers: false
		} ) )
		.pipe( gulp.dest( config.output.potDestination + config.projectName + '.pot' ) );

} );
