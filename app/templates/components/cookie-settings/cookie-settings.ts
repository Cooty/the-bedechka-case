import "../../ts/interfaces/WindowGlobals";
import "./_cookie-consent.scss";
import settings from "./cookie-settings.enum";
import template from "./cookie-settings.html";

export default class CookieSettings {
    private readonly openerClassName = "cookie-consent--show";
    private form: HTMLFormElement;
    private toastMessage: HTMLElement;
    private opened = false;
    private toggle: HTMLElement;

    private createToastMessage() {
        const tempContainer = document.createElement("div");
        const selectedTier = JSON.parse(localStorage.getItem(settings.cookieTier));
        let tier2Checked = true;

        if(selectedTier && selectedTier !== settings.defaultTier) {
            tier2Checked = false;
        }

        tempContainer.innerHTML = template(tier2Checked);
        this.form = tempContainer.querySelector(".js-cookie-tiers-form");
        this.form.addEventListener("submit", (e)=> {
            e.preventDefault();
            this.saveAndClose();
        });

        return tempContainer.firstElementChild;
    }

    private open() {
        const toastMessage = <HTMLElement>this.createToastMessage();
        this.toastMessage = toastMessage;
        document.querySelector("body").appendChild(toastMessage);
        window.setTimeout(()=> {
            toastMessage.classList.add(this.openerClassName);
            this.opened = true;
        },1)

    }

    private close() {
        this.toastMessage.classList.remove(this.openerClassName);
        window.setTimeout(()=> {
            this.toastMessage.parentNode.removeChild(this.toastMessage);
            this.toastMessage = null;
            this.opened = false;
        },300);
    }

    private saveAndClose() {
        if(!this.toastMessage) {
            return;
        }

        const formData = new FormData(this.form);
        const cookieTier = formData.get("cookie_tier") || 1;

        try {
            localStorage.setItem(settings.consent, "true");
            localStorage.setItem(settings.cookieTier, cookieTier.toString());

            const event = new CustomEvent(settings.customEventName, {
                detail: {cookieTier},
                bubbles: true
            });

            this.toastMessage.dispatchEvent(event);
        } catch (e) {
            console.error(e);
        }

        this.close();
    }

    public init() {
        this.toggle = document.querySelector(".js-cookie-settings-toggle");

        if(!window.localStorage.getItem(settings.consent)) {
            this.open();
        }

        this.toggle.addEventListener("click", ()=> {
            if(!this.opened) {
                this.open();
            } else {
                this.close();
            }
        });
    }
}
