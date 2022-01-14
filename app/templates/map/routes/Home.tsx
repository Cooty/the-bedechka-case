import * as React from "react";
import CasesContext from "../contexts/cases";
import { Outlet } from "react-router-dom";
import ILocation from "../../ts/map-common/ILocation";
import MapCasesNav from "../components/MapCasesNav";

const Home: React.VFC = () => {
    const cases = React.useContext<ILocation[]>(CasesContext);

    return (
        <React.Fragment>
            <MapCasesNav cases={cases} />
            <section className="map__container">
                <Outlet />
            </section>
        </React.Fragment>
    )
}

export default Home;
