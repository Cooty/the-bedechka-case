import "../scss/app.scss";

import {Header} from "./modules/header";
import {Timeline} from "./modules/timeline";
import {CasesMap} from "./modules/cases-map";

import {IConfig} from "./interfaces/IConfig";
import {Layer} from "leaflet";

declare global {
    interface Window {
        _config?: IConfig,
        L?: any,
        onloadCSS?: Function
    }
}

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
        new Header(document.querySelector(".js-header"));
        new Timeline(document.querySelector(".js-timeline"));
        new CasesMap(document.querySelector(".js-cases-map"));
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();

export default app;