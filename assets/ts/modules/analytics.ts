import "../interfaces/WindowGlobals";
import CookieSettings from "../../../templates/components/cookie-settings/cookie-settings.enum";
import deleteAllCookies from "../utils/delete-all-cookies";

export default class Analytics {
    private idNumber = "168786587";
    private readonly id = `UA-${this.idNumber}-1`;
    private isLoaded = false;

    private load() {
        const ga = document.createElement("script");
        ga.src = `https://www.googletagmanager.com/gtag/js?id=${this.id}`;
        ga.async = true;
        const head = document.querySelector("head");

        head.appendChild(ga);

        window.dataLayer = window.dataLayer || [];

        const gtag = function (...args: any[]) {
            window.dataLayer.push(arguments);
        };

        gtag("js", new Date());
        gtag("config", this.id);
    }

    public init() {
        try {
            const cookieTier = JSON.parse(window.localStorage.getItem(CookieSettings.cookieTier));

            if(cookieTier >= 2) {
                this.load();
                this.isLoaded = true;
            }

            document.querySelector("body")
                .addEventListener(CookieSettings.customEventName, (e: CustomEvent)=> {
                    if(JSON.parse(e.detail.cookieTier) >= 2 && !this.isLoaded) {
                        this.load();
                    } else {
                        deleteAllCookies();
                    }});
        } catch(e) {
            console.error(e);
        }

    }
}
