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
	module: {
		...defaultConfig.module,
		rules: [
			...( defaultConfig.module?.rules || [] ).filter(
				( rule ) => ! ( rule.test && rule.test.toString().includes( 'svg' ) )
			),
			{
				test: /\.svg$/,
				use: [
					{
						loader: '@svgr/webpack',
						options: {
							svgoConfig: {
								plugins: [
									{
										name: 'preset-default',
										params: {
											overrides: {
												inlineStyles: {
													onlyMatchedOnce: false,
												},
												removeViewBox: false,
											},
										},
									},
									'convertStyleToAttrs',
									{
										name: 'convertColors',
										params: { currentColor: true },
									},
								],
							},
						},
					},
				],
			},
		],
	},
};
