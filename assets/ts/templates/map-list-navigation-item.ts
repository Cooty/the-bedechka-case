import sanitizeHTML from "../utils/sanitize-html";

const mapListNavigationItem = (name: string, jsSelectorClass: string) =>
    `<li class="cases-map__navigation-item">
        <button type="button" class="tag ${jsSelectorClass}">
            ${sanitizeHTML(name)}
        </button>
    </li>`;

export default mapListNavigationItem;