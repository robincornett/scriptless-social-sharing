/*
 * Copyright (c) 2017 Robin Cornett
 */

'use strict';

var gulp = require( 'gulp' );

gulp.task( 'assets-shortcodes', function () {
	var config     = require( '../config' ),
		task_dir   = config.paths.dependencyDir + '/sixtenpress-shortcodes/includes/**/*.*',
		directions = config.paths.dependencyDir + '/sixtenpress-shortcodes/sixtenpress-shortcodes.php';
	gulp.src( directions )
		.pipe( gulp.dest( config.root + 'includes/shortcodes' ) );
	gulp.src( task_dir )
		.pipe( gulp.dest( config.root + 'includes/shortcodes/includes' ) );
} );
