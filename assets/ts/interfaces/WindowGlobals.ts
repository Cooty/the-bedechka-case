import IConfig from "./IConfig";

export {};

declare global {
    interface Window {
        _config?: IConfig,
        _translations?: any,
        L?: any,
        YT?: any,
        onYouTubeIframeAPIReady?: Function,
        dataLayer?: any
    }
}