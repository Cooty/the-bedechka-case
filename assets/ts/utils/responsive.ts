type BreakPointMap = Record<string, number>;

export class Responsive {

    public static breakpoints: BreakPointMap = {
        desktop: 1024,
        phablet: 600,
        tablet: 768,
        smallest: 320
    };

    public static getViewportWidth(): number {
        return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    }
}