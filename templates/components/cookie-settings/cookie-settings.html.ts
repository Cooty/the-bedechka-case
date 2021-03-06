import "../../../assets/ts/interfaces/WindowGlobals";

const cookieSettings = (tier2Checked: boolean = false)=> `
    <form class="cookie-consent js-cookie-tiers-form" action="/" novalidate name="cookie-settings">
        <p class="cookie-consent__text">
            ${window._translations.cookieConsent.lead}
        </p>
        <ul class="cookie-consent__options">
            <li class="cookie-consent__options-item">
                <label>
                    <input type="checkbox" readonly disabled checked value="1" name="cookie_tier" />
                    ${window._translations.cookieConsent.tier1}
                </label>
            </li>
            <li class="cookie-consent__options-item">
                <label>
                    <input type="checkbox" ${tier2Checked ? 'checked' : ''} value="2" name="cookie_tier" />
                    ${window._translations.cookieConsent.tier2}
                </label>
            </li>
        </ul>
        <div class="cookie-consent__button">
            <button type="submit" class="button button--small">
                ${window._translations.cookieConsent.save}
            </button>
        </div>
    </form>`;

export default cookieSettings;