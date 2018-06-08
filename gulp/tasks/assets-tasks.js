'use strict';

var gulp = require( 'gulp' );

gulp.task( 'assets-tasks', function () {
	var config     = require( '../config' ),
		task_dir   = config.paths.dependencyDir + '/gulp-tasks/gulp/tasks/*.*',
		directions = config.paths.dependencyDir + '/gulp-tasks/gulp/config.js';
	gulp.src( directions )
		.pipe( gulp.dest( config.root + 'gulp' ) );
	gulp.src( task_dir )
		.pipe( gulp.dest( config.root + 'gulp/tasks' ) );
} );
