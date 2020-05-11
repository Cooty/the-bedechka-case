import "../../scss/modules/_yt-player.scss";
import {Browser} from "leaflet";
import win = Browser.win;

interface IElements {
    body?: HTMLBodyElement,
    videoEmbeds?: Element[],
    closeButton?: HTMLButtonElement,
    ytIframeContainer?: HTMLElement,
    ytPlayerCloseLayer?: HTMLElement
}

export default class YtPlayer {
    private isInitialized: boolean = false;
    private elements: IElements = {};
    private readonly overlayOpenClassName = "yt-player-opened";
    private readonly overlayAnimateClassName = "yt-player-animated";
    private readonly cssTransitionTime = 300;

    constructor() {
        this.init();
    }

    private getElements() {
        const videoEmbeds = Array.from(document.querySelectorAll('.js-video-embed'));
        const body = document.querySelector('body');

        this.elements = {...this.elements, ...{body, videoEmbeds}};
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

        const ytPlayerCloseLayer = document.createElement("div");
        ytPlayerCloseLayer.className = "yt-player__closer-layer";
        ytPlayerCloseLayer.addEventListener("click", ()=> {this.closeOverlay();});

        ytPlayerContainer.appendChild(closeButton);
        ytPlayerContainer.appendChild(ytIframeContainer);

        ytPlayer.appendChild(ytPlayerCloseLayer);
        ytPlayer.appendChild(ytPlayerContainer);

        this.elements.body.appendChild(ytPlayer);

        this.elements = {...this.elements, ...{closeButton, ytIframeContainer, ...ytPlayerCloseLayer}};
    }

    private showOverlay() {
        this.elements.body.classList.add(this.overlayOpenClassName);
        const timeOut = window.setTimeout(()=> {
            this.elements.body.classList.add(this.overlayAnimateClassName);
            return window.clearTimeout(timeOut);
        },1);
    }

    private closeOverlay() {
        this.elements.body.classList.remove(this.overlayAnimateClassName);
        const timeOut = window.setTimeout(()=> {
            this.elements.body.classList.remove(this.overlayOpenClassName);
            this.elements.ytIframeContainer.innerHTML = "";
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

                this.showOverlay();
            });
        });
    }

    public init() {
        if(!this.isInitialized) {
            this.getElements();
            this.addEventHandlers();
            this.isInitialized = true;
        }
    }
}
