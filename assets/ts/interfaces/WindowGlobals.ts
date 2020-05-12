import IConfig from "./IConfig";

export {};

declare global {
    interface Window {
        _config?: IConfig,
        L?: any,
        YT?: any,
        onYouTubeIframeAPIReady?: Function
    }
}