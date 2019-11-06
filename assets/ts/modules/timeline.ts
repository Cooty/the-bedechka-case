import {Responsive} from "../utils/responsive";

export class Timeline {

    private readonly timeline: Element;
    private readonly moverModifierClassName: string;
    private backButton: Element;
    private forwardButton: Element;
    private scrollPlane: Element;
    private firstStep: Element;

    constructor(timeline: Element) {
        this.timeline = timeline;

        if(!this.timeline) {
            return;
        }

        this.moverModifierClassName = 'hide-mover';

        this.getElements();

        if(this.backButton && this.forwardButton && this.scrollPlane) {
            this.init();
        }

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

    private moveForward() {
        const moveIncrement = this.getStepWidth();
    }

    private moveBackward() {
        const moveIncrement = this.getStepWidth();
    }

    private isEnd(): boolean {
        return this.scrollPlane.scrollLeft === this.scrollPlane.scrollWidth;
    }

    private isStart(): boolean {
        return this.scrollPlane.scrollLeft > this.getStepWidth();
    }

    private showBackButton() {
        // hide the back button when reaching the start of the timeline
    }

    private hideBackButton() {
        // hide the back button when reaching the start of the timeline
    }

    private showForwardButton() {
        // hide the forward button when reaching the end of the timeline
    }

    private hideForwardButton() {
        // hide the forward button when reaching the end of the timeline
    }

    private addEventHandlers() {

    }

    public init() {
        console.log('Timeline module reporting for duty');
    }
}