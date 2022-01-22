export default function appendHrefWithHash(anchor: HTMLAnchorElement, hash: string) {
    const oldHref = new URL(anchor.href);
    const newHref = `${oldHref.origin}${oldHref.pathname}${oldHref.search}#${hash}`;
    anchor.setAttribute('href', '');
    anchor.setAttribute('href', newHref);
}
