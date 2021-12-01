import "../scss/app.scss";

import "regenerator-runtime/runtime";

import Header from "../components/header/header";
import LazyLoading from "./modules/lazy-loading";
import Analytics from "./modules/analytics";
import CookieSettings from "../components/cookie-settings/cookie-settings";
import "./interfaces/WindowGlobals";
import detectWebP from "./modules/detect-webp";

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
        const cookieConsent = new CookieSettings();
        cookieConsent.init();

        const analytics = new Analytics();
        analytics.init();
        detectWebP();
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = App.getInstance();
Object.freeze(app);

app.init();

export default app;