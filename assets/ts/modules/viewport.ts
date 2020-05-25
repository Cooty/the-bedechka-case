export default class Viewport {
    public static isInViewport(el: HTMLElement, threshold: number = 200) {
        const bounding = el.getBoundingClientRect();

        console.log(threshold);

        return (
            bounding.top + threshold >= 0 &&
            bounding.left + threshold >= 0 &&
            bounding.bottom - threshold <= (window.innerHeight || document.documentElement.clientHeight) &&
            bounding.right - threshold <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
}