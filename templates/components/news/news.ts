import "./_news.scss";

import INewsItems from "./INewsItems";
import {getNetworkErrorMessage} from "../../../assets/ts/utils/error-handling";
import template from "./news-list-item.html";
import "../../../assets/ts/interfaces/WindowGlobals";

export default class News {
    private currentPage: number;
    private readonly button: HTMLButtonElement;
    private readonly buttonContainer: HTMLElement;
    private readonly list: HTMLElement;

    constructor(button: HTMLButtonElement) {
        if (!button) {
            return;
        }
        this.button = button;
        this.currentPage = 1; // the 0th page is already rendered to the template from server-side
        this.buttonContainer = document.getElementById("js-load-more-container");
        this.list = document.getElementById("js-news-list");
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

    private removeButtonContainer() {
        this.list.removeChild(this.buttonContainer);
    }

    private async buttonListener() {
        try {
            this.button.disabled = true;
            await this.getNewsItems()
                .then((newsItems: INewsItems) => {
                    return newsItems
                }).then((newsItems: INewsItems)=> {
                    this.addItems(this.makeNewsItems(newsItems));
                    this.currentPage++;
                    if(this.isLastPage(newsItems.total)) {
                        this.removeButtonContainer();
                    } else {
                        this.button.disabled = false;
                    }
                });
        } catch (e) {
            News.handleError(e);
        }
    }

    private static handleError(e: Error) {
        alert(window._config.genericErrorMessage);
        console.error(e);
    }

    public init() {
        this.button.addEventListener("click", this.buttonListener.bind(this));
    }

}