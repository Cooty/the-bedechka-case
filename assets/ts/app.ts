import "../scss/app.scss";

import "regenerator-runtime/runtime";

import Header from "./modules/header";
import LazyLoading from "./modules/lazy-loading";
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
        new LazyLoading(".js-lazy-loading");
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();

export default app;