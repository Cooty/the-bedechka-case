const Encore = require('@symfony/webpack-encore');
const babelLoader = {
    test: /\.js$/,
    loader: 'babel-loader',
    query: {
        presets: [
            [
                "@babel/preset-env",
                {
                    "useBuiltIns": "entry",
                    "corejs": { version: 3, proposals: true }
                },
            ]
        ]
    }
};

Encore
    .setOutputPath('public/build/')
    .copyFiles({
        from: './assets/images',
    })
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableVersioning()
    .enableSourceMaps(!Encore.isProduction())
    .addEntry('app', './assets/ts/app.ts')
    .enableTypeScriptLoader()
    .enableForkedTypeScriptTypesChecking()
    .addLoader(babelLoader)
    .enablePostCssLoader()
    .enableSassLoader((options) => {
        options.outputStyle = 'compressed';
    })
    .disableSingleRuntimeChunk();

const config = Encore.getWebpackConfig();

config.watchOptions = {
    poll: true
};

module.exports = config;