import {ILocations} from "../interfaces/ILocations";

export class CasesMap {
    private readonly rootElement: HTMLElement;
    private readonly map: HTMLElement;
    private readonly locationList: HTMLElement;

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
        const response = await fetch('/dummy-locations.json');
        return await response.json();
    }

    public init() {
        try {
            CasesMap.getLocations().then(data=> console.log(data));
        } catch(e) {
            console.error(e);
        }

        console.log("cases map is reporting for duty");
    }

}