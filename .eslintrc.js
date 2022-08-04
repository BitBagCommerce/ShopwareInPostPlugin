module.exports = {
    'env': {
        'browser': true,
        'es2021': true
    },
    'extends': 'eslint:recommended',
    'parserOptions': {
        'ecmaVersion': 'latest',
        "sourceType": "module"
    },
    'rules': {
        'eqeqeq': 'off',
        'curly': 'error',
        'semi': ['error', 'always'],
        'quotes': ['error', 'single']
    },
};
