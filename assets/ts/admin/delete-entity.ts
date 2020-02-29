import * as $ from "jquery";

const deleteRowFromList = (id: string)=> {
    const row:JQuery = $(`#js-row-${id}`);

    row.remove();
};

const deleteEntity = (id: string, url: string)=> {
    return $.ajax({
        url,
        method: "POST",
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify({id})
    }).then(() => id);
};

const doDelete = async (id: string, url: string) => {
    deleteEntity(id, url);
};

export const init = () => {
    const deleteButtons = $(".js-delete-entity-button");

    if(!deleteButtons.length) return;

    deleteButtons.on("click.delete", (e: Event)=> {
        const button:JQuery<EventTarget> = $(e.currentTarget);
        const id: string = button.data("id");

        doDelete(button.data("id"), button.data("url"))
            .then(()=> {deleteRowFromList(id)})
            .catch((data: any)=> {console.error(data)});
    });
};