import ILocation from "./ILocation";
import sanitizeHTML from "../../ts/utils/sanitize-html";

const popupContent = (location: ILocation): string => {
    const cssNS = "leaflet-popup-content";
    const image = `<div class="${cssNS}__image-container embed-16-9">
            <img src="${sanitizeHTML(location.image)}" 
                 alt="${sanitizeHTML(location.name)}"
                 class="${cssNS}__image" />
        </div>`;

    return `
        ${location.image ? image : ""}
        <h1 class="${cssNS}__title">${sanitizeHTML(location.name)}</h1>
        <div class="${cssNS}__text">${sanitizeHTML(location.description)}</div>
        ${location.link ? `<a href="${sanitizeHTML(location.link)}" class="${cssNS}__link" target="_blank" rel="noopener noreferer">${sanitizeHTML(location.link)}</a>`: ""}
    `;
};

export default popupContent;