import * as React from "react";
import CasesContext from "../contexts/cases";
import {Outlet, useLocation} from "react-router-dom";
import ILocation from "../../ts/map-common/ILocation";
import MapCasesNav from "../components/MapCaseNav/MapCasesNav";
import MapContentContainer from "../components/MapContentContainer/MapContentContainer";
import appendHrefWithHash from "../../ts/utils/append-href-with-hash";
import TranslationsContext from "../contexts/translations";

const Home: React.VFC = () => {
    const cases = React.useContext<ILocation[]>(CasesContext);
    const translations = React.useContext<Record<string, any>>(TranslationsContext);
    const location = useLocation();

    React.useEffect(() => {
        const langLink = document.querySelector(".language-link")! as HTMLAnchorElement;
        const hash = location.pathname !== "/" ? location.pathname : "";
        appendHrefWithHash(langLink, hash);

        if(location.pathname === "/") {
            document.title = `${translations?.pageTitles.map} - ${translations?.pageTitles?.suffix}`;
        }
    }, [location, document]);

    return (
        <React.Fragment>
            <MapCasesNav cases={cases} />
            <MapContentContainer>
                <Outlet />
            </MapContentContainer>
        </React.Fragment>
    )
}

export default Home;
