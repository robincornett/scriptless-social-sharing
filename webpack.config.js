const path = require( 'path' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

const scss = ( name ) => path.resolve( 'assets/src/scss', `${ name }.scss` );
const js = ( name ) => path.resolve( 'assets/src/js', `${ name }.js` );

module.exports = {
	...defaultConfig,

	// Extend the entries wp-scripts derives from block.json rather than replacing them.
	entry: async () => ( {
		...( await defaultConfig.entry() ),
		'css/scriptlesssocialsharing-style': scss( 'scriptlesssocialsharing-style' ),
		'css/scriptlesssocialsharing-admin': scss( 'scriptlesssocialsharing-admin' ),
		'js/image-upload': js( 'image-upload' ),
		'js/scriptless-sortable': js( 'scriptless-sortable' ),
	} ),

	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'assets/build' ),
	},

	plugins: [
		...defaultConfig.plugins,
		// The stylesheet entries would each otherwise emit a stub .js and .asset.php.
		new RemoveEmptyScriptsPlugin(),
	],
};
