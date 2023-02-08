const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
module.exports = [
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
	},
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
		entry: __dirname + '/inc/contact-section/block/src/index.js',
		output: {
			path: __dirname + '/inc/contact-section/block/build/',
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
		entry: __dirname + '/inc/page-list-ancestor/block/src/index.js',
		output: {
			path: __dirname + '/inc/page-list-ancestor/block/build/',
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
		entry: __dirname + '/inc/sitemap-page/block/src/index.js',
		output: {
			path: __dirname + '/inc/sitemap-page/block/build/',
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
		entry: __dirname + '/inc/sns/block/src/index.js',
		output: {
			path: __dirname + '/inc/sns/block/build/',
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
];