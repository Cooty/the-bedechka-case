import "../../scss/layout/_cases-map.scss";

import ILocations from "../interfaces/ILocations";
import ILocation from "../interfaces/ILocation";
import {LatLngExpression, Map, Marker} from "leaflet";
import popupContent from "../templates/popup-content";
import openStreetMapsAttribution from "../templates/open-street-maps-attribution";
import mapListNavigationItem from "../templates/map-list-navigation-item";
import {getNetworkErrorMessage} from "../utils/error-handling";
import "../interfaces/WindowGlobals";
const {loadCSS} = require("fg-loadcss/src/loadCSS");

export default class CasesMap {
    private readonly rootElement: HTMLElement;
    private readonly leafletCDNURL: string;

    constructor(rootElement: HTMLElement) {
        if (!rootElement) {
            return;
        }

        this.rootElement = rootElement;
        this.leafletCDNURL = "https://unpkg.com/leaflet@1.5.1";
        this.init();
    }

    private static async getLocations(): Promise<ILocations> {
        const locationsAPIURL: RequestInfo = `${window._config.mapCaseApiUrl}?token=${window._config.mapCaseEndpointToken}`;

        const response = await fetch(locationsAPIURL);
        if (response.status !== 200) {
            throw new Error(getNetworkErrorMessage(locationsAPIURL, response.status));
        } else {
            return await response.json();
        }
    }

    private async loadLeafletJS() {
        const leafletJSURL: RequestInfo = `${this.leafletCDNURL}/dist/leaflet.js`;

        const response = await fetch(leafletJSURL);
        if (response.status !== 200) {
            throw new Error(getNetworkErrorMessage(leafletJSURL, response.status));
        } else {
            return await response.text();
        }
    }

    private static appendScript(text: string) {
        const script = document.createElement("script");
        script.innerText = text;

        document.body.appendChild(script);
    }

    private handleError(e: Error) {
        console.error(e);
        this.rootElement.style.display = "none";
    }

    private static makeMap(): Map {
        const mapContainerId: any = "js-cases-map-container";
        const mapCenter: LatLngExpression = [42.43897, 25.6289515]; // coords of Park Bedechka
        const defaultZoom = 7;
        const mapProviderURL = "https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}";

        const casesMap = window.L.map(mapContainerId).setView(mapCenter, defaultZoom);
        window.L.tileLayer(mapProviderURL, {
            attribution: openStreetMapsAttribution(),
            maxZoom: 19,
            minZoom: 5,
            id: "mapbox.streets",
            accessToken: window._config.mapboxAccessToken
        }).addTo(casesMap);

        return casesMap;
    }

    private makeNavigationListItems(locations: ILocations, jsSelectorClass: string): string {
        return locations.items.map(location => {
            return mapListNavigationItem(location.name, jsSelectorClass);
        }).join("");
    }

    private appendAndReturnNavigationListItems(navigationListItems: string, jsSelectorClass: string): NodeList {
        const locationList = this.rootElement.querySelector(".js-cases-map-location-list");

        locationList.innerHTML = navigationListItems;

        return locationList.querySelectorAll(`.${jsSelectorClass}`);
    }

    private addLocationsToMap(locations: ILocations, map: Map): Marker[] {
        const markers: Marker[] = [];

        locations.items.map((location: ILocation) => {
            const marker: Marker = window.L.marker([location.coords.latitude, location.coords.longitude])
                .addTo(map);
            marker.bindPopup(popupContent(location));

            markers.push(marker);
        });

        return markers;
    }

    private controlPopupsFromNavigationList(navigationItems: NodeList, markers: Marker[]) {
        const navigationButtonActiveClass = "tag--active";
        // need this for IE and Edge to loop through a NodeList
        const navigationItemsList = Array.from(navigationItems);

        navigationItemsList.map((navigationItem: HTMLElement, index: number) => {
            navigationItem.addEventListener("click", () => {
                if (navigationItem.classList.contains(navigationButtonActiveClass)) {
                    return;
                }

                navigationItemsList.map((navigationItem: HTMLElement) => {
                    navigationItem.classList.remove(navigationButtonActiveClass);
                });

                navigationItem.classList.add(navigationButtonActiveClass);
                markers[index].openPopup();
            });
        });
    }

    public initCasesMap(scriptText: string, locationsData: ILocations) {
        CasesMap.appendScript(scriptText);
        const map = CasesMap.makeMap();
        const markers = this.addLocationsToMap(locationsData, map);
        const jsSelectorClass = "js-cases-map-navigation-btn";
        const navigationList =
            this.makeNavigationListItems(locationsData, jsSelectorClass);
        const navigationListItems =
            this.appendAndReturnNavigationListItems(navigationList, jsSelectorClass);
        this.controlPopupsFromNavigationList(navigationListItems, markers);
    }

    private async leafletCSSCallback() {
        try {
            await CasesMap.getLocations().then(locationsData => {
                return locationsData;
            }).then((locationsData) => {
                this.loadLeafletJS().then((scriptText) => {
                    this.initCasesMap(scriptText, locationsData);
                }).catch((e) => {
                    this.handleError(e);
                });
            });
        } catch (e) {
            this.handleError(e)
        }

    }

    public init() {
        const leafletCSSURL = `${this.leafletCDNURL}/dist/leaflet.css`;
        const leafletCSS = loadCSS(leafletCSSURL);

        leafletCSS.onload = () => this.leafletCSSCallback();
    }

}