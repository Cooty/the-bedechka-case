import "../scss/app.scss";

import "regenerator-runtime/runtime";

import Header from "./modules/header";
import Timeline from "./modules/timeline";
import CasesMap from "./modules/cases-map";
import NewsPagination from "./modules/news-pagination";
import YTPlayer from "./modules/yt-player";
import "./interfaces/WindowGlobals";

class App {
    private static instance: App;

    private constructor() {}

    public static getInstance(): App {
        if (!App.instance) {
            App.instance = new App();
        }

        return App.instance;
    }

    public init() {
        new Header(document.getElementById("js-header"));
        new Timeline(document.getElementById("js-timeline"));

        if(window._config && window._config.mapCaseApiUrl) {
            new CasesMap(document.getElementById("js-cases-map"));
        }

        if(window._config && window._config.newsHasPagination) {
            new NewsPagination(<HTMLButtonElement>document.getElementById("js-news-load-more"));
        }

        new YTPlayer();
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();

export default app;