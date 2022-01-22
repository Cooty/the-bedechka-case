import * as React from "react";
import { useParams } from "react-router-dom";
import MapCasesDetails from "../components/MapCasesDetails/MapCasesDetails";
import CasesContext from "../contexts/cases";
import TranslationsContext from "../contexts/translations";
import ILocation from "../../ts/map-common/ILocation";

const CaseDetails: React.VFC = () => {
    const { id } = useParams();
    const cases = React.useContext<ILocation[]>(CasesContext);
    const translations = React.useContext<Record<string, any>>(TranslationsContext);
    const mapCase = cases.filter((item) => item.id === id)[0];

    React.useEffect(() => {
        if(mapCase && mapCase.name) {
            document.title = `${mapCase.name} - ${translations?.pageTitles?.suffix}`;
        }
    }, [document, mapCase]);

    return mapCase ? <MapCasesDetails mapCase={mapCase} /> : null;
}

export default CaseDetails;
