import INewsItem from "../interfaces/INewsItem";
import sanitizeHTML from "../utils/sanitize-html";

const newsItemInside = (n: INewsItem) => `
    <a href="${sanitizeHTML(n.link)}" class="box" target="_blank" rel="noreferrer noopener">
        <h3 class="box__subtitle">
            ${sanitizeHTML(n.title)}
        </h3>
        <small class="box__info">${sanitizeHTML(n.source)}</small>
    </a>`;

export default newsItemInside;