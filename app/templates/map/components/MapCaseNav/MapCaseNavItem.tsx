import * as React from "react";
import {NavLink} from "react-router-dom";
import routeNames from "../../routes/route-names";

interface MapCaseNavItemProps {
    imageUrl: string,
    title: string,
    id: string,
    clickHandler: () => void
}

const MapCaseNavItem: React.FC<MapCaseNavItemProps> = ({
    imageUrl,
    title,
    id,
    clickHandler
}) => {
    return (
        <li className="map__cases-nav-item">
            <NavLink
                to={`/${routeNames.CASE_DETAIL}/${id}`}
                onClick={clickHandler}
                className={({isActive}) => isActive ? "active" : null}
            >
                <div className="map__cases-nav-item-media">
                    <div className="map__cases-nav-item-media-body">
                        <strong className="map__cases-nav-item-title">
                            {title}
                        </strong>
                    </div>
                    <div
                        className="map__cases-nav-item-media-img"
                        title={title}
                        style={{backgroundImage: `url(${imageUrl})`}}
                    />
                </div>
            </NavLink>
        </li>
    );
}

export default MapCaseNavItem;
