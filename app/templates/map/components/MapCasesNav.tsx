import React from "react";
import ILocation from "../../ts/map-common/ILocation";
import {NavLink} from "react-router-dom";

const MapCasesNav: React.FC<{cases: ILocation[]}> = ({cases}) => {
    const {open, setOpen} = React.useState<boolean>(false);
    const toggle = setOpen((prevState) => setOpen(!!prevState));

    return (
        <nav className={`map__cases-nav ${open ? "map__cases-nav--open" : ""}`}>
            <button
                className="map__cases-nav-toggler"
                onClick={toggle}
                type="button"
            >
                x
            </button>
            <div className="map__cases-nav-scroll-plane">
                <ul className="map__cases-nav-list">
                    {cases.map((mapCase) => {
                        return (
                            <li key={mapCase.id} className="map__cases-nav-list-item">
                                <NavLink to={`/case/${mapCase.id}`}>
                                    {mapCase.name}
                                </NavLink>
                            </li>
                        )
                    })}
                </ul>
            </div>
        </nav>
    );
}

export default MapCasesNav;
