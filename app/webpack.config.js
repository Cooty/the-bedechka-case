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
    .enableVersioning(Encore.isProduction())
    .enableSourceMaps()
    .addEntry("app", "./templates/ts/app.ts")
    .addEntry("home", "./templates/home/home.ts")
    .addEntry("yt", "./templates/components/youtube-embed/youtube-embed.ts")
    .addEntry("admin", "./templates/admin/ts/admin.ts")
    .addStyleEntry("the-crew", "./templates/the-crew/the-crew.scss")
    .addStyleEntry("protagonists", "./templates/protagonists/protagonists.scss")
    .addStyleEntry("screenings", "./templates/screenings/screenings.scss")
    .addStyleEntry("partners", "./templates/partners/partners.scss")
    .addStyleEntry("critical-path", "./templates/scss/critical-path.scss")
    .addStyleEntry("critical-path-home", "./templates/home/critical-path-home.scss")
    .addStyleEntry("critical-path-subpages", "./templates/scss/critical-path-subpages.scss")
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