import "../scss/home.scss";

import Timeline from "./modules/timeline";
import CasesMap from "./modules/cases-map";
import NewsPagination from "./modules/news-pagination";
import "./interfaces/WindowGlobals";

class Home {
    private static instance: Home;

    private constructor() {}

    public static getInstance(): Home {
        if (!Home.instance) {
            Home.instance = new Home();
        }

        return Home.instance;
    }

    public init() {
        new Timeline(document.getElementById("js-timeline"));

        if(window._config && window._config.mapCaseApiUrl) {
            new CasesMap(document.getElementById("js-cases-map"));
        }

        if(window._config && window._config.newsHasPagination) {
            new NewsPagination(<HTMLButtonElement>document.getElementById("js-news-load-more"));
        }
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const home = Home.getInstance();
Object.freeze(home);

home.init();

export default home;