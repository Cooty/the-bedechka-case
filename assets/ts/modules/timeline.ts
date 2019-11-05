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

    public init() {
        console.log('Timeline module reporting for duty');
    }
}