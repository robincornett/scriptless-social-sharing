const path = require( 'path' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports = {
	...defaultConfig,
	entry: {
		'css/scriptlesssocialsharing-style':      path.resolve( 'sass', 'scriptlesssocialsharing-style.scss' ),
		'css/scriptlesssocialsharing-admin':       path.resolve( 'sass', 'scriptlesssocialsharing-admin.scss' ),
		'css/scriptlesssocialsharing-block':       path.resolve( 'sass', 'scriptlesssocialsharing-block.scss' ),
		'css/scriptlesssocialsharing-fontawesome': path.resolve( 'sass', 'scriptlesssocialsharing-fontawesome.scss' ),
		'blocks/buttons/index':                    path.resolve( 'src/blocks/buttons', 'index.js' ),
		'js/block':                                path.resolve( 'assets/src/js', 'block.js' ),
		'js/image-upload':                         path.resolve( 'assets/src/js', 'image-upload.js' ),
		'js/scriptless-sortable':                  path.resolve( 'assets/src/js', 'scriptless-sortable.js' ),
	},
	output: {
		path: path.resolve( __dirname, 'assets/build' ),
	},
	plugins: [
		...defaultConfig.plugins,
		new MiniCSSExtractPlugin(),
		new RemoveEmptyScriptsPlugin(),
	],
};
