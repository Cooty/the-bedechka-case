import {Responsive} from '../utils/responsive';

export class Header {

    private header = document.querySelector('.js-header');

    private toggleHeader(scrollTop: number, lastScrollTop: number): boolean {
        const modifierClass = 'header--pulled-up';

        if(scrollTop > lastScrollTop) {
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