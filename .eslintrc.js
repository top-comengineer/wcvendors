module.exports = {
	env: {
		jquery: true,
		es6: true,
		node: true,
		amd: true
	},
	parser: 'babel-eslint',
	parserOptions: {
		sourceType: 'module'
	},
	extends: ['wordpress', 'eslint:recommended', 'plugin:prettier/recommended'],
	plugins: ['babel', 'prettier'],
	rules: {
		'no-undef': 'off'
	}
};
