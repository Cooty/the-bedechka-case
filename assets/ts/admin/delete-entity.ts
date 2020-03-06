import * as $ from "jquery";

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
        window.location.href = `${window._config.loginUri}&
            redirectUri=${encodeURIComponent(window.location.pathname)}&
            implicitLogout=1`;
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