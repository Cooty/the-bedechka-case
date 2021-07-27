import INewsItem from "./INewsItem";
import sanitizeHTML from "../../ts/utils/sanitize-html";

const newsListItem = (n: INewsItem) => `
    <a href="${sanitizeHTML(n.link)}" class="box" target="_blank" rel="noreferrer noopener">
        ${ n.image ? `
            <figure class="box__image-container">
                <div class="embed-16-9">
                    <img src="${n.image}"
                         alt="${n.title} - ${n.source}"
                         class="box__image" />
                </div>
            </figure>
                ` : 
            '' }
        <h3 class="box__subtitle">
            ${sanitizeHTML(n.title)}
        </h3>
        <p class="news-source">
            <small class="box__info">${sanitizeHTML(n.source)}</small>
            
            ${ n.publishingDate ? `
                    <small class="box__info">${sanitizeHTML(n.publishingDate)}</small>
                ` 
                    :
                ''
            }
        </p>
    </a>`;

export default newsListItem;