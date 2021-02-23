import "./_yt-player.scss";
import "./_video-embed.scss";
import "../../../assets/scss/modules/_embed.scss";
import "../../../assets/ts/interfaces/WindowGlobals";

interface IElements {
    body?: HTMLBodyElement,
    videoEmbeds?: HTMLElement[],
    closeButton?: HTMLButtonElement,
    ytIframeContainer?: HTMLElement,
    ytPlayerCloseLayer?: HTMLElement,
    ytPlayerTarget?: HTMLElement
}

/**
 * Manage the playback of youtube videos based on data pre-rendered to the DOM
 * via the YT Data API integration. The static preview is in "./youtube-embed.html.twig"
 * The JS generated from this file is responsible for loading the client-side JS player API
 * and creating the video-embed.
 */
export default class Player {
    private isInitialized: boolean = false;
    private elements: IElements = {};
    private readonly overlayOpenClassName = "yt-player-opened";
    private readonly overlayAnimateClassName = "yt-player-animated";
    private readonly cssTransitionTime = 300;

    constructor() {
        this.init();
    }

    private getElements() {
        const videoEmbeds = Array.from(document.querySelectorAll('.js-video-embed') as NodeListOf<HTMLElement>);
        const body = document.querySelector('body');

        this.elements = {...this.elements, ...{body, videoEmbeds}};
    }

    private static loadYTIFrameAPI() {
        const tag = document.createElement('script');

        tag.src = "https://www.youtube.com/iframe_api";
        const firstScriptTag = document.getElementsByTagName("script")[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
    }

    private onYouTubeIframeAPIReady() {
        this.addEventHandlers();
        this.isInitialized = true;
    }

    private static makePlayerTarget() {
        return document.createElement("div");
    }

    private addPlayerTarget(ytPlayerTarget: HTMLElement, parent: HTMLElement) {
        parent.appendChild(ytPlayerTarget);
        this.elements = {...this.elements, ...{ytPlayerTarget}};

        return this.elements;
    }

    private makeAndAddPlayerOverlay() {
        const ytPlayer = document.createElement("article");
        ytPlayer.className = "yt-player";

        const ytPlayerContainer = document.createElement("div");
        ytPlayerContainer.className = "yt-player__player-container";

        const closeButton = document.createElement("button");
        closeButton.type = "button";
        closeButton.className = "yt-player__close";
        closeButton.innerText = "×";
        closeButton.addEventListener("click", ()=> {this.closeOverlay();});

        const ytIframeContainer = document.createElement("div");
        ytIframeContainer.className = "embed-16-9";

        const ytPlayerTarget = Player.makePlayerTarget();
        this.addPlayerTarget(ytPlayerTarget, ytIframeContainer);

        const ytPlayerCloseLayer = document.createElement("div");
        ytPlayerCloseLayer.className = "yt-player__closer-layer";
        ytPlayerCloseLayer.addEventListener("click", ()=> {this.closeOverlay();});

        ytPlayerContainer.appendChild(closeButton);
        ytPlayerContainer.appendChild(ytIframeContainer);

        ytPlayer.appendChild(ytPlayerCloseLayer);
        ytPlayer.appendChild(ytPlayerContainer);

        this.elements.body.appendChild(ytPlayer);

        this.elements = {...this.elements, ...{closeButton, ytIframeContainer, ytPlayerCloseLayer}};
    }

    private makePlayer(id: string) {
        return new window.YT.Player(this.elements.ytPlayerTarget, {
            host: "https://www.youtube-nocookie.com", // doesn't add tracking cookies
            videoId: id,
            // @see https://developers.google.com/youtube/player_parameters.html?playerVersion=HTML5#Parameters
            playerVars: {
                autoplay: 1, // start immediately
                hl: document.documentElement.getAttribute("lang"), // set player UI's language to site's language
                modestbranding: 1, // removes the YT logo
                playsinline: 1, // play as inline player on iOS
                rel: 0, // related videos at the end will only come from our channel
                cc_load_policy: 1 // start with captions
            }
        });
    }

    private showOverlay() {
        this.elements.body.classList.add(this.overlayOpenClassName);
        const timeOut = window.setTimeout(()=> {
            this.elements.body.classList.add(this.overlayAnimateClassName);
            return window.clearTimeout(timeOut);
        },1);
    }

    private cleanUpBeforeClosing() {
        this.elements.ytIframeContainer.innerHTML = "";
        delete this.elements.ytPlayerTarget;
    }

    private closeOverlay() {
        this.elements.body.classList.remove(this.overlayAnimateClassName);
        const timeOut = window.setTimeout(()=> {
            this.elements.body.classList.remove(this.overlayOpenClassName);
            this.cleanUpBeforeClosing();
            return window.clearTimeout(timeOut);
        }, this.cssTransitionTime);
    }

    private addEventHandlers() {
        this.elements.videoEmbeds.forEach(videoEmbed => {
            videoEmbed.addEventListener("click", (e)=> {
                e.preventDefault();
                if(!this.elements.ytIframeContainer) {
                    this.makeAndAddPlayerOverlay();
                }

                if(!this.elements.ytPlayerTarget) {
                    const ytPlayerTarget = Player.makePlayerTarget();
                    this.addPlayerTarget(ytPlayerTarget, this.elements.ytIframeContainer);
                }

                this.makePlayer(videoEmbed.dataset.id);
                this.showOverlay();
            });
        });
    }

    public init() {
        if(!this.isInitialized) {
            window.onYouTubeIframeAPIReady = this.onYouTubeIframeAPIReady.bind(this);
            this.getElements();
            Player.loadYTIFrameAPI();
        }
    }
}
