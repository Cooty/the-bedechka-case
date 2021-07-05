import INewsItem from "./INewsItem";
import sanitizeHTML from "../../ts/utils/sanitize-html";

// The HTML should match the HTML in the file
// ./news-list-item.html.twig in the <li class="news__item">
// the outer element is created in a loop and we use HTMLFragments to add the inside elements
// that's why the differance
// Also the `<img>` tag doesn't need `data-src` attribute and `js-lazy-loading` classname
// since these items are added via JS after page load we don't need lazy-loading on the images
// they are already intrinsically lazy loaded by appending the new DOM-elements
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
        </p>
    </a>`;

export default newsListItem;