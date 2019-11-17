import {ILocations} from "../interfaces/ILocations";
import {ILocation} from "../interfaces/ILocation";

export class CasesMap {
    private readonly rootElement: HTMLElement;
    private readonly map: HTMLElement;
    private readonly locationList: HTMLElement;
    private locationsData: ILocations;
    private casesMap: object;

    constructor(rootElement: HTMLElement) {
        if(!rootElement) {
            return;
        }
        this.rootElement = rootElement;
        this.locationList = this.rootElement.querySelector(".js-cases-map");
        this.map = this.rootElement.querySelector(".js-cases-map-location-list");
        this.init();
    }

    private static async getLocations(): Promise<ILocations> {
        const locationsAPIURL: RequestInfo = "/dummy-locations.json";

        const response = await fetch(locationsAPIURL);
        if(response.status !== 200) {
            throw new Error(`The URL: ${locationsAPIURL} responded with ${response.status}`);
        } else {
            return await response.json();
        }
    }

    private static async loadLeafletJS() {
        const leafletJSURL: RequestInfo = "https://unpkg.com/leaflet@1.5.1/dist/leaflet.js";

        const response = await fetch(leafletJSURL);
        if(response.status !== 200) {
            throw new Error(`The URL: ${leafletJSURL} responded with ${response.status}`);
        } else {
            return await response.text();
        }
    }

    private static appendScript(text: string) {
        const script = document.createElement("script");
        script.id = "leafletJS";
        script.innerText = text;

        document.body.appendChild(script);
    }

    private handleError(e: Error) {
        console.error(e);
        this.rootElement.style.display = "none";
    }

    private makeMap() {
        const mapContainerId: string = "js-cases-map-container";
        const mapCenter = [42.43897, 25.6289515]; // coords of Park Bedechka
        const defaultZoom = 7;
        // @ts-ignore
        const casesMap = window.L.map(mapContainerId).setView(mapCenter, defaultZoom);
        // @ts-ignore
        window.L.tileLayer("https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}", {
            attribution: "Map data &copy; <a href=\"https://www.openstreetmap.org/\">OpenStreetMap</a> contributors, <a href=\"https://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, Imagery © <a href=\"https://www.mapbox.com/\">Mapbox</a>",
            maxZoom: 19,
            minZoom: 5,
            id: "mapbox.streets",
            // @ts-ignore
            accessToken: window._config.mapboxAccessToken
        }).addTo(casesMap);

        this.casesMap = casesMap;
    }

    private static makePopupContent(location: ILocation): string {
        return `
            <img src="${location.image}" alt="${location.name}" />
            <h1>${location.name}</h1>
            <p>${location.description}</p>
            ${location.link ? `<a href="${location.link}" target="_blank" rel="noopener noreferer">${location.link}</a>`: ""}
        `;
    }

    private addLocationsToMap() {
        console.log(this.casesMap);
        this.locationsData.locations.map((location: ILocation) => {
            // @ts-ignore
            const marker = L.marker([location.coords.lng, location.coords.lat])
                .addTo(this.casesMap);
            marker.bindPopup(CasesMap.makePopupContent(location));
        });
    }

    public init() {
        try {
            const leafletCSSURL = "https://unpkg.com/leaflet@1.5.1/dist/leaflet.css";
            // @ts-ignore
            const leafletCSS = window.loadCSS(leafletCSSURL);
            // @ts-ignore
            window.onloadCSS(leafletCSS, ()=> {
                try {
                    CasesMap.getLocations().then(locationsData=> {
                        this.locationsData = locationsData;
                    }).then(()=> {
                        CasesMap.loadLeafletJS().then((scriptText)=> {
                            CasesMap.appendScript(scriptText);
                            this.makeMap();
                            this.addLocationsToMap();
                        }).catch((e) => {
                            this.handleError(e);
                        });
                    });
                } catch (e) {
                    this.handleError(e);
                }
            });
        } catch(e) {
            this.handleError(e);
        }
    }

}