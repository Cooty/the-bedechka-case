import debounce from "../utils/debounce";
import Viewport from "../utils/viewport";

export default class LazyLoading {
    readonly elements: HTMLElement[] = [];

    constructor(selector: string) {
        this.elements = Array.from(document.querySelectorAll(selector));

        if(this.elements.length) {
            this.init();
        }
    }

    private static doLazyLoading(element: HTMLElement) {
        const dataSrc = element.getAttribute("data-src");
        const dataSrcSet = element.getAttribute("data-srcset");

        if(dataSrc) {
            element.setAttribute("src", dataSrc);
        } else if(dataSrcSet) {
            element.setAttribute("srcset", dataSrcSet);
        } else {
            element.classList.add("lazy-loading-loaded");
        }

        element.dataset.loaded = "1";
    }

    private checkForElements() {
        if(!this.elements.length) {
            return;
        }

        this.elements.forEach(element=> {
            if(element.dataset.loaded) {
                return;
            }

            const {lazyLoadThreshold} = element.dataset;
            const args = [element];

            if(lazyLoadThreshold) {
                args.push(JSON.parse(lazyLoadThreshold));
            }

            if(Viewport.isInViewport.apply(null, args)) {
                LazyLoading.doLazyLoading(element);
            }
        });
    }

    private scrollHandler() {
        this.checkForElements();
    }

    private init() {
        this.checkForElements();

        window.addEventListener(
            "scroll",
            debounce(this.scrollHandler.bind(this), 10),
            false
        );
    }
};