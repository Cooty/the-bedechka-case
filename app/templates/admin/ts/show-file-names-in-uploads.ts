import * as $ from "jquery";

export const init = ()=> {
    const fileInput:JQuery = $(".custom-file input[type='file']");

    if(fileInput.length) {
        fileInput.each(function() {
            const $input = $(this);
            $input.on("change.bsFileUpload", ()=> {
                $input
                    .next(".custom-file-label")
                    .html($input.val().toString());
            });
        });
    }
};