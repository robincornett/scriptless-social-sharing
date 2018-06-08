/**
 * Use this file to define unique project details.
 */

'use strict';

var project = require( '../package.json' );

module.exports = {

	/**
	 * The project slug (folder)
	 */
	projectSlug: project.name,

	/**
	 * The project's proper name
	 */
	projectName: project.description,

	/**
	 * Current project version
	 */
	version: project.version,

	/**
	 * Type of project (plugin/theme)
	 */
	type: 'plugin',

	/**
	 * Preferred languages folder
	 */
	languageFolder: 'languages',

	/**
	 * Local Development URL (for BrowserSync)
	 */
	url: 'local.wordpress-trunk.test',

	/**
	 * If blank, will be compact for themes, compressed for plugins.
	 * Define this manually to override (expanded, compact, compressed, nested)
	 */
	sassOutputStyle: '',

	/**
	 * Array of assets/packages for importing into project.
	 * Current packages available: 'assets-shortcodes',
	 *                             'assets-sixtenpress',
	 *                             'assets-slick',
	 *                             'assets-tasks',
	 *                             'assets-tgmpa'
	 */
	packages: [
		// 'assets-shortcodes',
		// 'assets-sixtenpress',
		// 'assets-slick',
		// 'assets-tasks',
		// 'assets-tgmpa'
	],

	/**
	 * Destination folder for project zip archives.
	 */
	buildDestination: '../../build'
};
