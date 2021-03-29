export const getNetworkErrorMessage = (url: string, statusCode: number)=> {
    return `The URL: ${url} responded with ${statusCode}`;
};