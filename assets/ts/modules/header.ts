import {Responsive} from '../utils/responsive';
import {$} from '../utils/bling';

export class Header {
    public init() {
        console.log('Header module reporting for duty');
        if(Responsive.getViewportWidth() < Responsive.breakpoints.desktop) {
            this.addScrollEvent();
        }
    }

    private header = $('.header');

    private addScrollEvent() {
        let lastScrollTop = 0;
        const modifierClass = 'header--pulled-up';

        window.on('scroll', ()=> {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if(scrollTop > lastScrollTop) {
                this.header.classList.add(modifierClass);
            } else {
                this.header.classList.remove(modifierClass);
            }

            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; // For Mobile or negative scrolling
        });
    }


}