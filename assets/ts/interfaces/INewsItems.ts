import INewsItem from "./INewsItem";

export default interface INewsItems {
    items: INewsItem[],
    total: number
}