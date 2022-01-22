import {LatLngExpression} from "leaflet";

const MapSettings = {
    defaultZoom: 7,
    defaultMaxZoom: 18,
    defaultMinZoom: 5,
    defaultCenter: [42.43897, 25.6289515] as LatLngExpression,
    defaultTileProviderUrl: "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
} as const;

Object.freeze(MapSettings);

export default MapSettings;
