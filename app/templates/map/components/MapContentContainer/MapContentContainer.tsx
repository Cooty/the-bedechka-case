import * as React from "react";
import "./MapContentContainer.scss";

const MapContentContainer: React.FC<any> = ({
    children,
    ...props
}) => {
    return (
        <section className="map__content-container" {...props}>
            {children}
        </section>
    )
}

export default MapContentContainer;
