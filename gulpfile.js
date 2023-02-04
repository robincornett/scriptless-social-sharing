/**
 * gulpfile.js
 */
'use strict';

var gulp = require( 'gulp' );

function getTask ( task ) {
	var taskDir = './gulp/tasks/' + task;

	return require( taskDir );
}

var tasks = [ 'sass', 'js', 'sprites', 'translate', 'zip' ];
for ( var index in tasks ) {
	getTask( tasks[ index ] );
}

gulp.task( 'build', gulp.series( [ 'sass', 'js', 'translate', 'zip' ] ) );
