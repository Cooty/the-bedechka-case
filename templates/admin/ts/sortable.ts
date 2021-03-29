import "../scss/_sortable.scss";
import "../scss/_drag-handle.scss";
// @ts-ignore
import * as sortable from "html5sortable/dist/html5sortable.es.js";
import * as $ from "jquery";

type ReorderData = {
    order: number,
    id: string
};

const getReorderedList = (list: HTMLCollection):ReorderData[] => {
    return Array.from(list)
        .map((child: HTMLLIElement, index: number):ReorderData => {
            return {
                order: index + 1, // has to be indexed from 1 cause MySQL can't store 0 as an integer
                id: child.dataset.id
            };
        });
};

const sortUpdateHandler = (e: CustomEvent) => {
    const list = e.detail?.origin?.container?.children;
    const reorderedList = getReorderedList(list);

    // We're sending a parallel AJAX calls to the server, this way it's easier to
    // update the order, since we only process ONE request on the server
    // instead of doing batch processing
    const ajaxCalls = reorderedList.map((reorderData) => {
        return sendUpdateRequest(reorderData);
    });

    // https://api.jquery.com/jquery.when/
    // No error handling - YOLO :)
    $.when(ajaxCalls);
};

const sendUpdateRequest = (updateData: ReorderData) => {
    return $.ajax({
        url: "/admin/reorder/crew-members",
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(updateData)
    });
};

export const init = () => {
    sortable.default(".js-sortable", {
        forcePlaceholderSize: true
    })[0].addEventListener("sortupdate", sortUpdateHandler, false);
};
