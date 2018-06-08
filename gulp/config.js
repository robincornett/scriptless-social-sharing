/**
 * The configuration file for gulp projects.
 * This file should not be edited--project specific variables are stored in variables.js
 */
'use strict';

/**
 * User defined variables
 */
var variables = require( './variables' );

/**
 * Main source
 * @type {string}
 */
var source = './';

/**
 * Source for Sass files
 * @type {string}
 */
var styleSource = source + 'sass/**/*.scss';

/**
 * CSS destination folder
 * themes: ''
 * plugins: 'includes/css'
 * @type {string}
 */
var styleDestination = source + ( 'theme' === variables.type ? '' : 'includes/css' );

/**
 * Sass output style
 * @type {string}
 */
var sassOutputStyle = '' !== variables.sassOutputStyle ? variables.sassOutputStyle : ( 'theme' === variables.type ? 'compact' : 'compressed' );

/**
 * JS destination folder
 * themes: 'js'
 * plugins: 'includes/js'
 * @type {string}
 */
var scriptDestination = 'theme' === variables.type ? 'js' : 'includes/js';

/**
 * Source for javascript files
 * @type {[*]}
 */
var scriptSource = [
	source + scriptDestination + '/**/*.js',
	'!' + source + scriptDestination + '/**/*min.js'
];

/**
 * Source for Bower files
 * @type {string}
 */
var dependencySource = 'node_modules';

/**
 * Source for Six/Ten Press files
 * @type {[*]}
 */
var SixTenSource = [
	dependencySource + '/sixtenpress/includes/common/**.*',
	dependencySource + '/sixtenpress/includes/common/**/*'
];

/**
 * Destination for Six/Ten Press common files
 * @type {string}
 */
var SixTenDestination = source + 'includes/common';

/**
 * Source for language files.
 * @type {string}
 */
var potSource = source + '**/*.php';

/**
 * Destination for language files.
 * @type {string}
 */
var potDestination = source + variables.languageFolder + '/';

/**
 * The following should not be edited.
 * @type {{projectName: string, version: string, paths: {sassPath: string, dependencyDir: string, sixten: string}, output: {style: string, destination: string}, destination: string, buildInclude: [*]}}
 */
module.exports = {

	projectName: variables.projectSlug,
	projectTitle: variables.projectName,
	version: variables.version,
	root: source,
	url: variables.url,
	paths: {
		sassPath: styleSource,
		jsPath: scriptSource,
		dependencyDir: dependencySource,
		sixtenBower: SixTenSource,
		potSource: potSource
	},
	output: {
		style: sassOutputStyle,
		styleDestination: styleDestination,
		scriptDestination: scriptDestination,
		sixtenDestination: SixTenDestination,
		potDestination: potDestination
	},
	packages: variables.packages,
	destination: variables.buildDestination,
	buildInclude: [
		'**',

		// exclude:
		'!node_modules/**/*',
		'!bower_components/**/*',
		'!sass/**/*',
		'!dist/**/*',
		'!node_modules',
		'!bower_components',
		'!sass',
		'!dist',
		'!gulpfile.js',
		'!package.json',
		'!package-lock.json',
		'!bower.json',
		'!gulp/**/*',
		'!gulp',
		'!yarn.lock'
	]
};
