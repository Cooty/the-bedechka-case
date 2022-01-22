import * as React from "react";
import Close from "../icons/Close";
import Menu from "../icons/Menu";

interface MapCaseNavTogglerProps {
    open: boolean,
    onClickHandler: (e: React.MouseEvent<HTMLButtonElement>) => void
}

const MapCaseNavToggler: React.FC<MapCaseNavTogglerProps> = ({
    open,
    onClickHandler
}) => {
    return (
        <button
            className="map__cases-nav-toggler"
            onClick={onClickHandler}
            type="button"
        >
            { open ? (<Close />) : (<Menu />) }
        </button>
    );
}

export default MapCaseNavToggler;
