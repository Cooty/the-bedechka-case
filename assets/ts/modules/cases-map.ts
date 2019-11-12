import {ILocations} from "../interfaces/ILocations";

export class CasesMap {
    private readonly mapSelector: string;
    private readonly locationList: string;

    constructor() {
        this.mapSelector = "js-cases-map";
        this.locationList = "js-cases-map-location-list";

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