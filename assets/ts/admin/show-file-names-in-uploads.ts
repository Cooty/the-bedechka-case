const $ = require("jquery");

export const init = ()=> {
    $(".custom-file input[type='file']").on("change",(e: Event)=> {
        //get the file name
        const $fileInput = $(e.currentTarget);
        const fileName = $fileInput.val();
        //replace the "Choose a file" label
        $fileInput.next(".custom-file-label").html(fileName);
    });
};