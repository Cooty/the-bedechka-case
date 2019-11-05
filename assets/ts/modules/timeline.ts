export class Timeline {

    private readonly timeline: Element;
    private backButton: Element;
    private forwardButton: Element;
    private scrollPlane: Element;

    constructor(timeline: Element) {
        this.timeline = timeline;

        if(!this.timeline) {
            return;
        }

        this.getElements();

        if(this.backButton && this.forwardButton && this.scrollPlane) {
            this.init();
        }

    }

    private getElements() {
        this.backButton = this.timeline.querySelector('.js-timeline-back');
        this.forwardButton = this.timeline.querySelector('.js-timeline-forward');
        this.scrollPlane = this.timeline.querySelector('.js-timeline-steps');
    }

    private getStepWidth() {
        // get the number of pixels which to move the timeline
    }

    private moveForward() {
        // move the scroll plane by one slide forward
    }

    private moveBackward() {
        // move the scroll plane by one slide backward
    }

    private hideBackButton() {
        // hide the back button when reaching the start of the timeline
    }

    private hideForwardButton() {
        // hide the forward button when reaching the end of the timeline
    }

    private addEventHandlers() {
        // hook up all functions to events
    }

    public init() {
        console.log('Timeline module reporting for duty');
    }
}