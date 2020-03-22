import Responsive from '../utils/responsive';

export default class Header {
    private readonly header: HTMLElement;

    constructor(header: HTMLElement) {
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

        const scrollListener = ()=> {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            this.toggleHeader(scrollTop, lastScrollTop);

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        };

        window.addEventListener('scroll', scrollListener);
    }

    public init() {
        if(Responsive.ltDesktop()) {
            this.addScrollEvent();
        }
    }

}