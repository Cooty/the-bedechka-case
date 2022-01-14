import * as React from "react";
import { useParams } from "react-router-dom";

const CaseDetails: React.VFC = () => {
    const { caseId } = useParams();

    return (
        <div style={{backgroundColor: '#fff', outline: '1px solid blue'}}>
            <h1>
                This is case: {caseId}
            </h1>
        </div>
    )
}

export default CaseDetails;
