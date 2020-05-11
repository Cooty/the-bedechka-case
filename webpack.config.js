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
                    "corejs": {version: 3, proposals: true}
                },
            ]
        ]
    }
};

Encore
    .setOutputPath('public/build/')
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]'
    })
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableVersioning()
    .enableSourceMaps()
    .addEntry('app', './assets/ts/app.ts')
    .addEntry('admin', './assets/ts/admin/app.ts')
    .enableTypeScriptLoader()
    .enableForkedTypeScriptTypesChecking()
    .addLoader(babelLoader)
    .enablePostCssLoader()
    .enableSassLoader((options) => {
        options.outputStyle = 'compressed';
    })
    .enableSingleRuntimeChunk()
    .splitEntryChunks();

const config = Encore.getWebpackConfig();

// needed for Vagrant VM for watch mode to work correctly
config.watchOptions = {
    poll: true
};

module.exports = config;