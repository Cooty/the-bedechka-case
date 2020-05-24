import "../../scss/components/_cases-map.scss";
import "../../scss/components/_leaflet-popup-content.scss";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

import ILocations from "../interfaces/ILocations";
import ILocation from "../interfaces/ILocation";
import {LatLngExpression, Map, Marker} from "leaflet";
import popupContent from "../templates/popup-content";
import openStreetMapsAttribution from "../templates/open-street-maps-attribution";
import {getNetworkErrorMessage} from "../utils/error-handling";
import "../interfaces/WindowGlobals";
const {loadCSS} = require("fg-loadcss/src/loadCSS");

export default class CasesMap {
    private readonly rootElement: HTMLElement;
    private readonly leafletCDNURL: string;
    private locationsLoaded = false;
    private markerCluster: any;
    private markers: Marker[] = [];
    private readonly navigationButtonActiveClass = "tag--active";

    constructor(rootElement: HTMLElement) {
        if (!rootElement) {
            return;
        }

        this.rootElement = rootElement;
        this.leafletCDNURL = "https://unpkg.com/leaflet@1.5.1";
        this.init();
    }

    private async getLocations(): Promise<ILocations> {
        this.locationsLoaded = true;
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
            maxZoom: 18,
            minZoom: 5,
            id: "mapbox.streets",
            accessToken: window._config.mapboxAccessToken
        }).addTo(casesMap);

        return casesMap;
    }

    private mapLocationTagsClickHandler(el: HTMLElement, index: number) {
        if(el.classList.contains(this.navigationButtonActiveClass)) {
            return;
        }
        const listItems = Array.from(el.parentElement.parentElement.childNodes);

        listItems.map((el: HTMLElement)=> {
            el.firstElementChild.classList.remove(this.navigationButtonActiveClass);
        });

        el.classList.add(this.navigationButtonActiveClass);

        const m = this.markers[index];

        // have to zoom into the the Marker Cluster first then show the popup
        // @see https://github.com/Leaflet/Leaflet.markercluster/issues/72
        this.markerCluster.zoomToShowLayer(m, ()=> {
            m.openPopup();
        });
    }

    private makeNavigationListItems(locations: ILocations) {
        const locationList = this.rootElement.querySelector(".js-cases-map-location-list");

        locations.items.forEach((location, index) => {
            const li = document.createElement('li');
            li.className = 'cases-map__navigation-item';
            const button = document.createElement('button');
            button.id = location.id;
            button.type = 'button';
            button.className = 'tag';
            button.innerText = location.name;

            li.appendChild(button);

            li.addEventListener("click", () => {
                this.mapLocationTagsClickHandler(button, index);
            });

            locationList.appendChild(li);
        });
    }

    private markNavigationElementAsActive(event: any) {
        const id = event.target.properties.id;
        const button = document.getElementById(id);
        const activeButtons = Array.from(button.parentElement.parentElement.querySelectorAll(`.${this.navigationButtonActiveClass}`));

        activeButtons.map(button=> {
            button.classList.remove(this.navigationButtonActiveClass);
        });

        button.classList.add(this.navigationButtonActiveClass);
    }

    private addLocationsToMap(locations: ILocations, map: Map) {
        const markerCluster = window.L.markerClusterGroup();

        locations.items.map((location: ILocation)=> {
            const marker: any = window.L.marker([location.coords.latitude, location.coords.longitude]);
            marker.bindPopup(popupContent(location));
            marker.properties = {};
            marker.properties.id = location.id;

            markerCluster.addLayer(marker);

            marker.on("click", (event: any)=> {
                this.markNavigationElementAsActive(event);
            });
            this.markers.push(marker);
        });

        map.addLayer(markerCluster);

        this.markerCluster = markerCluster;
    }

    public initCasesMap(scriptText: string, locationsData: ILocations) {
        CasesMap.appendScript(scriptText);

        import(/* webpackMode: "lazy-once" */<any>"leaflet.markercluster/dist/leaflet.markercluster").then(()=> {
            const map = CasesMap.makeMap();
            this.addLocationsToMap(locationsData, map);
            this.makeNavigationListItems(locationsData);
        });
    }

    private async leafletCSSCallback() {
        if(this.locationsLoaded) {
            return;
        }

        try {
            await this.getLocations().then((locationsData) => {
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