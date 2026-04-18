module.exports = function ( grunt ) {
	require( 'load-grunt-tasks' )( grunt );

	grunt.initConfig( {

		pkg: grunt.file.readJSON( 'package.json' ),

		clean: {
			main: [ 'build/**' ],
		},

		copy: {
			main: {
				src: [
					'assets/build/**',
					'includes/**',
					'languages/**',
					'src/**',
					'*.php',
					'*.txt',
					'!assets/build/**/*.asset.php',
				],
				dest: 'build/<%= pkg.name %>/',
			},
		},

		compress: {
			main: {
				options: {
					mode: 'zip',
					archive: './build/<%= pkg.name %>-<%= pkg.version %>.zip',
				},
				expand: true,
				cwd: 'build/<%= pkg.name %>/',
				src: [ '**/*' ],
				dest: '<%= pkg.name %>/',
			},
		},
	} );
};
