import * as React from "react";
import {Marker} from "react-leaflet";
import {LatLngExpression} from "leaflet";
import {useNavigate} from "react-router-dom";
import CasesContext from "../contexts/cases";
import ILocation from "../../ts/map-common/ILocation";
import CaseMap from "../components/CaseMap/CaseMap";
import routeNames from "./route-names";

const Cases: React.VFC = () => {
    const cases = React.useContext<ILocation[]>(CasesContext);
    const navigate = useNavigate();

    return (
        <CaseMap>
            {cases.map((mapCase) => {
                const position = [
                    mapCase.coords.latitude,
                    mapCase.coords.longitude
                ] as LatLngExpression;

                return (
                    <Marker
                        position={position}
                        key={mapCase.id}
                        eventHandlers={{
                            click: () => {
                                navigate(`/${routeNames.CASE_DETAIL}/${mapCase.id}`)
                            }
                        }}
                    />
                );
            })}
        </CaseMap>
    );
};

export default Cases;
