import * as React from "react";
import {HashRouter, Routes, Route} from "react-router-dom";
import Home from "../routes/Home";
import Cases from "../routes/Cases";
import CaseDetails from "../routes/CaseDetails";
import routeNames from "../routes/route-names";

const Router: React.VFC = () => {
    return (
        <HashRouter>
            <Routes>
                <Route path={routeNames.HOME} element={<Home />}>
                    <Route index element={<Cases />} />
                    <Route path={`${routeNames.CASE_DETAIL}/:id`} element={<CaseDetails />} />
                </Route>
            </Routes>
        </HashRouter>
    );
}

export default Router;
