const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
module.exports = [
	{
		...defaultConfig,
		entry: __dirname + '/inc/child-page-index/block/src/index.js',
		output: {
			path: __dirname + '/inc/child-page-index/block/build/',
			filename: 'block.js',
		},
		module: {
			...defaultConfig.module,
			rules: [
				...defaultConfig.module.rules,
				{
					test: /\.js$/,
					exclude: /(node_modules|bower_components)/,
					use: {
						loader: 'babel-loader',
						options: {
							presets: [ '@babel/preset-env' ],
							plugins: [
								'@babel/plugin-transform-react-jsx',
							],
						},
					},
				},
			],
		},
	},
	{
		...defaultConfig,
		entry: __dirname + '/inc/call-to-action/package/block/src/index.js',
		output: {
			path: __dirname + '/inc/call-to-action/package/block/build/',
			filename: 'block.js',
		},
		module: {
			...defaultConfig.module,
			rules: [
				...defaultConfig.module.rules,
				{
					test: /\.js$/,
					exclude: /(node_modules|bower_components)/,
					use: {
						loader: 'babel-loader',
						options: {
							presets: [ '@babel/preset-env' ],
							plugins: [
								'@babel/plugin-transform-react-jsx',
							],
						},
					},
				},
			],
		},
	}
];