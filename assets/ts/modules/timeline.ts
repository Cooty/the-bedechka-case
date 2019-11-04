export class Timeline {

    private readonly timeline: Element;
    private readonly backButton: Element;
    private readonly forwardButton: Element;
    private readonly scrollPlane: Element;

    constructor(timeline: Element) {
        this.timeline = timeline;

        if(!this.timeline) {
            return;
        }

        this.backButton = this.timeline.querySelector('.js-timeline-back');
        this.forwardButton = this.timeline.querySelector('.js-timeline-forward');
        this.scrollPlane = this.timeline.querySelector('.js-timeline-steps');

        if(this.backButton && this.forwardButton && this.scrollPlane) {
            this.init();
        }

    }

    public init() {
        console.log('Timeline module reporting for duty');
    }
}