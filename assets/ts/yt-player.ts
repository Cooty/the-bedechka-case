import "../scss/modules/_video-embed.scss";
import "../scss/modules/_embed.scss";

import YTPlayer from "./modules/yt-player";
import "./interfaces/WindowGlobals";

class YT {
    private static instance: YT;

    private constructor() {}

    public static getInstance(): YT {
        if (!YT.instance) {
            YT.instance = new YT();
        }

        return YT.instance;
    }

    public init() {
        new YTPlayer();
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = YT.getInstance();
Object.freeze(app);

app.init();

export default app;