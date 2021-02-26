const Encore = require("@symfony/webpack-encore");

const babelLoader = {
    test: /\.js$/,
    loader: "babel-loader",
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
    .setOutputPath("public/build/")
    .copyFiles({
        from: "./assets/images",
        to: "images/[path][name].[hash:8].[ext]"
    })
    .setPublicPath("/build")
    .cleanupOutputBeforeBuild()
    .enableVersioning()
    .enableSourceMaps()
    .addEntry("app", "./assets/ts/app.ts")
    .addEntry("home", "./assets/ts/home.ts")
    .addEntry("yt", "./templates/ts/youtube-embed.ts")
    .addEntry("admin", "./assets/ts/admin/app.ts")
    .addStyleEntry("the-crew", "./assets/scss/the-crew.scss")
    .addStyleEntry("people", "./assets/scss/people.scss")
    .addStyleEntry("screenings", "./assets/scss/screenings.scss")
    .addStyleEntry("partners", "./assets/scss/partners.scss")
    .addStyleEntry("critical-path", "./assets/scss/critical-path.scss")
    .addStyleEntry("critical-path-home", "./assets/scss/critical-path-home.scss")
    .addStyleEntry("critical-path-subpages", "./assets/scss/critical-path-subpages.scss")
    .enableTypeScriptLoader()
    .enableForkedTypeScriptTypesChecking()
    .addLoader(babelLoader)
    .enablePostCssLoader()
    .enableSassLoader()
    .enableSingleRuntimeChunk()
    .splitEntryChunks();

const config = Encore.getWebpackConfig();

// needed for Vagrant VM for watch mode to work correctly
config.watchOptions = {
    poll: true
};

config.optimization.noEmitOnErrors = true;

module.exports = config;