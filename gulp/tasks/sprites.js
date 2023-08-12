'use strict';

var gulp = require( 'gulp' );

gulp.task(
	'sprites',
	() => {
		var svgSprite = require( 'gulp-svg-sprites' );

		return gulp.src( [ 'includes/svg/*.svg', '!includes/svg.brands.svg' ] )
			.pipe( svgSprite( {
				svg: {
					symbols: "brands.svg"
				},
				mode: 'symbols',
				preview: false
			} ) )
			.pipe( gulp.dest( "includes/svg" ) );
	}
);
