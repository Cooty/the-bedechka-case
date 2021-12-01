import isWebPSupported from "../utils/is-webp-supported";

function init() {
    const root = document.querySelector("html");

    isWebPSupported().then((resolve)=> {
        if(!resolve) {
            root.classList.add("no-webp");
        }
    });
}

export default init;
