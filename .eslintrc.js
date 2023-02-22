module.exports = {
	root: true,
	extends: [ 'plugin:@wordpress/eslint-plugin/recommended' ],
	rules: {
		'import/no-unresolved': 'off',
		'import/no-extraneous-dependencies': 'off',
		camelcase: 'off',
	},
};