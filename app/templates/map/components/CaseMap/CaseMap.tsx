import * as React from "react";
import { MapContainer, TileLayer, ZoomControl, useMap } from "react-leaflet";
import MarkerClusterGroup from "react-leaflet-markercluster";
import {control, LatLngExpression} from "leaflet";
import "./CasesMap.scss";
import MapSettings from "../../../ts/map-common/map-settings";
import openStreetMapsAttribution from "../../../ts/map-common/open-street-maps-attribution.html";
import zoom = control.zoom;

interface CaseMapProps {
    className?: "",
    center?: LatLngExpression,
    zoom?: number,
    maxZoom?: number,
    minZoom?: number,
    tileProviderUrl?: string
}

// For whatever reason react-leaflet can't properly resolve these images
// from the installed package, so we'll just download them from unpgk CDN
const DefaultIcon = window.L.icon({
    iconUrl: "https://unpkg.com/leaflet@1.5.1/dist/images/marker-icon.png",
    shadowUrl: "https://unpkg.com/leaflet@1.5.1/dist/images/marker-shadow.png"
});

window.L.Marker.prototype.options.icon = DefaultIcon;

const ChangeView: React.FC<{ center: LatLngExpression, zoom: number }> = ({ center, zoom}) => {
    const map = useMap();
    map.setView(center, zoom);
    return null;
}

const CaseMap: React.FC<CaseMapProps> = ({
    className = '',
    children,
    center = MapSettings.defaultCenter,
    zoom = MapSettings.defaultZoom,
    maxZoom = MapSettings.defaultMaxZoom,
    minZoom = MapSettings.defaultMinZoom,
    tileProviderUrl = MapSettings.defaultTileProviderUrl
}) => {
    return (
        <MapContainer
            className={`map__cases-map ${className}`}
            center={center}
            zoom={zoom}
            maxZoom={maxZoom}
            minZoom={minZoom}
            zoomControl={false}
        >
            <TileLayer
                url={tileProviderUrl}
                attribution={openStreetMapsAttribution()}
            />
            <ZoomControl position="bottomright" />
            <ChangeView center={center} zoom={zoom} />
            <MarkerClusterGroup>
                {children}
            </MarkerClusterGroup>
        </MapContainer>
    )
}

export default CaseMap;
