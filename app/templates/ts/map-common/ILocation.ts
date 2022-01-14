import ICoords from "./ICoords";

export default interface ILocation {
    name: string,
    description: string,
    image: string,
    id: string,
    coords: ICoords,
    link?: string
}