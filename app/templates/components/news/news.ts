import "./_news.scss";

import INewsItems from "./INewsItems";
import {getNetworkErrorMessage} from "../../ts/utils/error-handling";
import template from "./news-list-item.html";
import "../../ts/interfaces/WindowGlobals";
import debounce from "../../ts/utils/debounce";
import Viewport from "../../ts/utils/viewport";

export default class News {
    private currentPage: number;
    private readonly button: HTMLButtonElement;
    private readonly buttonContainer: HTMLElement;
    private readonly list: HTMLElement;
    private readonly container: HTMLElement;
    private isFirstPageLoaded: boolean;
    private isContainerReached: boolean;

    constructor(button: HTMLButtonElement) {
        if (!button) {
            return;
        }
        this.button = button;
        this.currentPage = 0; // the 0th page is already rendered to the template from server-side
        this.buttonContainer = document.getElementById("js-load-more-container");
        this.list = document.getElementById("js-news-list");
        this.container = document.getElementById("js-news-section-container");
        this.isFirstPageLoaded = false;
        this.isContainerReached = false;
        this.init();
    }

    private async getNewsItems(): Promise<INewsItems> {
        const {
            newsApiUrl,
            newsItemsPerPage
        } = window._config;

        const offset = this.currentPage * newsItemsPerPage;

        const newsAPIURL: RequestInfo = `${newsApiUrl}?pageSize=${newsItemsPerPage}&offset=${offset}`;
        const response = await fetch(newsAPIURL);

        if(response.status !== 200) {
            throw new Error(getNetworkErrorMessage(newsAPIURL, response.status));
        } else {
            return await response.json();
        }
    }

    private makeNewsItems(newsItems: INewsItems): string[] {
        return newsItems.items.map(n => {
            return template(n);
        });
    }

    private addItems(html: string[]) {
        if(!html.length)
            return;

        const fragment = new DocumentFragment();

        html.forEach((el)=> {
            const li = document.createElement("li");
            li.className = "news__item";
            li.innerHTML = el;

            fragment.appendChild(li);
        });

        this.list.insertBefore(fragment, this.buttonContainer);
    }

    private isLastPage(total: number) {
        return this.currentPage * window._config.newsItemsPerPage >= total;
    }

    private isNoItems(total: number) {
        return total === 0;
    }

    private static remove(el: HTMLElement) {
        el.parentNode.removeChild(el)
    }

    private async load() {
        try {
            this.button.disabled = true;
            const newsItems = await this.getNewsItems();
            this.addItems(this.makeNewsItems(newsItems));
            this.currentPage++;

            if(this.isLastPage(newsItems.total)) {
                News.remove(this.buttonContainer);

                if(this.isNoItems(newsItems.total)) {
                    News.remove(this.container);
                }
            }
        } catch (e) {
            News.handleError(e);
        } finally {
            if(this.button) {
                this.button.disabled = false;
            }
        }
    }

    private static handleError(e: Error) {
        alert(window._config.genericErrorMessage);
        console.error(e);
    }

    private loadFirstPage() {
        this.load().then(() => {
            this.isFirstPageLoaded = true;
        });
    }

    private scrollHandler() {
        if(
            !this.isContainerReached &&
            Viewport.isInViewport(this.container, 300)
        ) {
            this.isContainerReached = true;
            this.loadFirstPage();
        }
    }

    public init() {
        window.addEventListener(
            "scroll",
            debounce(this.scrollHandler.bind(this), 10),
            false
        );
        this.button.addEventListener("click", this.load.bind(this));
    }

}
