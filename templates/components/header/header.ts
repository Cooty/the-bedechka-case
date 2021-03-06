import Responsive from "../../../assets/ts/utils/responsive";
// Need to import the file so that Twig can reference it from the public/build folder
// as an <img src="{{ asset('build/images/logo@2x.png') }}">
// with the build hash attached to the file.
// Webpack Encore will do the copying, but for that it has to be include in either TS or SCSS
import "./logo/logo@2x.png";
// SCSS files from this component and it's partials are included manually in critical-path.scss
// this way we can include it as inline style in the Twig templates

export default class Header {
    private readonly header: HTMLElement;

    constructor(header: HTMLElement) {
        this.header = header;

        if(this.header) {
            this.init();
        }
    }

    private toggleHeader(scrollTop: number, lastScrollTop: number): boolean {
        const modifierClass = "header--pulled-up";

        if(scrollTop > lastScrollTop && scrollTop >= this.header.clientHeight) {
            this.header.classList.add(modifierClass);
            return true;
        } else {
            this.header.classList.remove(modifierClass);
            return false;
        }
    }

    private addScrollEvent() {
        let lastScrollTop = 0;

        const scrollListener = ()=> {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            this.toggleHeader(scrollTop, lastScrollTop);

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        };

        window.addEventListener("scroll", scrollListener);
    }

    private init() {
        if(Responsive.ltDesktop()) {
            this.addScrollEvent();
        }
    }

}