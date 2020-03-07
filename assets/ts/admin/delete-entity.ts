import * as $ from "jquery";
import {setCookie} from "./utils/cookie";

const deleteRowFromList = (id: string)=> {
    const row:JQuery = $(`#js-row-${id}`);

    row.remove();
};

const deleteEntity = (id: string, url: string) => {
    return $.ajax({
        url,
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify({id})
    });
};

const redirectOnForbiddenResponse = (status: number)=> {
    if(status === 403) {
        setCookie("tbc_1_implicitLogout", "1", null);
        setCookie("tbc_1_redirectUri", window.location.pathname, null);

        window.location.href = window._config.loginUri;
    }
};

export const init = () => {
    const deleteButtons = $(".js-delete-entity-button");

    if(!deleteButtons.length) return;

    deleteButtons.on("click.delete", (e: Event)=> {
        const button:JQuery<EventTarget> = $(e.currentTarget);
        const id: string = button.data("id");
        const confirm = window.confirm(`${window._config.deleteEntityConfirmMessage} ${button.data("name")}`);

        if(confirm) {
            deleteEntity(id, button.data("url"))
                .then(() => {
                    deleteRowFromList(id)
                })
                .fail((jqXHR: any) => {
                    redirectOnForbiddenResponse(jqXHR.status);
                }).catch(e=> console.error(e));
        }
    });
};