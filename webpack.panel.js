const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	mode: 'production',
	entry: path.resolve( __dirname, 'src/editor-panel/index.js' ),
	output: {
		path: path.resolve( __dirname, 'build/editor-panel' ),
		filename: 'index.js',
	},
};
