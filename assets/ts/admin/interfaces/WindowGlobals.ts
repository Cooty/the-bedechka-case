import {IConfig} from "./IConfig";

export {};

declare global {
    interface Window {
        _config?: IConfig
    }
}