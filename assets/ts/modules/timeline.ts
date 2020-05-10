import Responsive from "../utils/responsive";
import {debounce} from "../utils/debounce";
import "../../scss/layout/_timeline.scss"

export default class Timeline {
    private readonly timeline: HTMLElement;
    private readonly moverModifierClassName: string;
    private backButton: HTMLElement;
    private forwardButton: HTMLElement;
    private scrollPlane: HTMLElement;
    private firstStep: HTMLElement;
    private isInitialized: boolean = false;

    constructor(timeline: HTMLElement) {
        this.timeline = timeline;

        if(!this.timeline) {
            return;
        }

        this.moverModifierClassName = 'hide-mover';

        this.getElements();

        if(this.hasAllElements()) {
            this.init();
            window.addEventListener("resize", debounce(this.init.bind(this), 100));
        }

    }

    private hasAllElements(): boolean {
        return !!(this.backButton && this.forwardButton && this.scrollPlane);
    }

    private getElements() {
        this.backButton = this.timeline.querySelector('.js-timeline-back');
        this.forwardButton = this.timeline.querySelector('.js-timeline-forward');
        this.scrollPlane = this.timeline.querySelector('.js-timeline-steps');
        this.firstStep = this.scrollPlane.querySelector('.timeline__step:first-of-type');
    }

    private getStepWidth(): number
    {
        if(window.getComputedStyle) {
            const width = this.firstStep.clientWidth;
            const marginRight = window.getComputedStyle(this.firstStep)
                .getPropertyValue('margin-right');

            return width + parseInt(marginRight, 10);
        } else {
            return this.firstStep.clientWidth;
        }
    }

    private scrollTo(element:HTMLElement, to:number, duration:number) {
        if (duration <= 0)
            return;

        const tick = 5;
        const difference = to - element.scrollLeft;
        const perTick = difference / duration * tick;

        setTimeout(() => {
            element.scrollLeft = element.scrollLeft + perTick;
            if (element.scrollLeft === to)
                return;

            this.scrollTo(element, to, duration - tick);
        }, tick);
    }

    private moveForward() {
        const moveIncrement = this.getStepWidth();
        const scrollTo = moveIncrement + this.scrollPlane.scrollLeft;

        this.scrollTo(this.scrollPlane, scrollTo, 200);
    }

    private moveBackward() {
        const moveIncrement = this.getStepWidth();
        const scrollTo = this.scrollPlane.scrollLeft - moveIncrement;

        this.scrollTo(this.scrollPlane, scrollTo, 200);
    }

    private isEnd(): boolean {
        return this.scrollPlane.scrollLeft === (this.scrollPlane.scrollWidth - this.scrollPlane.offsetWidth);
    }

    private isStart(): boolean {
        return this.scrollPlane.scrollLeft === 0;
    }

    private showBackButton() {
        this.backButton.classList.remove(this.moverModifierClassName);
    }

    private hideBackButton() {
        this.backButton.classList.add(this.moverModifierClassName);
    }

    private showForwardButton() {
        this.forwardButton.classList.remove(this.moverModifierClassName);
    }

    private hideForwardButton() {
        this.forwardButton.classList.add(this.moverModifierClassName);
    }

    private scrollHandler() {
        if(this.isEnd()) {
            this.hideForwardButton();
            this.showBackButton();
        } else if(this.isStart()) {
            this.hideBackButton();
            this.showForwardButton();
        } else {
            this.showBackButton();
            this.showForwardButton();
        }
    }

    private addEventHandlers() {
        this.scrollPlane.addEventListener("scroll", debounce(this.scrollHandler.bind(this), 50));

        this.forwardButton.addEventListener("click", this.moveForward.bind(this));
        this.backButton.addEventListener("click", this.moveBackward.bind(this));
    }

    public init() {
        if(!this.isInitialized && Responsive.gteDesktop()) {
            this.addEventHandlers();
            this.isInitialized = true;
        }
    }
}