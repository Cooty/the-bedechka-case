export const mapListNavigationItem = (name: string, jsSelectorClass: string):string => {
    return `<li class="cases-map__navigation-item">
        <button type="button" class="tag ${jsSelectorClass}">
            ${name}
        </button>
    </li>`;
};