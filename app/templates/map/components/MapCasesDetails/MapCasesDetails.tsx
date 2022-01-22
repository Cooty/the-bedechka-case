import * as React from "react";
import {Marker} from "react-leaflet";
import {Link} from "react-router-dom";
import {LatLngExpression} from "leaflet";
import routeNames from "../../routes/route-names";
import ILocation from "../../../ts/map-common/ILocation";
import "./MapCasesDetails.scss";
import CaseMap from "../CaseMap/CaseMap";
import TranslationsContext from "../../contexts/translations";
import ArrowBack from "../icons/ArrowBack";

const MapCasesDetails: React.FC<{mapCase: ILocation}> = ({mapCase}) => {
    const position = [
        mapCase.coords.latitude,
        mapCase.coords.longitude
    ] as LatLngExpression;
    const translations = React.useContext<Record<string, any>>(TranslationsContext);

    return (
        <article className="map__cases-details">
            <div className="map__cases-details-info">
                <div className="map__cases-details-back-container">
                    <Link to={routeNames.HOME} className="map__cases-details-back">
                        <ArrowBack />
                        {translations?.navigation?.backToMapIndex}
                    </Link>
                </div>
                <h1 className="map__cases-details-title">
                    {mapCase.name}
                </h1>

                <div className="map__cases-details-description">
                    {mapCase.description}
                </div>
                {mapCase.link && (<p className="mt-2">
                    <a href={mapCase.link}
                       className="map__cases-details-link"
                       target="_blank"
                       rel="noreferer noopener"
                    >
                        {mapCase.link}
                    </a>
                </p>)}

                {mapCase.image && (
                    <img className="map__cases-details-img mt-2"
                         src={mapCase.image}
                         alt={mapCase.name}
                    />
                )}
            </div>
            <div className="map__cases-details-map">
                <CaseMap center={position}>
                    <Marker position={position} />
                </CaseMap>
            </div>
        </article>
    );
}

export default MapCasesDetails;
