module.exports = [
	{
		entry: {
			'react-jsx-runtime': {
				import: 'react/jsx-runtime',
			},
		},
		output: {
			path: __dirname + '/assets/js',
			filename: 'react-jsx-runtime.js',
			library: {
				name: 'ReactJSXRuntime',
				type: 'window',
			},
		},
		externals: {
			react: 'React',
		},
	},
];