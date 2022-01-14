import * as React from "react";
import { HashRouter, Routes, Route } from "react-router-dom";
import Home from "../routes/Home";
import Map from "../routes/Map";
import CaseDetails from "../routes/CaseDetails";

const CasesMap: React.VFC = () => {
    return (
        <HashRouter>
            <Routes>
                <Route path="/" element={<Home />}>
                    <Route index element={<Map />} />
                    <Route path="case/:caseId" element={<CaseDetails />} />
                </Route>
            </Routes>
        </HashRouter>
    );
}

export default CasesMap;
