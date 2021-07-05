import "./home.scss";
import Timeline from "../components/timeline/timeline";
import CasesMapBox from "../components/cases-map-box/cases-map-box";
import News from "../components/news/news";
import "../ts/interfaces/WindowGlobals";

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
            new CasesMapBox(document.getElementById("js-cases-map"));
        }

        if(window._config && window._config.newsHasPagination) {
            new News(<HTMLButtonElement>document.getElementById("js-news-load-more"));
        }
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const home = Home.getInstance();
Object.freeze(home);

home.init();

export default home;