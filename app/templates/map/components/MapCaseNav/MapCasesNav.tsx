import * as React from "react";
import ILocation from "../../../ts/map-common/ILocation";
import MapCaseNavItem from "./MapCaseNavItem";
import MapCaseNavToggler from "./MapCaseNavToggler";
import "./MapCaseNav.scss";

const MapCasesNav: React.FC<{cases: ILocation[]}> = ({cases}) => {
    const [open, setOpen] = React.useState<boolean>(false);
    const toggle = (_: React.MouseEvent<HTMLButtonElement>) => {
        setOpen((prevState) => !prevState);
    }

    return (
        <React.Fragment>
            <nav className={`map__cases-nav ${open ? "map__cases-nav--open" : ""}`}>
                <MapCaseNavToggler open={open} onClickHandler={toggle} />
                <div className="map__cases-nav-scroll-plane">
                    <ul className="map__cases-nav-list">
                        {cases.map((mapCase) => {
                            return (
                                <MapCaseNavItem
                                    key={mapCase.id}
                                    title={mapCase.name}
                                    imageUrl={mapCase.image}
                                    id={mapCase.id}
                                    clickHandler={() => {setOpen(false)}}
                                />
                            )
                        })}
                    </ul>
                </div>
            </nav>
            { open ? (
                <div
                    className="map__cases-nav-backdrop"
                    onClick={(_) => {setOpen(false)}}
                />)
                :
                null
            }
        </React.Fragment>
    );
}

export default MapCasesNav;
