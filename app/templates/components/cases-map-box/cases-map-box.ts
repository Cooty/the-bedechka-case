import "./_cases-map.scss";
import "../../ts/map-common/_map-popup-content.scss";
import "leaflet.markercluster/dist/MarkerCluster.css";
import "leaflet.markercluster/dist/MarkerCluster.Default.css";

import ILocations from "../../ts/map-common/ILocations";
import ILocation from "../../ts/map-common/ILocation";
import {LatLngExpression, Map, Marker} from "leaflet";
import popupContent from "../../ts/map-common/popup-content.html";
import openStreetMapsAttribution from "../../ts/map-common/open-street-maps-attribution.html";
import {getNetworkErrorMessage} from "../../ts/utils/error-handling";
import "../../ts/interfaces/WindowGlobals";
import Viewport from "../../ts/utils/viewport";
import debounce from "../../ts/utils/debounce";
const {loadCSS} = require("fg-loadcss/src/loadCSS");
import MapSettings from "../../ts/map-common/map-settings";

export default class CasesMapBox {
    private readonly rootElement: HTMLElement;
    private readonly leafletCDNURL: string;
    private locationsLoaded = false;
    private markerCluster: any;
    private markers: Marker[] = [];
    private readonly navigationButtonActiveClass = "tag--active";
    private readonly locationList: HTMLElement;

    constructor(rootElement: HTMLElement) {
        if (!rootElement) {
            return;
        }

        this.rootElement = rootElement;
        this.locationList = rootElement.querySelector(".js-cases-map-location-list");
        this.leafletCDNURL = "https://unpkg.com/leaflet@1.5.1";
        this.init();
    }

    private async getLocations(): Promise<ILocations> {
        this.locationsLoaded = true;

        const response = await fetch(window._config.mapCaseApiUrl);
        if (response.status !== 200) {
            throw new Error(getNetworkErrorMessage(window._config.mapCaseApiUrl, response.status));
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

    private deactivateNavListItem() {
        const activeNavItem = this.locationList
            .querySelector(`.${this.navigationButtonActiveClass}`);
        
        if (activeNavItem && activeNavItem.classList) {
            activeNavItem.classList.remove(this.navigationButtonActiveClass);
        }
    }

    private makeMap(): Map {
        const mapContainerId: any = "js-cases-map-container";
        const mapCenter: LatLngExpression = MapSettings.defaultCenter; // coords of Park Bedechka
        const defaultZoom = MapSettings.defaultZoom;
        const mapProviderURL = "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";

        const casesMap = window.L.map(
            mapContainerId,
            {
                zoomControl: false // disable the default zoom control
            }
        ).setView(mapCenter, defaultZoom);

        // add a new zoom controll to the bottom-right corner
        // @see https://stackoverflow.com/questions/33614912/how-to-locate-leaflet-zoom-control-in-a-desired-position
        new window.L.Control.Zoom({ position: "bottomright" }).addTo(casesMap);

        casesMap.on("popupclose", this.deactivateNavListItem.bind(this));

        window.L.tileLayer(mapProviderURL, {
            attribution: openStreetMapsAttribution(),
            minZoom: MapSettings.defaultMinZoom,
            maxZoom: MapSettings.defaultMaxZoom,
            dragging: !window.L.Browser.mobile,
            tap: !window.L.Browser.mobile
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

        // have to zoom into the Marker Cluster first then show the popup
        // @see https://github.com/Leaflet/Leaflet.markercluster/issues/72
        this.markerCluster.zoomToShowLayer(m, ()=> {
            m.openPopup();
        });
    }

    private createNavigationItem(location: ILocation, index: number) {
        const navigationItem = document.createElement('li');
        navigationItem.className = 'cases-map__navigation-item';

        const button = document.createElement('button');
        button.id = location.id;
        button.type = 'button';
        button.className = 'tag tag--cases-map';
        button.innerText = location.name;

        navigationItem.appendChild(button);

        navigationItem.addEventListener("click", () => {
            this.mapLocationTagsClickHandler(button, index);
        });

        return navigationItem;
    }

    private makeNavigationListItems(locations: ILocations) {
        locations.items.forEach((location, index) => {
            this.locationList.appendChild(this.createNavigationItem(location, index));
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
        CasesMapBox.appendScript(scriptText);

        /**
         * @see: https://medium.com/front-end-weekly/webpack-and-dynamic-imports-doing-it-right-72549ff49234
         * @see: https://webpack.js.org/api/module-methods/#magic-comments
         */
        import(/* webpackMode: "lazy-once" */<any>"leaflet.markercluster/dist/leaflet.markercluster").then(()=> {
            const map = this.makeMap();
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

    private loadMap() {
        const leafletCSSURL = `${this.leafletCDNURL}/dist/leaflet.css`;
        const leafletCSS = loadCSS(leafletCSSURL);

        leafletCSS.onload = () => this.leafletCSSCallback();
    }

    private initializeMapWhenInViewport() {
        if(Viewport.isInViewport(this.rootElement, 500)) {
            this.loadMap();
            window.removeEventListener("scroll", this.scrollHandler);
        }
    }

    private scrollHandler = debounce(()=> {
        this.initializeMapWhenInViewport()
    },10);

    public init() {
        window.addEventListener("scroll", this.scrollHandler);
        // will remove the scroll event handler if it's
        // already in viewport on opening the page
        // that's why we call it after the adding of the event handler
        this.initializeMapWhenInViewport();
    }

}
