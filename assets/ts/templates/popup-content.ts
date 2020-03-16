import {ILocation} from "../interfaces/ILocation";

export const popupContent = (location: ILocation): string => {
    const cssNS = "leaflet-popup-content";
    const image = `<div class="${cssNS}__image-container embed-16-9">
            <img src="${location.image}" alt="${location.name}" class="${cssNS}__image" />
        </div>`;

    return `
        ${location.image ? image : ""}
        <h1 class="${cssNS}__title">${location.name}</h1>
        <div class="${cssNS}__text">${location.description}</div>
        ${location.link ? `<a href="${location.link}" class="${cssNS}__link" target="_blank" rel="noopener noreferer">${location.link}</a>`: ""}
    `;
};