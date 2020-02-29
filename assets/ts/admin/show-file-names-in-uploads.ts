import * as $ from "jquery";

export const init = ()=> {
    const fileInput:JQuery = $(".custom-file input[type='file']");

    if(fileInput.length) {
        fileInput.on("change.bsFileUpload",()=> {
            const fileName = fileInput.val().toString();

            fileInput.next(".custom-file-label").html(fileName);
        });
    }
};