type BreakPointMap = Record<string, number>;

export class Responsive {
    // Copied from ~/assets/scss/config/_breakpoints.scss
    public static breakpoints: BreakPointMap = {
        desktop: 1024,
        phablet: 600,
        tablet: 768,
        smallest: 320
    };

    public static getViewportWidth(): number {
        return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }

    public static ltDesktop(): boolean {
        return this.getViewportWidth() < this.breakpoints.desktop;
    }

    public static gteDesktop(): boolean {
        return this.getViewportWidth() >= this.breakpoints.desktop;
    }
}