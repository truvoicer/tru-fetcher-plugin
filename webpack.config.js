const Dotenv = require('dotenv-webpack');
const defaults = require('@wordpress/scripts/config/webpack.config');
const webpack = require('webpack');
defaults.plugins.push(
    new webpack.ProvidePlugin({
        $: 'jquery',
        jQuery: 'jquery',
    }),
    new Dotenv({
        path: '.env'
    })
)
module.exports = {
    ...defaults,
    // https://webpack.js.org/configuration/entry-context/#context
    externals: {
        react: 'React',
        'react-dom': 'ReactDOM',
    },
};
