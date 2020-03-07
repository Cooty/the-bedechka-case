export function setCookie(cname: string, cvalue: string, exdays: number = null)
{
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    const expires = exdays ? `expires=${d.toUTCString()}` : "";

    document.cookie = `${cname}=${cvalue};${expires};path=/`;
}