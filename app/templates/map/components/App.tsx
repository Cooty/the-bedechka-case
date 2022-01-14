import * as React from "react";
import ILocation from "../../ts/map-common/ILocation";
import { getCases } from "../services/api";
import CasesContext from "../contexts/cases";
const CasesMap = React.lazy(() => import("./CasesMap"));

const App: React.VFC = () => {
    const [cases, setCases] = React.useState<ILocation[]>([]);
    const mounted = React.useRef(false);

    React.useEffect(() => {
        mounted.current = true;
        getCases()
            .then(cases => {
                if(mounted.current) {
                    setCases(cases.items);
                }
            })
        return () => {mounted.current = false};
    }, [])

    return (
        <React.Suspense fallback={<span className="spinner" />}>
            <CasesContext.Provider value={cases}>
                <CasesMap />
            </CasesContext.Provider>
        </React.Suspense>
    );
}

export default App;
