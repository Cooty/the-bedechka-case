import {Responsive} from '../utils/responsive';

export class Header {
    private readonly header: Element;

    constructor(header: Element) {
        this.header = header;

        if(this.header) {
            this.init();
        }
    }

    private toggleHeader(scrollTop: number, lastScrollTop: number): boolean {
        const modifierClass = 'header--pulled-up';

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

        window.addEventListener('scroll', ()=> {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            this.toggleHeader(scrollTop, lastScrollTop);

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        });
    }

    public init() {
        if(Responsive.getViewportWidth() < Responsive.breakpoints.desktop) {
            this.addScrollEvent();
        }
    }

}