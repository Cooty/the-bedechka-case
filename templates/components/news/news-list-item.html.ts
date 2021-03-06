import INewsItem from "./INewsItem";
import sanitizeHTML from "../../../assets/ts/utils/sanitize-html";

// The HTML should match the HTML in the file
// ./news-list-item.html.twig in the <li class="news__item">
// the outer element is created in a loop and we use HTMLFragments to add the inside elements
// that's why the differance
const newsListItem = (n: INewsItem) => `
    <a href="${sanitizeHTML(n.link)}" class="box" target="_blank" rel="noreferrer noopener">
        <h3 class="box__subtitle">
            ${sanitizeHTML(n.title)}
        </h3>
        <p class="news-source">
            ${ n.logo ? `<img src="${n.logo}" alt="" class="news-source__img" />` : '' }
            <small class="box__info">${sanitizeHTML(n.source)}</small>
        </p>
    </a>`;

export default newsListItem;