import YTPlayer from "./player";
import "../../ts/interfaces/WindowGlobals";

/**
 * Entry point for the JS file that initializes the Youtube players
 * Add this file to templates where the YT player is the only
 * JS-related functionality
 * (apart from the global JS functionality like header scrolling, analytics, etc)
 */
class YouTubeEmbed {
    private static instance: YouTubeEmbed;

    private constructor() {}

    public static getInstance(): YouTubeEmbed {
        if (!YouTubeEmbed.instance) {
            YouTubeEmbed.instance = new YouTubeEmbed();
        }

        return YouTubeEmbed.instance;
    }

    public init() {
        new YTPlayer();
    }
}

// Need some additional settings to work with Vagrant file-syncing
// https://github.com/symfony/webpack-encore/issues/191
const app = YouTubeEmbed.getInstance();
Object.freeze(app);

app.init();

export default app;