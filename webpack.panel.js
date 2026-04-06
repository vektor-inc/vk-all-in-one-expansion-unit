const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	mode: 'production',
	entry: path.resolve( __dirname, 'inc/block-editor-panels/src/index.js' ),
	output: {
		path: path.resolve( __dirname, 'inc/block-editor-panels/build' ),
		filename: 'index.js',
	},
};
